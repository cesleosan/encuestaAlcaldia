<?php
class Usuario {
    private $db;

    public function __construct() {
        // Instancia la clase Database de tu núcleo MVC
        $this->db = new Database;
    }

    /**
     * Busca un usuario por su nombre de usuario.
     * Útil para la nueva lógica de login con password_verify.
     */
    public function obtenerUsuarioPorNombre($usuario) {
        // Buscamos al usuario que esté activo
        $this->db->query('SELECT * FROM usuarios WHERE usuario = :usuario AND activo = 1');
        $this->db->bind(':usuario', $usuario);
        
        // Retorna el objeto usuario (incluyendo el hash en $row->password)
        return $this->db->single();
    }

    /**
     * Función para validar credenciales (Versión Segura con Hash)
     * Se mantiene por compatibilidad si prefieres validar dentro del modelo.
     */
    public function login($usuario, $password) {
        // 1. Buscar el usuario por su nombre de usuario
        $row = $this->obtenerUsuarioPorNombre($usuario);

        if ($row) {
            // 2. Verificar el hash de la contraseña
            // Compara el texto plano ingresado contra el hash de la BD
            if (password_verify($password, $row->password)) {
                return $row; // Éxito: Retornamos los datos del técnico
            }
        }

        return false; // Error: Usuario no existe, inactivo o contraseña mal
    }

    /**
     * Obtener datos por ID (Necesario para mantener la sesión activa)
     */
    public function obtenerUsuarioPorId($id) {
        $this->db->query('SELECT * FROM usuarios WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
}