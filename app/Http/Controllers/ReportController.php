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
            ->where('qna_id', $qnaId)
            ->whereHas('employee', function($q) use ($departmentId) {
                $q->where('deparment_id', $departmentId);
            })
            ->whereNotIn('codigodeincidencia_id', function($q) {
                $q->select('id')->from('codigos_de_incidencias')->whereIn('code', [902, 903, 904]);
            })
            ->get()
            ->groupBy('token') // Usar token para agrupar como en el legacy
            ->sortBy(function($group) {
                return $group->first()->employee->num_empleado;
            });

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

        if (!file_exists(storage_path('app/mpdf'))) {
            mkdir(storage_path('app/mpdf'), 0777, true);
        }

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

        $headerHtml = view('reports.pdf.rh5-header', compact('department', 'qna'))->render();
        $contentHtml = view('reports.pdf.rh5-content', compact('incidencias'))->render();
        
        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->setHTMLHeader($headerHtml);
        $mpdf->SetFooter('Página {PAGENO} de {nb}');
        
        // Removed simpleTables = true so CSS borders are fully respected in table cells

        $mpdf->WriteHTML($contentHtml, \Mpdf\HTMLParserMode::HTML_BODY);
        
        $pdfFileName = 'RH5-' . str_pad($qna->qna, 2, '0', STR_PAD_LEFT) . '-' . $qna->year . '-' . str_replace(' ', '_', $department->description) . '.pdf';

        $pdfContent = $mpdf->Output('', 'S');
        
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $pdfFileName . '"'
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

        $lic = ['40','41','46','47','53','54','55'];
        $inc = ['01','02','03','04','08','09','10','18','19','25','30','31','78','86','100'];

        $licIds = DB::table('codigos_de_incidencias')->whereIn('code', $lic)->pluck('id')->toArray();
        $incIds = DB::table('codigos_de_incidencias')->whereIn('code', $inc)->pluck('id')->toArray();

        $incidenciasUnicas = [];

        if (!empty($incIds)) {
            $queryInc = DB::table('incidencias')
                ->select('employees.id', 'employees.num_empleado')
                ->join('employees', 'employees.id', '=', 'incidencias.employee_id')
                ->whereNull('incidencias.deleted_at')
                ->where('employees.deparment_id', $departmentId)
                ->where('employees.condicion_id', 1) 
                ->whereIn('incidencias.codigodeincidencia_id', $incIds)
                ->whereBetween('incidencias.fecha_inicio', [$fecha_inicio, $fecha_final])
                ->groupBy('employees.id', 'employees.num_empleado')
                ->get();

            foreach ($queryInc as $row) {
                $incidenciasUnicas[$row->num_empleado] = $row->id;
            }
        }

        if (!empty($licIds)) {
            $queryLic = DB::table('incidencias')
                ->select('employees.id', 'employees.num_empleado', DB::raw('SUM(incidencias.total_dias) as count'))
                ->join('employees', 'employees.id', '=', 'incidencias.employee_id')
                ->whereNull('incidencias.deleted_at')
                ->where('employees.deparment_id', $departmentId)
                ->where('employees.condicion_id', 1) 
                ->whereIn('incidencias.codigodeincidencia_id', $licIds)
                ->whereBetween('incidencias.fecha_inicio', [$fecha_inicio, $fecha_final])
                ->groupBy('employees.id', 'employees.num_empleado')
                ->havingRaw('SUM(incidencias.total_dias) > 3')
                ->get();

            foreach ($queryLic as $row) {
                $incidenciasUnicas[$row->num_empleado] = $row->id;
            }
        }

        $employeeIds = array_values($incidenciasUnicas);
        
        $results = Employe::with(['puesto', 'horario', 'jornada'])
            ->whereIn('id', $employeeIds)
            ->orderBy('num_empleado')
            ->get();

        // 3. Generar PDF con mPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'orientation' => 'L',
            'format' => 'Letter',
            'tempDir' => storage_path('app/mpdf'),
            'margin_left' => 12.7,
            'margin_right' => 12.7,
            'margin_top' => 14,
            'margin_bottom' => 30, // Mucho espacio para las firmas en el footer
            'margin_header' => 8,
            'margin_footer' => 8,
        ]);

        $mpdf->setAutoTopMargin = 'stretch';
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetAuthor('SSCDMX');
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
        
        $pdfFileName = 'SIN_DERECHO_' . $monthName . '_' . $year . '_' . str_replace(' ', '_', $department->code) . '.pdf';

        return response($mpdf->Output($pdfFileName, 'I'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $pdfFileName . '"'
        ]);
    }
    public function kardexPdf($num_empleado, $fecha_inicio, $fecha_final)
    {
        $user = auth()->user();
        $empleado = Employe::with(['department', 'puesto', 'horario', 'jornada'])
            ->where('num_empleado', str_pad($num_empleado, 5, '0', STR_PAD_LEFT))
            ->firstOrFail();

        if (!$user->admin()) {
            $hasAccess = $user->departments()->where('deparment_id', $empleado->deparment_id)->exists();
            if (!$hasAccess) {
                abort(403, 'No tienes permiso para ver el kardex de este empleado.');
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
            return $item->codigo->code . $item->fecha_inicio;
        })->values();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'Letter',
            'orientation' => 'P',
            'tempDir' => storage_path('app/mpdf'),
            'margin_left' => 12.7,
            'margin_right' => 12.7,
            'margin_top' => 14,
            'margin_bottom' => 12.7,
            'margin_header' => 8,
            'margin_footer' => 8,
        ]);

        $mpdf->setAutoTopMargin = 'stretch';
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetAuthor('SSCDMX');
        $mpdf->SetTitle('Reporte Kardex');

        // Styles
        $css = '
            body { font-family: "Helvetica", "Arial", sans-serif; color: #333; font-size: 9pt; }
            .header-container { width: 100%; margin-bottom: 4px; }
            .logo-box { width: 40%; }
            .logo-img { width: 280px; }
            .title-box { width: 60%; text-align: right; vertical-align: middle; }
            .title-main { font-size: 10pt; font-weight: bold; line-height: 1.3; color: #444; }
            
            .banner { 
                background-color: #666; 
                color: #fff;
                padding: 4px; 
                margin: 6px 0;
                font-size: 10pt;
                text-align: center;
                text-transform: uppercase;
                letter-spacing: 2px;
                font-weight: bold;
            }
            
            .info-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 9.5pt; }
            .info-table td { padding: 4px 0; }
            .label { font-weight: normal; color: #6b7280; }
            .value { font-weight: bold; text-transform: uppercase; color: #111; }
            
            .divider { border-top: 1.5pt solid #9ca3af; margin-top: 6px; margin-bottom: 8px; }

            .content-table { width: 100%; border-collapse: collapse; font-size: 9.5pt; }
            .content-table thead tr { background-color: rgb(171,165,160); color: #000; }
            .content-table th { 
                padding: 6px 4px; 
                border-top: 1pt solid #d1d5db; 
                border-bottom: 1pt solid #d1d5db; 
                font-weight: bold; 
                text-transform: uppercase;
                font-size: 9pt;
            }
            .content-table td { 
                padding: 5px 4px; 
                vertical-align: top; 
                color: #111;
            }
            
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .font-bold { font-weight: bold; }
            .uppercase { text-transform: uppercase; }
            
            .group-divider td {
                border-top: 0.8pt solid #d1d5db;
                padding-top: 6px;
            }

            .comment-text {  
                font-size: 7.5pt; 
                font-weight: normal; 
                text-transform: uppercase; 
                display: block; 
                color: #6b7280; 
            }
        ';

        $headerHtml = view('reports.pdf.kardex-header', compact('empleado', 'fecha_inicio', 'fecha_final'))->render();
        $contentHtml = view('reports.pdf.kardex-content', compact('results'))->render();
        
        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->setHTMLHeader($headerHtml);
        $mpdf->SetFooter($empleado->name.' '.$empleado->father_lastname.' '.$empleado->mother_lastname.'|Generado el: {DATE d/m/Y} | Página {PAGENO} de {nb}');
        
        $mpdf->WriteHTML($contentHtml, \Mpdf\HTMLParserMode::HTML_BODY);
        
        $pdfFileName = 'Kardex_' . $empleado->num_empleado . '_' . str_replace(' ', '_', $empleado->name . '_' . $empleado->father_lastname) . '.pdf';

        return response($mpdf->Output($pdfFileName, 'I'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $pdfFileName . '"'
        ]);
    }
}
