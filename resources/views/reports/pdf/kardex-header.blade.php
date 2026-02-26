<div class="header-container">
    <table style="width: 100%;">
        <tr>
            <td class="logo-box">
                <img src="{{ public_path('images/60issste.png') }}" class="logo-img">
            </td>
            <td class="title-box">
                <div class="title-main">
                    REPRESENTACION ESTATAL BAJA CALIFORNIA<br>
                    SUBDELEGACION DE ADMINISTRACION<br>
                    DEPARTAMENTO DE RECURSOS HUMANOS
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="banner">K A R D E X</div>

<table class="info-table">
    <tr>
        <td style="width: 50%;">
            <span class="label">NUM EMPLEADO: </span><span class="value">{{ $empleado->num_empleado }}</span>
        </td>
        <td style="width: 50%; text-align: right;">
            <span class="label">NOMBRE: </span><span class="value">{{ $empleado->name }} {{ $empleado->father_lastname }} {{ $empleado->mother_lastname }}</span>
        </td>
    </tr>
    <tr>
        <td style="width: 50%;">
            <span class="label">FECHA DE INGRESO: </span><span class="value">@if($empleado->fecha_ingreso){{ \Carbon\Carbon::parse($empleado->fecha_ingreso)->format('d/m/Y') }}@else N/A @endif</span>
        </td>
        <td style="width: 50%; text-align: right;">
            <span class="label">TURNO: </span><span class="value">{{ $empleado->jornada->jornada ?? 'N/A' }}</span> - <span class="label">HORARIO: </span><span class="value">{{ $empleado->horario->horario ?? 'N/A' }}</span>
        </td>
    </tr>
    <tr>
        <td style="width: 50%;">
            <span class="label">CLAVE DE ADSCRIPCION: </span><span class="value">{{ ($empleado->department->code == "00104") ? "00105" : ($empleado->department->code ?? 'N/A') }}</span>
        </td>
        <td style="width: 50%; text-align: right;">
            <span class="label">DESCRIPCION: </span><span class="value">{{ $empleado->department->description ?? 'N/A' }}</span>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: right; padding-top: 10px;">
            <span class="label">RANGO DE FECHAS: </span><span class="value">{{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} AL {{ \Carbon\Carbon::parse($fecha_final)->format('d/m/Y') }}</span>
        </td>
    </tr>
</table>

<div class="divider"></div>
