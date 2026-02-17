<?php
class Mapa extends Controller {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) { header('Location: ' . URLROOT . '/Auth'); exit; }

        // Puntos simulados en Tlalpan (Topilejo, Ajusco, Parres)
        $puntos = [
            ['lat' => 19.196, 'lng' => -99.143, 'nombre' => 'Huerto Escolar #1', 'tipo' => 'Huerto'],
            ['lat' => 19.205, 'lng' => -99.160, 'nombre' => 'Parcela Maíz - Juan', 'tipo' => 'Agrícola'],
            ['lat' => 19.150, 'lng' => -99.180, 'nombre' => 'Ganado Ovino - Parres', 'tipo' => 'Pecuaria'],
            ['lat' => 19.220, 'lng' => -99.130, 'nombre' => 'Apiario San Miguel', 'tipo' => 'Pecuaria'],
            ['lat' => 19.210, 'lng' => -99.155, 'nombre' => 'Invernadero Rosas', 'tipo' => 'Agrícola'],
        ];

        $datos = ['puntos' => json_encode($puntos)];
        $this->view('mapa/index', $datos);
    }
}