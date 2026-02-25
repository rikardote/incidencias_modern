<div class="header-container">
    <table style="width: 100%;">
        <tr>
            <td class="logo-box">
                <img src="{{ public_path('images/60issste.png') }}" class="logo-img">
            </td>
            <td class="title-box">
                <div class="title-main">
                    REPRESENTACION ESTATAL BAJA CALIFORNIA<br>
                    CALIFORNIA<br>
                    SUBDELEGACION DE ADMINISTRACION<br>
                    DEPARTAMENTO DE RECURSOS HUMANOS
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="banner">REPORTE DE CONTROL DE ASISTENCIA</div>

<table class="info-table">
    <tr>
        <td style="width: 50%;">
            <span class="label">CLAVE DE ADSCRIPCION: </span><span class="value">{{ $department->code }}</span>
        </td>
        <td style="width: 50%; text-align: right;">
            <span class="label">DESCRIPCION: </span><span class="value">{{ $department->description }}</span>
        </td>
    </tr>
    <tr>
        <td style="width: 50%;">
            <span class="label">QNA: </span><span class="value">{{ str_pad($qna->qna, 2, '0', STR_PAD_LEFT) }}/{{ $qna->year }} - {{ $qna->description }}</span>
        </td>
        <td style="width: 50%; text-align: right;">
            <span class="label">AÑO: </span><span class="value">{{ $qna->year }}</span>
        </td>
    </tr>
</table>

<div class="divider"></div>
