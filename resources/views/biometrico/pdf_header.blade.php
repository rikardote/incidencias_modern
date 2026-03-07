<div class="header-container" style="font-family: 'Helvetica', sans-serif;">
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 5px;">
        <tr>
            <td style="width: 35%; vertical-align: middle;">
                <img src="{{ public_path('images/60issste.png') }}" style="width: 220px;">
            </td>
            <td style="width: 65%; text-align: right; vertical-align: middle;">
                <div style="font-size: 8pt; font-weight: bold; color: #333; text-transform: uppercase; line-height: 1.2;">
                    Representación Estatal Baja California<br>
                    Subdelegación de Administración<br>
                    Departamento de Recursos Humanos
                </div>
            </td>
        </tr>
    </table>

    <div style="border: 1.5pt solid #13322B; color: #13322B; text-align: center; padding: 4px; font-weight: bold; font-size: 10pt; margin-top: 5px; text-transform: uppercase; letter-spacing: 1.5px; border-radius: 2px;">
        CONTROL DE ASISTENCIA BIOMÉTRICO
    </div>

    <table style="width: 100%; margin-top: 6px; border-collapse: collapse;">
        <tr>
            <td style="width: 60%; vertical-align: bottom;">
                <div style="font-size: 7.5pt; color: #64748b; font-weight: bold; text-transform: uppercase;">Adscripción:</div>
                <div style="font-size: 9pt; font-weight: bold; color: #1e293b; text-transform: uppercase;">
                    [{{ str_pad($dpto->code, 5, "0", STR_PAD_LEFT) }}] {{ $dpto->description }}
                </div>
            </td>
            <td style="width: 40%; text-align: right; vertical-align: bottom;">
                <div style="font-size: 7.5pt; color: #64748b; font-weight: bold; text-transform: uppercase;">Periodo de Control:</div>
                <div style="font-size: 9pt; font-weight: bold; color: #1e293b;">
                    QNA {{ str_pad($quincena, 2, '0', STR_PAD_LEFT) }}/{{ $año }} 
                    <span style="font-size: 8pt; font-weight: normal; color: #64748b; margin-left: 4px;">
                        ({{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }})
                    </span>
                </div>
            </td>
        </tr>
    </table>
    
    <div style="border-bottom: 2pt solid #13322B; margin-top: 5px; margin-bottom: 5px; opacity: 0.8;"></div>
</div>
