<?php
class Dashboard extends Controller {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Seguridad
        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] === 'encuestador') {
            header('Location: ' . URLROOT . '/Auth');
            exit;
        }

        // --- DATOS SIMULADOS BASADOS EN TUS PREGUNTAS ---
        
        $stats = [
            // KPI Cards (Tarjetas de arriba)
            'kpis' => [
                'total' => 1250,
                'productores' => 980,
                'hectareas' => 450.5,
                'avance' => 68
            ],

            // GRÁFICA 1: TIPO DE PRODUCCIÓN (Pregunta 18)
            'grafica_produccion' => [
                'labels' => ['Agrícola', 'Pecuaria', 'Huertos', 'Granjas', 'Transformación'],
                'data' => [450, 300, 200, 150, 150]
            ],

            // GRÁFICA 2: PRINCIPALES PROBLEMÁTICAS (Pregunta 11)
            'grafica_problemas' => [
                'labels' => ['Falta de Agua', 'Altos Costos', 'Plagas', 'Mano de Obra', 'Clima'],
                'data' => [500, 350, 200, 100, 80]
            ],

            // GRÁFICA 3: DISTRIBUCIÓN POR PUEBLO (Pregunta 5)
            'grafica_pueblos' => [
                'labels' => ['Topilejo', 'Ajusco', 'Parres', 'Totoltepec', 'Xicalco'],
                'data' => [300, 250, 200, 150, 100]
            ],

            // GRÁFICA 4: GÉNERO (Pregunta 2)
            'grafica_sexo' => [
                'labels' => ['Mujeres', 'Hombres'],
                'data' => [680, 570]
            ]
        ];

        $datos = [
            'titulo' => 'Tablero de Control',
            'stats' => $stats
        ];

        $this->view('dashboard/index', $datos);
    }
}