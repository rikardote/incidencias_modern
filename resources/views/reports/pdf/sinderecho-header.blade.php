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

<div class="banner">REPORTE SIN DERECHO A NOTA BUENA POR DESEMPEÑO</div>

<table class="info-table">
    <tr>
        <td style="width: 50%;">
            <span class="label">CENTRO DE TRABAJO: </span><span class="value">{{ $department->code }} - {{ $department->description }}</span>
        </td>
        <td style="width: 50%; text-align: right;">
            <span class="label">PERIODO: </span><span class="value">{{ $monthName }} {{ $year }}</span>
        </td>
    </tr>
</table>

<div class="divider"></div>
