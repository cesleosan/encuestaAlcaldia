<?php
class Productores extends Controller {
    
    public function index() {
        // 1. Validar sesión
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) { 
            header('Location: ' . URLROOT . '/Auth'); 
            exit; 
        }

        // 2. DATOS DUMMIES (Simulando una base de datos)
        $productores = [
            [
                'folio' => 'AGR-001', 
                'nombre' => 'Juan Pérez López', 
                'pueblo' => 'San Miguel Topilejo', 
                'actividad' => 'Agrícola (Maíz)', 
                'fecha' => '12/Feb/2026',
                'estatus' => 'Completa'
            ],
            [
                'folio' => 'PEC-045', 
                'nombre' => 'María González R.', 
                'pueblo' => 'Parres El Guarda', 
                'actividad' => 'Pecuaria (Ovinos)', 
                'fecha' => '12/Feb/2026',
                'estatus' => 'Pendiente'
            ],
            [
                'folio' => 'TRA-102', 
                'nombre' => 'Pedro Hernández', 
                'pueblo' => 'San Andrés Totoltepec', 
                'actividad' => 'Transformación (Miel)', 
                'fecha' => '11/Feb/2026',
                'estatus' => 'Completa'
            ],
            [
                'folio' => 'HUE-008', 
                'nombre' => 'Ana Sofía Castro', 
                'pueblo' => 'San Miguel Ajusco', 
                'actividad' => 'Huerto (Hortalizas)', 
                'fecha' => '11/Feb/2026',
                'estatus' => 'Nueva'
            ],
            [
                'folio' => 'AGR-099', 
                'nombre' => 'Roberto Díaz', 
                'pueblo' => 'Magdalena Petlacalco', 
                'actividad' => 'Agrícola (Avena)', 
                'fecha' => '10/Feb/2026',
                'estatus' => 'Completa'
            ],
            [
                'folio' => 'GRA-012', 
                'nombre' => 'Luisa Martínez', 
                'pueblo' => 'San Pedro Mártir', 
                'actividad' => 'Granja (Conejos)', 
                'fecha' => '10/Feb/2026',
                'estatus' => 'Revisión'
            ]
        ];

        // 3. Empaquetar datos para la vista
        $datos = [
            'titulo' => 'Padrón de Productores',
            'lista' => $productores
        ];

        // 4. Cargar vista
        $this->view('productores/index', $datos);
    }
}