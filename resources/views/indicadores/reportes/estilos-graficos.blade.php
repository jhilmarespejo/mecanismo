{{-- 
    Archivo: resources/views/indicadores/reportes/estilos-graficos.blade.php
    Descripción: Estilos CSS para los gráficos y contenedores
--}}

<style>
    /* Estilos para comboboxes */
    .custom-select {
        padding: 0.625rem 0.875rem;
        border-radius: 8px;
        border: 2px solid #e0e6ed;
        transition: all 0.3s ease;
    }
    
    .custom-select:focus {
        border-color: #4b6cb7;
        box-shadow: 0 0 0 0.2rem rgba(75, 108, 183, 0.25);
    }
    
    .select-card {
        transition: transform 0.2s ease;
    }
    
    .select-card:hover {
        transform: translateY(-2px);
    }

    /* Estilos para contenedores de gráficos */
    .container.row {
        display: flex;
        align-items: stretch;
    }

    .indicadores, .parametros {
        display: flex;
        flex-direction: column;
    }
    
    .card {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease;
    }
    
    .card:hover {
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    }

    .grafico-container {
        flex-grow: 1;
        min-height: 400px;
    }

    /* Controles para gráficos animados */
    .controls {
        text-align: center;
        padding: 15px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px;
        margin: 15px 0;
        border: 1px solid #dee2e6;
    }

    .controls button {
        margin: 0 10px;
        padding: 8px 16px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    
    .controls button:hover {
        transform: translateY(-1px);
    }
    
    .controls input[type="range"] {
        vertical-align: middle;
        margin: 0 15px;
        width: 250px;
    }

    /* Estilos para diferentes tipos de gráficos */
    /* .grafico-numeral {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    }
    
    .grafico-delitos {
        background: linear-gradient(135deg, #fff3e0 0%, #ffcc80 100%);
    }
    
    .grafico-departamentos {
        background: linear-gradient(135deg, #e8f5e8 0%, #a5d6a7 100%);
    }
    
    .grafico-sexo {
        background: linear-gradient(135deg, #fce4ec 0%, #f8bbd9 100%);
    }
    
    .grafico-centros {
        background: linear-gradient(135deg, #f3e5f5 0%, #ce93d8 100%);
    } */

    /* Responsive */
    @media (max-width: 768px) {
        .custom-select option {
            max-width: 300px;
        }
        
        .select-card {
            padding: 1rem;
        }
        
        .controls input[type="range"] {
            width: 180px;
        }
    }

    /* Indicadores de carga */
    .loading-indicator {
        display: none;
        text-align: center;
        padding: 20px;
        color: #6c757d;
    }

    .loading-indicator.show {
        display: block;
    }

    /* Estilos para mensajes de sin datos */
    .no-data-message {
        text-align: center;
        padding: 40px;
        color: #6c757d;
        font-style: italic;
    }

    .no-data-message i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #dee2e6;
    }

    /* Animaciones */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .chart-container {
        animation: fadeIn 0.5s ease-out;
    }
    
    /* Títulos de gráficos */
    /* .chart-title {
        background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
        color: white;
        padding: 15px;
        border-radius: 8px 8px 0 0;
        margin: 0;
        font-weight: 600;
        text-align: center;
    } */

    /* .chart-subtitle {
        padding: 10px 15px;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        font-size: 0.9em;
        color: #6c757d;
    } */
</style>