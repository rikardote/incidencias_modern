<?php

namespace App\Http\Controllers;

use App\Models\Checada;
use App\Models\Department;
use App\Models\Qna;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class BiometricoController extends Controller
{
    public function index()
    {
        return view('biometrico.index');
    }

    public function exportar(Request $request)
    {
        $centro = $request->input('centro');
        $año = $request->input('año');
        $quincena = $request->input('quincena');

        if (!$centro || !$año || !$quincena) {
            return back()->with('error', 'Parámetros incompletos.');
        }

        $dpto = Department::findOrFail($centro);
        
        // Calcular fechas
        $mes = ceil($quincena / 2);
        $es_primera_quincena = ($quincena % 2) != 0;

        $fecha_inicio = $es_primera_quincena
            ? "{$año}-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-01"
            : "{$año}-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-16";

        $fecha_fin = $es_primera_quincena
            ? "{$año}-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-15"
            : "{$año}-" . str_pad($mes, 2, '0', STR_PAD_LEFT) . "-" . date('t', strtotime("{$año}-{$mes}-01"));

        $daterange = CarbonPeriod::create($fecha_inicio, $fecha_fin)->toArray();

        $checadaModel = new Checada();
        $registros = $checadaModel->obtenerRegistros($centro, $fecha_inicio, $fecha_fin);
        
        $empleados = $registros->groupBy('num_empleado')->sortBy(fn($g) => intval($g->first()->num_empleado));

        $qnaModel = Qna::where('qna', $quincena)->where('year', $año)->first();
        $qna_description = $qnaModel ? $qnaModel->description : "";
        $pdfFilePath = "Biometrico-{$año}-Q{$quincena}-{$dpto->description}.pdf";

        $html = view('biometrico.pdf', compact('empleados', 'fecha_inicio', 'fecha_fin', 'daterange', 'dpto', 'año', 'quincena', 'qna_description'))->render();

        if (!file_exists(storage_path('app/mpdf'))) {
            mkdir(storage_path('app/mpdf'), 0777, true);
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'Letter',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 60,
            'margin_bottom' => 10,
            'tempDir' => storage_path('app/mpdf'),
        ]);

        $mpdf->SetTitle($pdfFilePath);
        $mpdf->SetAuthor('Sistema de Incidencias');
        
        $mpdf->setAutoTopMargin = 'stretch';
        $header = view('biometrico.pdf_header', compact('dpto', 'año', 'quincena', 'qna_description', 'fecha_inicio', 'fecha_fin'))->render();
        $mpdf->SetHTMLHeader($header);
        $mpdf->SetFooter('Página {PAGENO} de {nb}');

        $mpdf->WriteHTML($html);
        return $mpdf->Output($pdfFilePath, 'I');
    }
}
