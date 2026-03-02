<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 8pt; color: #333; margin: 0; padding: 0; }
        .employee-record { margin-bottom: 6px; page-break-inside: avoid; }
        .emp-info-row { 
            background-color: #f8fafc;
            border: 0.5pt solid #cbd5e1;
            padding: 3px 6px;
            font-weight: bold;
            font-size: 8pt;
            display: block;
            margin-bottom: 0px;
            border-bottom: none;
        }
        .horario-text { float: right; font-weight: normal; font-size: 7.5pt; color: #475569; }
        
        table.attendance-grid { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: fixed; /* Fuerza el mismo tamaño para todas las celdas */
            margin-bottom: 4px;
        }
        table.attendance-grid th, table.attendance-grid td { 
            border: 0.5pt solid #cbd5e1; 
            padding: 2px 0; 
            text-align: center; 
            vertical-align: middle;
            overflow: hidden;
        }
        table.attendance-grid th { 
            background: #f1f5f9; 
            font-size: 6.5pt; 
            font-weight: bold;
            height: 14px;
        }
        table.attendance-grid td { 
            font-size: 7.4pt; 
            height: 20px; 
            color: #111;
            font-weight: normal;
        }
        
        .weekend { background-color: #f1f5f9; color: #94a3b8; }
        .empty { color: #e2e8f0; font-size: 4pt; }
        
        @php 
            $numDays = count($daterange);
            $cellWidth = 100 / $numDays;
        @endphp
        
        .col-day { width: {{ $cellWidth }}%; }
    </style>
</head>
<body>
    @foreach($empleados as $num_empleado => $registrosEmpleado)
        @php $first = $registrosEmpleado->first(); @endphp
        <div class="employee-record">
            <table style="width: 100%; border: 0.5pt solid #cbd5e1; border-bottom: none; background-color: #f8fafc; margin-bottom: 0px;">
                <tr>
                    <td style="text-align: left; font-weight: bold; font-size: 8pt; padding: 3px 6px; border: none;">
                        {{ $num_empleado }} - {{ strtoupper($first->apellido_paterno) }} {{ strtoupper($first->apellido_materno) }} {{ strtoupper($first->nombre) }}
                    </td>
                    <td style="text-align: right; font-weight: normal; font-size: 7.5pt; color: #475569; padding: 3px 6px; border: none;">
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

                                $entradaDisp = '';
                                $salidaDisp = '';
                                
                                if ($reg) {
                                    $horarioEntrada = strtotime($first->horario_entrada);
                                    $horarioSalida = strtotime($first->horario_salida);
                                    $esJornadaVespertina = $first->es_jornada_vespertina == 1;
                                    $horaMedia = $esJornadaVespertina ? $horarioEntrada + (($horarioSalida - $horarioEntrada) / 2) : strtotime('12:00:00');
                                    $hE = $reg->hora_entrada ? strtotime($reg->hora_entrada) : null;
                                    
                                    if ($hE && $hE > $horaMedia) {
                                        $entradaDisp = 'OMIT'; 
                                        $salidaDisp = substr($reg->hora_salida, 0, 5);
                                    } else {
                                        $entradaDisp = $reg->hora_entrada ? substr($reg->hora_entrada, 0, 5) : '';
                                        $salidaDisp = ($reg->hora_salida && $reg->hora_salida !== $reg->hora_entrada) ? substr($reg->hora_salida, 0, 5) : '';
                                    }
                                }
                            @endphp
                            <td class="{{ $isWeekend ? 'weekend' : '' }}">
                                @if($reg)
                                    @if($entradaDisp === 'OMIT')
                                        <span>OMIT</span>
                                    @elseif($entradaDisp)
                                        <span>{{ $entradaDisp }}</span>@if($salidaDisp)-{{ $salidaDisp }}@endif
                                    @else
                                        <span class="empty">--</span>
                                    @endif
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
</body>
</html>
