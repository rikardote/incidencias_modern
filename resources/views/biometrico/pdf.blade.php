<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin-top: 45mm;
            header: page-header;
            footer: page-footer;
        }
        body { font-family: 'Helvetica', sans-serif; font-size: 7.5pt; color: #333; margin: 0; padding: 0; }
        
        .header-table { width: 100%; border-bottom: 2pt solid #13322B; padding-bottom: 8px; margin-bottom: 10px; }
        .title-main { font-size: 11pt; font-weight: bold; color: #13322B; text-transform: uppercase; }
        .title-sub { font-size: 8.5pt; color: #9B2247; font-weight: bold; text-transform: uppercase; }

        .employee-record { margin-bottom: 15px; page-break-inside: avoid; }
        .emp-header-table { 
            width: 100%;
            border-collapse: collapse;
            background-color: #f1f5f9;
            border: 0.8pt solid #13322B;
            border-bottom: none;
        }
        .emp-header-td {
            padding: 5px 10px;
            color: #13322B;
            font-weight: bold;
            font-size: 8.5pt;
        }
        
        table.attendance-grid { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed;
            border: 0.8pt solid #13322B;
        }
        table.attendance-grid th, table.attendance-grid td { 
            border: 0.4pt solid #cbd5e1; 
            padding: 1px 0; 
            text-align: center; 
            vertical-align: middle;
            overflow: hidden;
        }
        table.attendance-grid th { 
            background: #f1f5f9; 
            font-size: 6.5pt; 
            font-weight: bold;
            height: 14px;
            text-transform: uppercase;
        }
        table.attendance-grid td { 
            font-size: 5.5pt; 
            height: 24px; 
            color: #111;
        }
        
        .weekend { background-color: #f8fafc; color: #94a3b8; }
        .empty { color: #e2e8f0; font-size: 4pt; }
        .retardo { color: #ef4444; font-weight: bold; }
        .inc-code { font-size: 6pt; font-weight: 900; color: #9B2247; display: block; margin-bottom: 1px; }
        .time-box { display: block; line-height: 1; }
        
        @php 
            $numDays = count($daterange);
            $cellWidth = 100 / $numDays;
        @endphp
        
        .col-day { width: {{ $cellWidth }}%; }
    </style>
</head>
<body>
    <htmlpageheader name="page-header">
        @include('biometrico.pdf_header')
    </htmlpageheader>

    @foreach($empleados as $num_empleado => $registrosEmpleado)
        @php $first = $registrosEmpleado->first(); @endphp
        <div class="employee-record">
            <table class="emp-header-table">
                <tr>
                    <td class="emp-header-td" style="text-align: left;">
                        {{ $num_empleado }} - {{ strtoupper($first->apellido_paterno) }} {{ strtoupper($first->apellido_materno) }} {{ strtoupper($first->nombre) }}
                        @if($first->lactancia)<span style="border: 0.5pt solid #10b981; color: #10b981; padding: 1px 4px; border-radius: 2px; font-size: 6pt; margin-left: 5px;">LACTANCIA</span>@endif
                        @if($first->estancia)<span style="border: 0.5pt solid #3b82f6; color: #3b82f6; padding: 1px 4px; border-radius: 2px; font-size: 6pt; margin-left: 5px;">ESTANCIA</span>@endif
                        @if($first->exento)<span style="border: 0.5pt solid #d4af37; color: #b48e1b; padding: 1px 4px; border-radius: 2px; font-size: 6pt; margin-left: 5px;">EXENTO</span>@endif
                    </td>
                    <td class="emp-header-td" style="text-align: right; color: #475569; font-size: 7.5pt;">
                        HORARIO: {{ $first->horario_entrada ? substr($first->horario_entrada, 0, 5) . '-' . substr($first->horario_salida, 0, 5) : $first->horario }}
                    </td>
                </tr>
            </table>
            <table class="attendance-grid">
                <thead>
                    <tr>
                        @foreach($daterange as $date)
                            <th class="col-day {{ $date->isWeekend() ? 'weekend' : '' }}">
                                {{ strtoupper(substr($date->translatedFormat('D'), 0, 1)) }}{{ $date->format('d') }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach($daterange as $date)
                            @php 
                                $fechaStr = $date->format('Y-m-d');
                                $reg = $registrosEmpleado->firstWhere('fecha', $fechaStr);
                                $isWeekend = $date->isWeekend();

                                if ($reg) {
                                    $horarioEntrada = strtotime($first->horario_entrada);
                                    $horarioSalida = strtotime($first->horario_salida);
                                    $esJornadaVespertina = $first->es_jornada_vespertina == 1;
                                    $horaMedia = $esJornadaVespertina ? $horarioEntrada + (($horarioSalida - $horarioEntrada) / 2) : strtotime('12:00:00');
                                    $hE = $reg->hora_entrada ? strtotime($reg->hora_entrada) : null;
                                    
                                    $entradaDisp = '';
                                    $salidaDisp = '';
                                    if ($hE && $hE > $horaMedia) {
                                        $entradaDisp = 'OMIT'; 
                                        $salidaDisp = $reg->hora_salida ? substr($reg->hora_salida, 0, 5) : '';
                                    } else {
                                        $entradaDisp = $reg->hora_entrada ? substr($reg->hora_entrada, 0, 5) : '';
                                        $salidaDisp = ($reg->hora_salida && $reg->hora_salida !== $reg->hora_entrada) ? substr($reg->hora_salida, 0, 5) : '';
                                    }
                                }
                            @endphp
                            <td class="{{ $isWeekend ? 'weekend' : '' }}">
                                @if($reg)
                                    <div class="time-box">
                                        @if($entradaDisp === 'OMIT')
                                            <span style="color: #ef4444; font-weight: bold;">OMIT</span>
                                            @if($salidaDisp)<br>{{ $salidaDisp }}@endif
                                        @elseif($entradaDisp)
                                            <span class="{{ $reg->retardo ? 'retardo' : '' }}">{{ $entradaDisp }}</span>
                                            @if($salidaDisp)<br>{{ $salidaDisp }}@endif
                                        @else
                                            <span class="empty">--</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="empty">--</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach

    <htmlpagefooter name="page-footer">
        <div style="text-align: right; font-size: 7pt; color: #94a3b8; border-top: 0.5pt solid #cbd5e1; padding-top: 4px;">
            Generado el {{ date('d/m/Y H:i') }} | Página {PAGENO} de {nb}
        </div>
    </htmlpagefooter>
</body>
</html>
