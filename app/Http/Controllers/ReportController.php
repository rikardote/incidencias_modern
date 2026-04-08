<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Incidencia;
use App\Models\Qna;
use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Mpdf\Mpdf;

class ReportController extends Controller
{
    public function rh5Pdf($qnaId, $departmentId)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '300');

        $user = auth()->user();
        if (!$user->admin()) {
            $hasAccess = $user->departments()->where('deparment_id', $departmentId)->exists();
            if (!$hasAccess) {
                abort(403, 'No tienes permiso para ver el reporte de este departamento.');
            }
        }

        $qna = Qna::findOrFail($qnaId);
        $department = Department::findOrFail($departmentId);

        $incidencias = Incidencia::with(['employee', 'codigo', 'periodo'])
            ->select('incidencias.*')
            ->join('employees', 'employees.id', '=', 'incidencias.employee_id')
            ->where('incidencias.qna_id', $qnaId)
            ->where('employees.deparment_id', $departmentId)
            ->whereHas('codigo', function ($q) {
                $q->whereNotIn('code', [902, 903, 904]);
            })
            ->orderBy('employees.num_empleado')
            ->orderBy('incidencias.id')
            ->get();

        $numeros = $incidencias->pluck('employee.num_empleado')->unique()->filter()->toArray();
        if (!empty($numeros)) {
            app(\App\Services\Employees\EmployeeApiService::class)->preloadEmployeesData($numeros);
        }

        $incidencias = $incidencias->groupBy('token');

        $mpdf = new Mpdf([
            'orientation' => 'P',
            'format' => 'Letter',
            'margin_left' => 12.7,
            'margin_right' => 12.7,
            'margin_top' => 14,
            'margin_bottom' => 12.7,
            'margin_header' => 8,
            'margin_footer' => 8,
            'tempDir' => storage_path('app/mpdf')
        ]);

        // Auto-calculate margins to prevent header/footer overlap
        $mpdf->setAutoTopMargin = 'stretch';
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->simpleTables = false;
        $mpdf->packTableData = true;

        $css = '
            body { font-family: "Helvetica", "Arial", sans-serif; color: #333; font-size: 9pt; }
            .header-container { width: 100%; margin-bottom: 4px; }
            .logo-box { width: 40%; }
            .logo-img { width: 280px; }
            .title-box { width: 60%; text-align: right; vertical-align: middle; }
            .title-main { font-size: 10pt; font-weight: bold; line-height: 1.3; color: #444; }
            
            .banner { 
                background-color: #4b5563; 
                color: #fff;
                padding: 4px; 
                margin: 6px 0;
                font-size: 8pt;
                text-align: center;
                text-transform: uppercase;
                letter-spacing: 1px;
                font-weight: bold;
            }
            
            .info-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 9pt; }
            .info-table td { padding: 3px 0; }
            .label { font-weight: bold; color: #6b7280; }
            .value { font-weight: bold; text-transform: uppercase; color: #111; }
            
            .divider { border-top: 1.5pt solid #9ca3af; margin-top: 2px; margin-bottom: 8px; }

            .content-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; table-layout: fixed; }
            .content-table thead tr { background-color: #f3f4f6; }
            .content-table th { 
                color: #374151; 
                padding: 6px 4px; 
                border-top: 1pt solid #d1d5db; 
                border-bottom: 1pt solid #d1d5db; 
                font-weight: bold; 
                text-align: left;
                text-transform: uppercase;
                font-size: 8pt;
            }
            .content-table td { 
                padding: 4px 4px; 
                vertical-align: top; 
                color: #111;
                word-wrap: break-word;
            }
            
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .font-bold { font-weight: bold; }
            .uppercase { text-transform: uppercase; }
            
            .group-divider td {
                border-top: 0.8pt solid #9ca3af;
                padding-top: 6px;
            }

            .comment-text {  
                font-size: 7.5pt; 
                font-weight: normal; 
                text-transform: uppercase; 
                display: block; 
                margin-top: 2px; 
                color: #6b7280; 
            }
        ';

        // Convertir logo a base64 para que mPDF no lo lea del disco en cada página
        $logoPath = public_path('images/60issste.png');
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));

        $headerHtml = view('reports.pdf.rh5-header', compact('department', 'qna', 'logoBase64'))->render();

        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->setHTMLHeader($headerHtml);
        $mpdf->SetFooter('Página {PAGENO} de {nb}');

        // Optimización: Escribir la tabla por partes para evitar límites de PCRE backtrack
        $tableHeader = '<table class="content-table" style="overflow: wrap;">
            <thead>
                <tr>
                    <th style="width: 10%;">Num Empleado</th>
                    <th style="width: 35%;">Empleado</th>
                    <th style="width: 8%;">Codigo</th>
                    <th style="width: 12%;">Fecha Inicial</th>
                    <th style="width: 12%;">Fecha Final</th>
                    <th style="width: 13%;">Periodo</th>
                    <th style="width: 10%; text-align: center;">Total</th>
                </tr>
            </thead>
            <tbody>';
        
        $mpdf->WriteHTML($tableHeader);

        // Optimización: Generar HTML directamente en PHP (sin Blade ni Carbon::parse por fila)
        $lastEmp = null;
        $rowsHtml = '';
        $rowCount = 0;

        foreach ($incidencias as $token => $group) {
            $firstInGroup = $group->first();
            $empId = $firstInGroup->employee->num_empleado;
            $totalDias = $group->sum('total_dias');
            $isNewEmp = ($empId !== $lastEmp);

            $dividerClass = ($isNewEmp && $lastEmp !== null) ? 'group-divider' : '';
            $empDisplay = $isNewEmp ? $empId : '';

            // Columna empleado
            $empCol = '';
            if ($isNewEmp) {
                $empCol .= '<div style="font-size: 10pt; margin-bottom: 2px;">'
                    . e($firstInGroup->employee->father_lastname) . ' '
                    . e($firstInGroup->employee->mother_lastname) . ' '
                    . e($firstInGroup->employee->name) . '</div>';
            }
            if ($firstInGroup->otorgado) {
                $empCol .= '<div class="comment-text">' . e($firstInGroup->otorgado) . '</div>';
            }
            if ($firstInGroup->becas_comments) {
                $empCol .= '<div class="comment-text">' . e($firstInGroup->becas_comments) . '</div>';
            }
            if ($firstInGroup->horas_otorgadas) {
                $empCol .= '<div class="comment-text">' . e($firstInGroup->horas_otorgadas) . '</div>';
            }
            if ($firstInGroup->codigo->code == 900 && $firstInGroup->cobertura_txt) {
                $empCol .= '<div class="comment-text">LABORÓ: ' . e($firstInGroup->cobertura_txt) . '</div>';
            }
            if ($firstInGroup->codigo->code == 61 && $firstInGroup->motivo_comision) {
                $empCol .= '<div class="comment-text">MOTIVO: ' . e($firstInGroup->motivo_comision) . '</div>';
            }

            // Código display
            $code = $firstInGroup->codigo->code;
            if ($code == 901) $codeDisplay = 'OT';
            elseif ($code == 905) $codeDisplay = 'PS';
            elseif ($code == 900) $codeDisplay = 'TXT';
            else $codeDisplay = str_pad($code, 2, '0', STR_PAD_LEFT);

            // Fechas (date directo, sin Carbon::parse)
            $fechaInicio = date('d/m/Y', strtotime($firstInGroup->fecha_inicio));
            $fechaFinal = date('d/m/Y', strtotime($firstInGroup->fecha_final));

            // Periodo
            $periodo = $firstInGroup->periodo
                ? $firstInGroup->periodo->periodo . '/' . $firstInGroup->periodo->year
                : '-';

            $rowsHtml .= '<tr class="' . $dividerClass . '">'
                . '<td class="text-center">' . $empDisplay . '</td>'
                . '<td>' . $empCol . '</td>'
                . '<td class="text-center">' . $codeDisplay . '</td>'
                . '<td class="text-center">' . $fechaInicio . '</td>'
                . '<td class="text-center">' . $fechaFinal . '</td>'
                . '<td class="text-center">' . $periodo . '</td>'
                . '<td class="text-center">' . $totalDias . '</td>'
                . '</tr>';

            $lastEmp = $empId;
            $rowCount++;

            // Escribir al PDF cada 200 filas para no acumular demasiado HTML en memoria
            if ($rowCount % 200 === 0) {
                $mpdf->WriteHTML($rowsHtml);
                $rowsHtml = '';
            }
        }

        // Escribir filas restantes
        if ($rowsHtml !== '') {
            $mpdf->WriteHTML($rowsHtml);
        }

        $mpdf->WriteHTML('</tbody></table>');

        $pdfFileName = str_pad($qna->qna, 2, '0', STR_PAD_LEFT) . '-' . $qna->year . '-' . mb_strtoupper($department->description) . '.RH5.PDF';

        $pdfContent = $mpdf->Output('', 'S');

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $pdfFileName . '"'
        ]);
    }

    public function sinDerechoPdf(Request $request, $year, $month, $departmentId)
    {
        // 1. Validar acceso
        $user = auth()->user();
        if (!$user->admin()) {
            $hasAccess = $user->departments()->where('deparment_id', $departmentId)->exists();
            if (!$hasAccess) {
                abort(403, 'No tienes permiso para ver el reporte de este departamento.');
            }
        }

        $department = Department::findOrFail($departmentId);

        // 2. Ejecutar la misma consulta del Livewire
        $dt = Carbon::create($year, $month, 1, 12, 0, 0);
        $fecha_inicio = $dt->copy()->startOfMonth()->format('Y-m-d');
        $fecha_final = $dt->copy()->endOfMonth()->format('Y-m-d');

        $months = [
            1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL',
            5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO',
            9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE'
        ];
        $monthName = $months[(int)$month] ?? '';

        $lic = ['40', '41', '46', '47', '53', '54', '55'];
        $inc = ['01', '02', '03', '04', '08', '09', '10', '18', '19', '25', '30', '31', '78', '86', '100'];

        $licIds = DB::table('codigos_de_incidencias')->whereIn('code', $lic)->pluck('id')->toArray();
        $incIds = DB::table('codigos_de_incidencias')->whereIn('code', $inc)->pluck('id')->toArray();

        // OPTIMIZACIÓN: Filtrar empleados del departamento primero (evita JOIN lento en incidencias)
        $targetEmployeeIds = DB::table('employees')
            ->where('deparment_id', $departmentId)
            ->where('condicion_id', 1)
            ->pluck('id')
            ->toArray();

        if (empty($targetEmployeeIds)) {
            return back()->with('error', 'No hay empleados para este reporte');
        }

        // Obtener incidencias en una sola pasada
        $incidencias = DB::table('incidencias')
            ->select('employee_id', 'codigodeincidencia_id', 'total_dias')
            ->whereNull('deleted_at')
            ->whereIn('employee_id', $targetEmployeeIds)
            ->whereIn('codigodeincidencia_id', array_merge($incIds, $licIds))
            ->whereBetween('fecha_inicio', [$fecha_inicio, $fecha_final])
            ->get()
            ->groupBy('employee_id');

        $employeeIdsToReport = [];
        foreach ($incidencias as $empId => $items) {
            $hasCritical = false;
            $sumLic = 0;
            foreach ($items as $item) {
                if (in_array($item->codigodeincidencia_id, $incIds)) {
                    $hasCritical = true;
                    break;
                }
                if (in_array($item->codigodeincidencia_id, $licIds)) {
                    $sumLic += $item->total_dias;
                }
            }
            if ($hasCritical || $sumLic > 3) {
                $employeeIdsToReport[] = $empId;
            }
        }

        $results = Employe::with(['puesto', 'horario', 'jornada'])
            ->whereIn('id', $employeeIdsToReport)
            ->orderBy('num_empleado')
            ->get();

        $numerosSD = $results->pluck('num_empleado')->unique()->filter()->toArray();
        if (!empty($numerosSD)) {
            app(\App\Services\Employees\EmployeeApiService::class)->preloadEmployeesData($numerosSD);
        }

        // 3. Generar PDF con mPDF
        $tempDir = storage_path('app/mpdf');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'orientation' => 'L',
            'format' => 'Letter',
            'tempDir' => $tempDir,
            'margin_left' => 12.7,
            'margin_right' => 12.7,
            'margin_top' => 14,
            'margin_bottom' => 30, // Espacio para las firmas en el footer
            'margin_header' => 8,
            'margin_footer' => 8,
        ]);

        $mpdf->setAutoTopMargin = 'stretch';
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetAuthor('ISSSTE BAJA CALIFORNIA');
        $mpdf->SetTitle('Reporte Sin Derecho a Nota Buena');

        // Styles
        $css = '
            body { font-family: "Helvetica", "Arial", sans-serif; color: #333; font-size: 9pt; }
            .header-container { width: 100%; margin-bottom: 4px; }
            .logo-box { width: 40%; }
            .logo-img { width: 280px; }
            .title-box { width: 60%; text-align: right; vertical-align: middle; }
            .title-main { font-size: 10pt; font-weight: bold; line-height: 1.3; color: #444; }
            
            .banner { 
                background-color: #4b5563; 
                color: #fff;
                padding: 4px; 
                margin: 6px 0;
                font-size: 8pt;
                text-align: center;
                text-transform: uppercase;
                letter-spacing: 1px;
                font-weight: bold;
            }
            
            .info-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 9pt; }
            .info-table td { padding: 3px 0; }
            .label { font-weight: bold; color: #6b7280; }
            .value { font-weight: bold; text-transform: uppercase; color: #111; }
            
            .divider { border-top: 1.5pt solid #9ca3af; margin-top: 2px; margin-bottom: 8px; }

            .content-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
            .content-table thead tr { background-color: #f3f4f6; }
            .content-table th { 
                color: #374151; 
                padding: 6px 4px; 
                border-top: 1pt solid #d1d5db; 
                border-bottom: 1pt solid #d1d5db; 
                font-weight: bold; 
                text-align: left;
                text-transform: uppercase;
                font-size: 8pt;
            }
            .content-table td { 
                padding: 4px 4px; 
                vertical-align: top; 
                color: #111;
            }
            
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .font-bold { font-weight: bold; }
            .uppercase { text-transform: uppercase; }
            
            .group-divider td {
                border-top: 0.8pt solid #9ca3af;
                padding-top: 6px;
            }

            .comment-text {  
                font-size: 7.5pt; 
                font-weight: normal; 
                text-transform: uppercase; 
                display: block; 
                margin-top: 2px; 
                color: #6b7280; 
            }
        ';

        $headerHtml = view('reports.pdf.sinderecho-header', compact('department', 'monthName', 'year'))->render();
        $contentHtml = view('reports.pdf.sinderecho-content', compact('results', 'department'))->render();
        $footerHtml = view('reports.pdf.sinderecho-footer', compact('department'))->render();

        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->setHTMLHeader($headerHtml);
        $mpdf->setHTMLFooter($footerHtml);
        $mpdf->WriteHTML($contentHtml, \Mpdf\HTMLParserMode::HTML_BODY);

        $pdfFileName = 'SIN_DERECHO_VIATICOS_' . mb_strtoupper($department->description) . '_' . $monthName . '_' . $year . '.pdf';

        return response($mpdf->Output($pdfFileName, 'I'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $pdfFileName . '"'
        ]);
    }

    public function biometricoIndividualPdf(Request $request, $employeeId, $year, $quincena, $quincenaFin = null)
    {
        ini_set('memory_limit', '512M');
        
        $employee = Employe::with(['department', 'puesto', 'horario'])->findOrFail($employeeId);
        
        $user = auth()->guard('web')->user();
        $isEmployee = false;

        if (!$user && auth()->guard('employee')->check()) {
            $user = auth()->guard('employee')->user();
            $isEmployee = true;
        }

        if (!$user) abort(403);

        if ($isEmployee) {
            if ($user->id != $employee->id) abort(403, 'No tiene permiso para ver esta información.');
        } else {
            if (!$user->admin()) {
                if (!$user->departments()->where('deparment_id', $employee->deparment_id)->exists()) {
                    abort(403);
                }
            }
        }

        $qStart = (int)min($quincena, $quincenaFin ?? $quincena);
        $qEnd = (int)max($quincena, $quincenaFin ?? $quincena);

        $mesStart = ceil($qStart / 2);
        $es_primera_start = ($qStart % 2) != 0;
        $inicio = $es_primera_start ? "{$year}-" . str_pad($mesStart, 2, '0', STR_PAD_LEFT) . "-01" : "{$year}-" . str_pad($mesStart, 2, '0', STR_PAD_LEFT) . "-16";

        $mesEnd = ceil($qEnd / 2);
        $es_primera_end = ($qEnd % 2) != 0;
        $fin = $es_primera_end ? "{$year}-" . str_pad($mesEnd, 2, '0', STR_PAD_LEFT) . "-15" : "{$year}-" . str_pad($mesEnd, 2, '0', STR_PAD_LEFT) . "-" . date('t', strtotime("{$year}-{$mesEnd}-01"));

        $checadaModel = new \App\Models\Checada();
        $results = $checadaModel->obtenerRegistrosPorEmpleado($employee->id, $inicio, $fin);
        
        $daterange = [];
        $current = Carbon::parse($inicio);
        $end = Carbon::parse($fin);
        while ($current <= $end) {
            $daterange[] = $current->copy();
            $current->addDay();
        }

        $mpdf = new Mpdf([
            'format' => 'Letter',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 60,
            'margin_bottom' => 20,
            'tempDir' => storage_path('app/mpdf')
        ]);

        $mpdf->setAutoTopMargin = 'stretch';
        $mpdf->setAutoBottomMargin = 'stretch';

        $mpdf->SetTitle('Reporte Biométrico Individual - ' . $employee->num_empleado);
        
        $logoPath = public_path('images/60issste.png');
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));

        $viewData = [
            'employee' => $employee,
            'results' => $results,
            'daterange' => $daterange,
            'year' => $year,
            'qStart' => $qStart,
            'qEnd' => $qEnd,
            'logo' => $logoBase64
        ];

        $html = view('reports.pdf.biometrico-individual', $viewData)->render();
        
        $mpdf->WriteHTML($html);

        return $mpdf->Output('Biometrico_' . $employee->num_empleado . '.pdf', 'I');
    }

    public function kardexPdf($num_empleado, $fecha_inicio, $fecha_final)
    {
        ini_set('memory_limit', '512M');
        
        $num_empleado = str_pad($num_empleado, 6, '0', STR_PAD_LEFT);
        $empleado = Employe::with(['department', 'puesto', 'horario', 'jornada'])->where('num_empleado', $num_empleado)->firstOrFail();
        
        $user = auth()->guard('web')->user();
        $isEmployee = false;

        if (!$user && auth()->guard('employee')->check()) {
            $user = auth()->guard('employee')->user();
            $isEmployee = true;
        }

        if (!$user) abort(403);

        if ($isEmployee) {
            if ($user->num_empleado !== $num_empleado) {
                abort(403, 'No tienes permiso para ver esta información.');
            }
        } else {
            if (!$user->admin()) {
                if (!$user->departments()->where('deparment_id', $empleado->deparment_id)->exists()) {
                    abort(403, 'No tienes permiso para ver el reporte de este empleado.');
                }
            }
        }

        $incidenciasDB = Incidencia::with(['codigo', 'periodo'])
            ->where('employee_id', $empleado->id)
            ->whereBetween('fecha_inicio', [$fecha_inicio, $fecha_final])
            ->get();

        $results = $incidenciasDB->groupBy(function ($inc) {
            return empty($inc->token) ? 'id_' . $inc->id : $inc->token;
        })->map(function ($group) {
            $first = $group->first();
            return (object)[
                'codigo' => $first->codigo,
                'fecha_inicio' => $group->min('fecha_inicio'),
                'fecha_final' => $group->max('fecha_final'),
                'total_dias' => $group->sum('total_dias'),
                'periodo' => $first->periodo,
                'otorgado' => $first->otorgado,
                'horas_otorgadas' => $first->horas_otorgadas,
                'diagnostico' => $first->diagnostico,
                'num_licencia' => $first->num_licencia,
                'cobertura_txt' => $first->cobertura_txt,
            ];
        })->sortBy(function ($item) {
            $code = $item->codigo->code ?? 'ZZ';
            return $code . $item->fecha_inicio;
        })->values();

        $mpdf = new Mpdf([
            'orientation' => 'P',
            'format' => 'Letter',
            'margin_left' => 12.7,
            'margin_right' => 12.7,
            'margin_top' => 14,
            'margin_bottom' => 12.7,
            'margin_header' => 8,
            'margin_footer' => 8,
            'tempDir' => storage_path('app/mpdf')
        ]);

        $mpdf->setAutoTopMargin = 'stretch';
        $mpdf->setAutoBottomMargin = 'stretch';

        $css = '
            body { font-family: "Helvetica", "Arial", sans-serif; color: #333; font-size: 9pt; }
            .header-container { width: 100%; margin-bottom: 4px; }
            .logo-box { width: 40%; }
            .logo-img { width: 280px; }
            .title-box { width: 60%; text-align: right; vertical-align: middle; }
            .title-main { font-size: 10pt; font-weight: bold; line-height: 1.3; color: #444; }
            
            .banner { 
                background-color: #4b5563; 
                color: #fff;
                padding: 4px; 
                margin: 6px 0;
                font-size: 8pt;
                text-align: center;
                text-transform: uppercase;
                letter-spacing: 1px;
                font-weight: bold;
            }
            
            .info-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 9pt; }
            .info-table td { padding: 3px 0; }
            .label { font-weight: bold; color: #6b7280; }
            .value { font-weight: bold; text-transform: uppercase; color: #111; }
            
            .divider { border-top: 1.5pt solid #9ca3af; margin-top: 2px; margin-bottom: 8px; }

            .content-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; table-layout: fixed; }
            .content-table thead tr { background-color: #f3f4f6; }
            .content-table th { 
                color: #374151; 
                padding: 6px 4px; 
                border-top: 1pt solid #d1d5db; 
                border-bottom: 1pt solid #d1d5db; 
                font-weight: bold; 
                text-align: left;
                text-transform: uppercase;
                font-size: 8pt;
            }
            .content-table td { 
                padding: 4px 4px; 
                vertical-align: top; 
                color: #111;
                word-wrap: break-word;
            }
            
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .font-bold { font-weight: bold; }
            .uppercase { text-transform: uppercase; }
            
            .group-divider td {
                border-top: 0.8pt solid #9ca3af;
                padding-top: 6px;
            }
        ';

        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
        
        $headerHtml = view('reports.pdf.kardex-header', compact('empleado', 'fecha_inicio', 'fecha_final'))->render();
        $mpdf->setHTMLHeader($headerHtml);
        $mpdf->SetFooter('Página {PAGENO} de {nb}');

        $contentHtml = view('reports.pdf.kardex-content', compact('results'))->render();
        $mpdf->WriteHTML($contentHtml);

        $pdfFileName = 'KARDEX_' . $num_empleado . '_' . str_replace('-', '', $fecha_inicio) . '_' . str_replace('-', '', $fecha_final) . '.pdf';

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $pdfFileName . '"'
        ]);
    }
}
