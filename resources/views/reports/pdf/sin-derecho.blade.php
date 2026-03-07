<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: "Helvetica", "Arial", sans-serif; color: #333; font-size: 9pt; }
        .header-container { width: 100%; margin-bottom: 4px; }
        .logo-box { width: 25%; }
        .logo-img { width: 140px; }
        .title-box { width: 75%; text-align: center; }
        .title-main { font-weight: bold; font-size: 11pt; line-height: 1.4; color: #111; }
        
        .banner { 
            background-color: #4b5563; 
            color: #fff;
            padding: 6px; 
            margin: 10px 0;
            font-size: 9pt;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
        }
        
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 9pt; }
        .info-table td { padding: 4px 0; }
        .label { font-weight: bold; color: #6b7280; }
        .value { font-weight: bold; text-transform: uppercase; color: #111; }
        
        .divider { border-top: 1.5pt solid #9ca3af; margin-top: 2px; margin-bottom: 15px; }

        .content-table { width: 100%; border-collapse: collapse; font-size: 9pt; }
        .content-table th { 
            background-color: #f3f4f6;
            color: #374151; 
            padding: 8px 4px; 
            border-top: 1pt solid #d1d5db; 
            border-bottom: 1pt solid #d1d5db; 
            font-weight: bold; 
            text-align: left;
            text-transform: uppercase;
        }
        .content-table td { 
            padding: 8px 4px; 
            border-bottom: 0.5pt solid #e5e7eb;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .footer {
            position: fixed;
            bottom: 0px;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    @include('reports.pdf.sinderecho-header')
    
    <table class="content-table" width="100%">
        <thead>
            <tr>
                <th width="12%" class="text-center">No. Emp</th>
                <th width="43%">Nombre del Empleado</th>
                <th width="15%" class="text-center">Clave Pto</th>
                <th width="30%">Denominación de Puesto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $emp)
                <tr>
                    <td class="text-center font-bold">{{ $emp->num_empleado }}</td>
                    <td class="uppercase">{{ $emp->father_lastname }} {{ $emp->mother_lastname }} {{ $emp->name }}</td>
                    <td class="text-center">{{ $emp->puesto->clave ?? 'N/A' }}</td>
                    <td class="uppercase">{{ $emp->puesto->puesto ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px; font-style: italic;">
                        No se encontraron empleados sin derecho en este periodo.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generado el {{ date('d/m/Y H:i') }} - Página {PAGENO} de {nb}
    </div>
</body>
</html>
