<div class="header-container" style="font-family: 'Helvetica', sans-serif; position: relative;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 40%; vertical-align: top;">
                <img src="{{ public_path('images/60issste.png') }}" style="width: 250px;">
            </td>
            <td style="width: 60%; text-align: right; vertical-align: top;">
                <div style="font-size: 9pt; font-weight: bold; text-transform: uppercase;">
                    Representación Estatal Baja California<br>
                    Subdelegación de Administración<br>
                    Departamento de Recursos Humanos
                </div>
            </td>
        </tr>
    </table>

    <div style="background-color: #1e293b; color: white; text-align: center; padding: 4px; font-weight: bold; font-size: 10pt; margin-top: 5px; text-transform: uppercase; letter-spacing: 1px;">
        Reporte de Control de Asistencia
    </div>

    <table style="width: 100%; margin-top: 5px; border-collapse: collapse;">
        <tr>
            <td style="width: 20%; padding-bottom: 2px;">
                <span style="font-size: 8pt; color: #64748b; font-weight: bold;">QNA:</span>
                <span style="font-size: 8pt; font-weight: bold;">{{ str_pad($quincena, 2, '0', STR_PAD_LEFT) }}/{{ $año }}</span>
            </td>
            <td style="width: 80%; text-align: right; padding-bottom: 2px;">
                <span style="font-size: 8pt; color: #64748b; font-weight: bold;">DEL:</span>
                <span style="font-size: 8pt; font-weight: bold;">{{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }}</span>
                <span style="font-size: 8pt; color: #64748b; font-weight: bold; margin-left: 5px;">AL:</span>
                <span style="font-size: 8pt; font-weight: bold;">{{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}</span>
            </td>
        </tr>
    </table>
    
    <div style="border-bottom: 0.5pt solid #cbd5e1; margin-top: 2px; margin-bottom: 3px;"></div>

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 15%;">
                <span style="font-size: 8pt; color: #64748b; font-weight: bold;">CLAVE:</span>
                <span style="font-size: 8pt; font-weight: bold;">{{ str_pad($dpto->code, 5, "0", STR_PAD_LEFT) }}</span>
            </td>
            <td style="width: 85%; text-align: right;">
                <span style="font-size: 8pt; color: #64748b; font-weight: bold;">ADSCRIPCIÓN:</span>
                <span style="font-size: 8pt; font-weight: bold; text-transform: uppercase;">{{ $dpto->description }}</span>
            </td>
        </tr>
    </table>

    <div style="border-bottom: 1.5pt solid #1e293b; margin-top: 4px; margin-bottom: 5px;"></div>
</div>
