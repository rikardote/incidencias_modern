<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Incidencia;
use App\Models\Qna;
use Illuminate\Http\Request;
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
            'tempDir' => storage_path('app/tmp')
        ]);

        // Auto-calculate margins to prevent header/footer overlap
        $mpdf->setAutoTopMargin = 'stretch';
        $mpdf->setAutoBottomMargin = 'stretch';

        if (!file_exists(storage_path('app/tmp'))) {
            mkdir(storage_path('app/tmp'), 0775, true);
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
}
