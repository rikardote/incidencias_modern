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

        if (!file_exists(storage_path('app/tmp'))) {
            mkdir(storage_path('app/tmp'), 0775, true);
        }

        $css = '
            @page {
                margin-top: 50mm;
            }
            body { font-family: "Times New Roman", Times, serif; color: #000; font-size: 10pt; }
            .header-container { width: 100%; margin-bottom: 5px; }
            .logo-box { width: 50%; }
            .logo-img { width: 320px; }
            .title-box { width: 50%; text-align: right; vertical-align: middle; }
            .title-main { font-size: 11pt; font-weight: bold; line-height: 1.2; }
            
            .banner { 
                background-color: #666; 
                color: #fff;
                padding: 5px; 
                margin: 5px 0;
                font-size: 9pt;
                text-align: center;
                text-transform: uppercase;
            }
            
            .info-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; font-size: 10pt; }
            .info-table td { padding: 3px 0; }
            .label { font-weight: normal; }
            .value { font-weight: bold; text-transform: uppercase; }
            
            .divider { border-top: 1.5pt solid #666; margin-top: 2px; margin-bottom: 5px; }

            .content-table { width: 100%; border-collapse: collapse; font-size: 10pt; }
            .content-table thead tr { background-color: #999; }
            .content-table th { color: #000; padding: 4px; border: 0.5pt solid #666; font-weight: normal; text-align: left; }
            .content-table td { padding: 5px 4px; border-bottom: 0.3pt solid #ccc; vertical-align: top; }
            
            .text-center { text-align: center !important; }
            .text-right { text-align: right !important; }
            .font-bold { font-weight: bold; }
            .uppercase { text-transform: uppercase; }
            
            .group-divider td { border-top: 1pt solid #000 !important; }
            .comment-text { font-weight: bold; text-transform: uppercase; display: block; margin-top: 10px; }
        ';

        $headerHtml = view('reports.pdf.rh5-header', compact('department', 'qna'))->render();
        $contentHtml = view('reports.pdf.rh5-content', compact('incidencias'))->render();
        
        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->setHTMLHeader($headerHtml);
        $mpdf->SetFooter('Página {PAGENO} de {nb}');
        $mpdf->WriteHTML($contentHtml, \Mpdf\HTMLParserMode::HTML_BODY);
        
        $pdfFileName = 'RH5-' . str_pad($qna->qna, 2, '0', STR_PAD_LEFT) . '-' . $qna->year . '-' . str_replace(' ', '_', $department->description) . '.pdf';

        $pdfContent = $mpdf->Output('', 'S');
        
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $pdfFileName . '"'
        ]);
    }
}
