<?php
class Usuario {
    private $db;

    public function __construct() {
        // Instancia la clase Database que creamos en el paso anterior
        $this->db = new Database;
    }

    // Función para validar credenciales
    public function login($usuario, $password) {
        // 1. Buscar el usuario por su nombre de usuario (ej. 'admin')
        $this->db->query('SELECT * FROM usuarios WHERE usuario = :usuario');
        $this->db->bind(':usuario', $usuario);
        
        $row = $this->db->single();

        // 2. Verificar contraseña
        // NOTA: Como en tu script SQL pusimos '12345' directo, comparamos texto plano.
        // En producción real, aquí usaríamos: if(password_verify($password, $row->password))
        if ($row) {
            if ($password == $row->password) {
                return $row; // Retornamos el objeto usuario completo (id, rol, nombre...)
            }
        }

        return false; // No existe o contraseña mal
    }

    // Obtener datos por ID (Útil para refrescar sesión)
    public function obtenerUsuarioPorId($id) {
        $this->db->query('SELECT * FROM usuarios WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
}