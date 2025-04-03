<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $formulario->FRM_titulo }}</title>
    <style>
        @page {
            margin: 15mm 10mm 15mm 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 7pt;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header {
            position: fixed;
            top: -25mm;
            left: 0;
            right: 0;
            height: 25mm;
            border-bottom: 0.5pt solid #ddd;
            margin-bottom: 15pt;
            padding-bottom: 5pt;
        }
        .header-content {
            display: table;
            width: 100%;
        }
        .logo {
            display: table-cell;
            width: 20%;
            vertical-align: middle;
        }
        .logo img {
            max-height: 20mm;
            max-width: 100%;
        }
        .header-text {
            display: table-cell;
            width: 80%;
            vertical-align: middle;
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
        }
        .title {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            margin: 20pt 0 15pt 0;
            text-transform: uppercase;
            color: #333;
            padding-bottom: 5pt;
            border-bottom: 1pt solid #333;
        }
        .section {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 12pt;
            margin-bottom: 8pt;
            padding-bottom: 2pt;
            border-bottom: 0.5pt solid #999;
            color: #333;
            page-break-inside: avoid;
        }
        .subsection {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 10pt;
            margin-bottom: 6pt;
            margin-left: 15pt;
            color: #555;
            page-break-inside: avoid;
        }
        .question {
            margin: 5pt 0 10pt 20pt;
            padding: 5pt;
            background-color: #f9f9f9;
            border-radius: 3pt;
            page-break-inside: avoid;
        }
        .question-header {
            font-weight: bold;
            margin-bottom: 3pt;
        }
        .options {
            display: flex;
            flex-wrap: wrap;
            margin-top: 3pt;
        }
        .option {
            width: 48%;
            margin-right: 2%;
            margin-bottom: 2pt;
            font-size: 9pt;
        }
        .checkbox {
            display: inline-block;
            width: 9pt;
            height: 9pt;
            border: 0.5pt solid #333;
            margin-right: 3pt;
            vertical-align: middle;
        }
        .radio {
            display: inline-block;
            width: 9pt;
            height: 9pt;
            border-radius: 50%;
            border: 0.5pt solid #333;
            margin-right: 3pt;
            vertical-align: middle;
        }
        .text-input {
            width: 95%;
            height: 15pt;
            border: 0.5pt solid #ccc;
            margin-top: 3pt;
        }
        .number-input {
            width: 60%;
            height: 15pt;
            border: 0.5pt solid #ccc;
            margin-top: 3pt;
        }
        .complement {
            font-style: italic;
            font-size: 9pt;
            color: #666;
            margin-top: 3pt;
            border-top: 0.5pt dotted #ccc;
            padding-top: 3pt;
        }
        .page-break {
            page-break-before: always;
        }
        .footer {
            position: fixed;
            bottom: -15mm;
            left: 0;
            right: 0;
            height: 10mm;
            text-align: center;
            font-size: 8pt;
            color: #666;
            border-top: 0.5pt solid #ddd;
            padding-top: 3pt;
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <img src="{{ isset($encabezado['logo']) ? $encabezado['logo'] : asset('images/logo.png') }}" alt="Logo">
            </div>
            <div class="header-text">
                {{ isset($encabezado['titulo']) ? $encabezado['titulo'] : 'Encabezado principal' }}
            </div>
        </div>
    </div>
    
    <!-- Pie de página -->
    <div class="footer">
        Página [page] de [topage] | Fecha de impresión: {{ date('d/m/Y') }}
    </div>

    <!-- Título del formulario -->
    <div class="title">{{ $formulario->FRM_titulo }}</div>

    <!-- Preguntas -->
    @php
        $seccionActual = null;
        $subseccionActual = null;
        $contadorPreguntas = 0;
    @endphp

    @forelse($preguntas as $pregunta)
        @php
            $esSeccion = str_contains(strtolower($pregunta->BCP_tipoRespuesta), 'sección');
            $esSubSeccion = str_contains(strtolower($pregunta->BCP_tipoRespuesta), 'subsección');
            $opciones = json_decode($pregunta->BCP_opciones, true);
            
            // Actualizar secciones actuales
            if ($esSeccion) {
                $seccionActual = $pregunta->BCP_pregunta;
                $subseccionActual = null;
            } elseif ($esSubSeccion) {
                $subseccionActual = $pregunta->BCP_pregunta;
            } else {
                $contadorPreguntas++;
            }
        @endphp

        <!-- Sección -->
        @if($esSeccion)
            <div class="section">{{ $pregunta->BCP_pregunta }}</div>
        
        <!-- Subsección -->
        @elseif($esSubSeccion)
            <div class="subsection">{{ $pregunta->BCP_pregunta }}</div>
        
        <!-- Pregunta normal -->
        @else
            <div class="question">
                <div class="question-header">{{ $contadorPreguntas }}. {{ preg_replace('/^\d+\.\s*/', '', $pregunta->BCP_pregunta) }}</div>
                
                <!-- Tipo de respuesta y opciones -->
                @if(str_contains(strtolower($pregunta->BCP_tipoRespuesta), 'casilla'))
                    <!-- Tipo casilla de verificación (checkboxes) -->
                    <div class="options">
                        @if($opciones)
                            @foreach($opciones as $opcion)
                                <div class="option">
                                    <span class="checkbox"></span> {{ $opcion }}
                                </div>
                            @endforeach
                        @endif
                    </div>
                
                @elseif(str_contains(strtolower($pregunta->BCP_tipoRespuesta), 'lista'))
                    <!-- Tipo lista desplegable (radio buttons) -->
                    <div class="options">
                        @if($opciones)
                            @foreach($opciones as $opcion)
                                <div class="option">
                                    <span class="radio"></span> {{ $opcion }}
                                </div>
                            @endforeach
                        @endif
                    </div>
                
                @elseif(str_contains(strtolower($pregunta->BCP_tipoRespuesta), 'respuesta corta'))
                    <!-- Tipo texto -->
                    <div class="text-input"></div>
                
                @elseif(str_contains(strtolower($pregunta->BCP_tipoRespuesta), 'numeral'))
                    <!-- Tipo numérico -->
                    <div class="number-input"></div>
                
                @else
                    <!-- Otro tipo de respuesta -->
                    <div class="text-input"></div>
                @endif
                
                <!-- Complemento -->
                @if($pregunta->BCP_complemento)
                    <div class="complement">
                        Por qué?: <div class="text-input"></div>
                    </div>
                @endif
            </div>
        @endif
    @empty
        <p>No hay preguntas registradas para este formulario.</p>
    @endforelse

</body>
</html>