<?php
class Usuarios extends Controller {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // SEGURIDAD EXTRICTA: SOLO ROOT
        if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'root') {
            header('Location: ' . URLROOT . '/Dashboard'); // Los saca al dashboard normal
            exit;
        }

        // Datos Dummy Usuarios
        $usuarios = [
            ['usuario' => 'admin', 'nombre' => 'Admin General', 'rol' => 'root', 'ultimo_acceso' => 'Hace 2 min'],
            ['usuario' => 'supervisor', 'nombre' => 'Jefe de Campo', 'rol' => 'supervisor', 'ultimo_acceso' => 'Ayer 14:00'],
            ['usuario' => 'encuestador1', 'nombre' => 'Juan Tecnico', 'rol' => 'encuestador', 'ultimo_acceso' => 'Hoy 09:30'],
            ['usuario' => 'encuestador2', 'nombre' => 'Ana Campo', 'rol' => 'encuestador', 'ultimo_acceso' => 'Hoy 10:15'],
        ];

        $datos = ['lista' => $usuarios];
        $this->view('usuarios/index', $datos);
    }
}