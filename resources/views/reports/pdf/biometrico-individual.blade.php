<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Biométrico Individual</title>
    <style>
        @page {
            header: page-header;
            footer: page-footer;
        }
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            font-size: 8pt;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2pt solid #13322B;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo-box { width: 35%; }
        .title-box { 
            width: 65%; 
            text-align: right; 
            vertical-align: middle;
        }
        .title-main { 
            font-size: 11pt; 
            font-weight: bold; 
            color: #13322B; 
            text-transform: uppercase;
            margin: 0;
        }
        .title-sub {
            font-size: 9pt;
            color: #9B2247;
            font-weight: bold;
            text-transform: uppercase;
        }

        .info-container {
            background-color: #f8fafc;
            border: 1pt solid #e2e8f0;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
        }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 4px 6px; }
        .label { font-weight: bold; color: #64748b; text-transform: uppercase; font-size: 7pt; width: 15%; }
        .value { font-weight: bold; color: #1e293b; text-transform: uppercase; font-size: 8.5pt; }

        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .content-table th {
            background-color: #13322B;
            color: white;
            padding: 8px 5px;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            border: 0.5pt solid #13322B;
        }
        .content-table td {
            padding: 6px 5px;
            border: 0.5pt solid #e2e8f0;
            text-align: center;
            font-size: 8pt;
        }
        .day-row:nth-child(even) { background-color: #fdfdfd; }
        .weekend { background-color: #f1f5f9; color: #64748b; }
        
        .badge {
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 7pt;
            display: inline-block;
        }
        .badge-incidencia { background-color: #9B2247; color: white; }
        .badge-retardo { background-color: #ef4444; color: white; }
        .badge-lactancia { background-color: #10b981; color: white; }
        .badge-estancia { background-color: #3b82f6; color: white; }
        .badge-exento { background-color: #d4af37; color: white; }

        .footer {
            text-align: right;
            font-size: 7pt;
            color: #94a3b8;
            border-top: 0.5pt solid #e2e8f0;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    {{-- DEFINICIONES DE ENCABEZADO Y PIE --}}
    <htmlpageheader name="page-header">
        <table class="header-table">
            <tr>
                <td class="logo-box">
                    <img src="{{ $logo }}" style="width: 220px;">
                </td>
                <td class="title-box">
                    <div class="title-main">Control de Asistencia Biométrico</div>
                    <div class="title-sub">Reporte Individual de Asistencia</div>
                    <div style="font-size: 8pt; color: #64748b; margin-top: 4px;">
                        Periodo: QNA {{ str_pad($qStart, 2, '0', STR_PAD_LEFT) }} - {{ str_pad($qEnd, 2, '0', STR_PAD_LEFT) }} / {{ $year }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="info-container" style="margin-top: 5px;">
            <table class="info-table">
                <tr>
                    <td class="label">Empleado:</td>
                    <td class="value" colspan="3">{{ $employee->num_empleado }} - {{ $employee->full_name }}</td>
                </tr>
                <tr>
                    <td class="label">Departamento:</td>
                    <td class="value">{{ $employee->department->description ?? 'N/A' }}</td>
                    <td class="label">Puesto:</td>
                    <td class="value" style="font-size: 7.5pt;">{{ $employee->puesto->puesto ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Horario:</td>
                    <td class="value">
                        {{ $employee->horario->horario ?? 'SIN HORARIO ASIGNADO' }}
                    </td>
                    <td class="label">Generado:</td>
                    <td class="value">{{ date('d/m/Y H:i') }}</td>
                </tr>
            </table>
        </div>
    </htmlpageheader>

    <htmlpagefooter name="page-footer">
        <div class="footer">
            Página {PAGENO} de {nb} | Sistema de Control de Incidencias | ISSSTE
        </div>
    </htmlpagefooter>

    {{-- ACTIVACIÓN DE ENCABEZADO Y PIE --}}
    <sethtmlpageheader name="page-header" value="on" show-this-page="1" />
    <sethtmlpagefooter name="page-footer" value="on" />
    
    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 25%;">Fecha / Día</th>
                <th style="width: 15%;">Entrada</th>
                <th style="width: 15%;">Salida</th>
                <th style="width: 45%;">Incidencias / Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($daterange as $date)
                @php
                    $fechaStr = $date->format('Y-m-d');
                    $reg = $results->firstWhere('fecha', $fechaStr);
                    $isWeekend = $date->isWeekend();
                    $isToday = $date->isToday();
                @endphp
                <tr class="day-row {{ $isWeekend ? 'weekend' : '' }}">
                    <td style="text-align: left; padding-left: 10px;">
                        <div style="font-weight: bold; font-size: 8.5pt;">{{ $date->format('d/m/Y') }}</div>
                        <div style="font-size: 6.5pt; color: #64748b; text-transform: uppercase;">{{ $date->translatedFormat('l') }}</div>
                    </td>
                    <td>
                        @if($reg && $reg->hora_entrada)
                            <span style="{{ $reg->retardo ? 'color: #ef4444; font-weight: bold;' : '' }}">
                                {{ substr($reg->hora_entrada, 0, 5) }}
                            </span>
                        @else
                            <span style="color: #cbd5e1;">--:--</span>
                        @endif
                    </td>
                    <td>
                        @if($reg && $reg->hora_salida && $reg->hora_salida !== $reg->hora_entrada)
                            {{ substr($reg->hora_salida, 0, 5) }}
                        @else
                            <span style="color: #cbd5e1;">--:--</span>
                        @endif
                    </td>
                    <td>
                        <div style="text-align: left; padding-left: 5px;">
                        @if($reg && $reg->incidencias)
                            @foreach(explode(',', $reg->incidencias) as $inc)
                                <span class="badge badge-incidencia">{{ trim($inc) }}</span>
                            @endforeach
                            
                            @if($reg->motivo_comision)
                                <div style="font-size: 6.5pt; font-style: italic; color: #475569; margin-top: 2px;">
                                    {{ $reg->motivo_comision }}
                                </div>
                            @endif

                            @if($reg->otorgado_motivo)
                                <div style="font-size: 6.5pt; font-style: italic; color: #475569; margin-top: 2px;">
                                    {{ $reg->otorgado_motivo }}
                                </div>
                            @endif
                        @elseif($employee->lactancia && $employee->lactancia_inicio && $employee->lactancia_fin && $fechaStr >= $employee->lactancia_inicio && $fechaStr <= $employee->lactancia_fin && !$isWeekend)
                             <span class="badge badge-lactancia">92 (LACTANCIA)</span>
                        @elseif($employee->estancia && $employee->estancia_inicio && $employee->estancia_fin && $fechaStr >= $employee->estancia_inicio && $fechaStr <= $employee->estancia_fin && !$isWeekend)
                             <span class="badge badge-estancia">93 (ESTANCIA)</span>
                        @elseif($employee->exento && !$isWeekend)
                             <span class="badge badge-exento">94 (EXENTO)</span>
                        @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
