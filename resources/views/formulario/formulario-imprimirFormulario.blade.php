<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $formulario->FRM_titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            margin: 20px;
        }
        h1, h2, h3, h4, h5 {
            margin: 10px 0;
        }
        h1 {
            text-align: center;
            font-size: 20px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        h2 {
            font-size: 16px;
            margin-top: 20px;
            text-transform: uppercase;
        }
        h3 {
            font-size: 14px;
            margin-top: 15px;
        }
        p {
            margin: 5px 0;
        }
        .section {
            font-size: 16px;
            font-weight: bold;
            margin-top: 15px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ccc;
        }
        .subsection {
            font-size: 14px;
            font-weight: bold;
            margin-left: 20px;
        }
        .question {
            margin-left: 40px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }
        .complement {
            font-style: italic;
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .options {
            margin-top: 5px;
            margin-left: 10px;
        }
        .option {
            margin: 3px 0;
        }
    </style>
</head>
<body>

<!-- Título -->
<h1>{{ $formulario->FRM_titulo }}</h1>

<!-- Preguntas -->
@forelse($preguntas as $index => $pregunta)
    @php
        $esSeccion = str_contains(strtolower($pregunta->BCP_tipoRespuesta), 'sección');
        $esSubSeccion = str_contains(strtolower($pregunta->BCP_tipoRespuesta), 'subsección');
        $opciones = json_decode($pregunta->BCP_opciones, true);
    @endphp

    <!-- Sección -->
    @if($esSeccion)
        <h2 class="section">{{ $pregunta->BCP_pregunta }}</h2>
    <!-- Subsección -->
    @elseif($esSubSeccion)
        <h3 class="subsection">{{ $pregunta->BCP_pregunta }}</h3>
    <!-- Pregunta -->
    @else
        <div class="question">
            <p><strong>{{ $index + 1 }}. {{ $pregunta->BCP_pregunta }}</strong></p>

            <!-- Complemento -->
            @if($pregunta->BCP_complemento)
                <p class="complement">Complemento: {{ $pregunta->BCP_complemento }}</p>
            @endif

            <!-- Opciones -->
            @if($opciones)
                <div class="options">
                    @foreach($opciones as $key => $opcion)
                        <p class="option">☐ {{ $opcion }}</p>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
@empty
    <p>No hay preguntas registradas para este formulario.</p>
@endforelse

</body>
</html>
