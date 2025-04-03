<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $formulario->FRM_titulo }}</title>
    <style>
        @page {
            margin: 10mm 10mm 10mm 10mm; /* Márgenes reducidos para maximizar espacio */
            size: {{ isset($tamano) && $tamano === 'oficio' ? 'legal' : 'letter' }} portrait;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 6px; /* Tamaño de letra pequeño como solicitado */
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            height: 40px;
            margin-bottom: 8px;
            position: relative;
            border-bottom: 1px solid #ccc;
        }
        .logo {
            position: absolute;
            left: 0;
            top: 0;
            height: 35px;
        }
        .header-text {
            position: absolute;
            left: 60px;
            top: 13px;
            font-weight: bold;
            font-size: 9px;
        }
        h1 {
            text-align: center;
            font-size: 9px;
            margin: 8px 0;
            text-transform: uppercase;
            font-weight: bold;
        }
        h2 {
            font-size: 8px;
            margin: 5px 0;
            text-transform: uppercase;
            page-break-after: avoid;
            border-bottom: 1px solid #ddd;
            padding-bottom: 2px;
        }
        h3 {
            font-size: 7px;
            margin: 4px 0;
            page-break-after: avoid;
            font-weight: bold;
        }
        p {
            margin: 1px 0;
        }
        .section {
            font-weight: bold;
            margin-top: 6px;
            margin-bottom: 3px;
            page-break-after: avoid;
        }
        .subsection {
            font-weight: bold;
            margin-left: 10px;
            margin-top: 4px;
            margin-bottom: 2px;
        }
        .question {
            margin-left: 10px;
            padding: 4px;
            border: 1px solid #eee;
            margin-bottom: 4px;
            page-break-inside: avoid;
        }
        .complement {
            font-style: italic;
            font-size: 9px;
            color: #555;
            margin-top: 1px;
            margin-left: 5px;
        }
        /* Estilo para mostrar opciones en fila con alineación perfecta */
        .option-container {
            margin-top: 2px;
            display: table;
            width: 100%;
        }
        .option-row {
            display: table-row;
        }
        .option {
            display: table-cell;
            padding-right: 10px;
            padding-bottom: 2px;
            white-space: nowrap;
        }
        /* Checkbox estilo CSS para evitar problemas de codificación */
        .checkbox {
            display: inline-block;
            width: 8px;
            height: 8px;
            border: 1px solid #000;
            margin-right: 4px;
            vertical-align: middle;
        }
        .page-break {
            page-break-after: always;
        }
        .question-number {
            display: inline-block;
            min-width: 15px;
        }
        /* Líneas para respuestas */
        .answer-line {
            display: block;
            border-bottom: 1px solid #999;
            margin-top: 20px;
            padding-bottom: 1px;
            width: 100%;
        }
        .number-prefix {
            display: inline-block;
            width: 30px;
            font-weight: bold;
        }
        .complemento {
            display: block;
            font-style: italic;
            font-size: 9px;
            color: #555;
            margin: 1px 0 2px 0;
        }
    </style>
</head>
<body>

<!-- Encabezado -->
<div class="header">
    <img src="img/logo2.png" alt="Defensor del Pueblo" style="width:6%">
    <div class="header-text">Defensoría del Pueblo - Mecanismo de Prevención de la tortura</div>
</div>

<!-- Título -->
<h1>{{ $formulario->FRM_titulo }}</h1>

<!-- Preguntas -->
@forelse($preguntas as $index => $pregunta)
    @php
        $esSeccion = str_contains(strtolower($pregunta->BCP_tipoRespuesta), 'sección');
        $esSubSeccion = str_contains(strtolower($pregunta->BCP_tipoRespuesta), 'subsección');
        $opciones = json_decode($pregunta->BCP_opciones, true);
        $tipoRespuesta = strtolower($pregunta->BCP_tipoRespuesta);
        
        // Contador para opciones
        $opcionCount = is_array($opciones) ? count($opciones) : 0;
        $opcionesPorFila = 3; // Cuántas opciones mostrar por fila
        $filas = $opcionCount > 0 ? ceil($opcionCount / $opcionesPorFila) : 0;
    @endphp

    <!-- Sección -->
    @if($esSeccion)
        <h2 class="section">{{ $index + 1 }}. {{ $pregunta->BCP_pregunta }}</h2>
    <!-- Subsección -->
    @elseif($esSubSeccion)
        <h3 class="subsection">{{ $index + 1 }}. {{ $pregunta->BCP_pregunta }}</h3>
    <!-- Pregunta -->
    @else
        <div class="question">
            <p>
                <span class="number-prefix">{{ $index + 1 }}.</span> 
                <strong>{{ $pregunta->BCP_pregunta }}</strong>
            </p>

            <!-- Opciones en filas alineadas horizontalmente -->
            @if(in_array($tipoRespuesta, ['lista desplegable', 'casilla verificación', 'casilla de verificación', 'multiple opcion']))
                <div class="option-container">
                    @if($opciones && is_array($opciones))
                        @for($fila = 0; $fila < $filas; $fila++)
                            <div class="option-row">
                                @for($col = 0; $col < $opcionesPorFila; $col++)
                                    @php $index = $fila * $opcionesPorFila + $col; @endphp
                                    @if($index < $opcionCount)
                                        <div class="option">
                                            <span class="checkbox"></span> {{ $opciones[$index] }}
                                        </div>
                                    @else
                                        <div class="option"></div>
                                    @endif
                                @endfor
                            </div>
                        @endfor
                    @endif
                </div>
            @elseif(in_array($tipoRespuesta, ['afirmación', 'afirmacion']))
                <div class="option-container">
                    <div class="option-row">
                        <div class="option"><span class="radio"></span> Sí</div>
                        <div class="option"><span class="radio"></span> No</div>
                        {{-- <div class="option"><span class="checkbox"></span> No</div> --}}
                    </div>
                </div>
            @elseif($tipoRespuesta == 'numeral' || $tipoRespuesta == 'respuesta corta')
                <span class="answer-line" ></span>
            @elseif($tipoRespuesta == 'respuesta larga')
                <span class="answer-line"></span>
                <span class="answer-line"></span>
            @endif

             <!-- Complemento -->
             @if($pregunta->BCP_complemento)
             <p class="comp.complemento">{{ $pregunta->BCP_complemento }}</p>
         @endif
        </div>
    @endif
@empty
    <p>No hay preguntas registradas para este formulario.</p>
@endforelse

<!-- Pie de página con numeración -->
<script type="text/php">
    if (isset($pdf)) {
        $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
        $size = 8;
        $font = $fontMetrics->getFont("Arial");
        $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
        $x = ($pdf->get_width() - $width) / 2;
        $y = $pdf->get_height() - 20;
        $pdf->page_text($x, $y, $text, $font, $size);
    }
</script>

</body>
</html>