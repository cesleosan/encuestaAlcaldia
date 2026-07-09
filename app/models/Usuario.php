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

    public function registrarInicioSesion($usuarioId, $sessionId, $ip, $userAgent) {
        try {
            $this->db->query("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = :id");
            $this->db->bind(':id', (int)$usuarioId);
            $this->db->execute();

            if (!$this->existeTablaSesiones()) return true;

            $this->db->query("
                INSERT INTO usuario_sesiones
                    (usuario_id, session_id, ip, user_agent, inicio, ultima_actividad, estado)
                VALUES
                    (:usuario_id, :session_id, :ip, :user_agent, NOW(), NOW(), 'activa')
            ");
            $this->db->bind(':usuario_id', (int)$usuarioId);
            $this->db->bind(':session_id', $sessionId);
            $this->db->bind(':ip', $ip);
            $this->db->bind(':user_agent', substr((string)$userAgent, 0, 255));
            return $this->db->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    public function cerrarSesion($usuarioId, $sessionId) {
        try {
            if (!$this->existeTablaSesiones()) return true;

            $this->db->query("
                UPDATE usuario_sesiones
                SET cierre = NOW(), estado = 'cerrada', ultima_actividad = NOW()
                WHERE usuario_id = :usuario_id
                  AND session_id = :session_id
                  AND cierre IS NULL
            ");
            $this->db->bind(':usuario_id', (int)$usuarioId);
            $this->db->bind(':session_id', $sessionId);
            return $this->db->execute();
        } catch (Exception $e) {
            return false;
        }
    }

    public function getMonitoreoAccesos() {
        if ($this->existeTablaSesiones()) {
            try {
                $this->db->query("
                    SELECT
                        u.id,
                        u.usuario,
                        u.nombre_completo,
                        u.rol,
                        u.modulo,
                        u.activo,
                        u.ultimo_acceso,
                        (
                            SELECT us.inicio
                            FROM usuario_sesiones us
                            WHERE us.usuario_id = u.id
                            ORDER BY us.id DESC
                            LIMIT 1
                        ) AS ultimo_inicio,
                        (
                            SELECT us.ultima_actividad
                            FROM usuario_sesiones us
                            WHERE us.usuario_id = u.id
                            ORDER BY us.id DESC
                            LIMIT 1
                        ) AS ultima_actividad,
                        (
                            SELECT us.cierre
                            FROM usuario_sesiones us
                            WHERE us.usuario_id = u.id
                            ORDER BY us.id DESC
                            LIMIT 1
                        ) AS ultimo_cierre,
                        (
                            SELECT us.estado
                            FROM usuario_sesiones us
                            WHERE us.usuario_id = u.id
                            ORDER BY us.id DESC
                            LIMIT 1
                        ) AS estado_sesion,
                        (
                            SELECT us.ip
                            FROM usuario_sesiones us
                            WHERE us.usuario_id = u.id
                            ORDER BY us.id DESC
                            LIMIT 1
                        ) AS ip,
                        (
                            SELECT us.user_agent
                            FROM usuario_sesiones us
                            WHERE us.usuario_id = u.id
                            ORDER BY us.id DESC
                            LIMIT 1
                        ) AS user_agent,
                        (
                            SELECT COUNT(*)
                            FROM usuario_sesiones us
                            WHERE us.usuario_id = u.id
                              AND us.estado = 'activa'
                              AND us.cierre IS NULL
                              AND us.ultima_actividad >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
                        ) AS sesiones_activas
                    FROM usuarios u
                    ORDER BY u.modulo ASC, u.rol ASC, u.nombre_completo ASC
                ");
                return $this->db->resultSet();
            } catch (Exception $e) {
                return $this->getMonitoreoBasico();
            }
        }

        return $this->getMonitoreoBasico();
    }

    private function getMonitoreoBasico() {
        $this->db->query("
            SELECT
                u.id,
                u.usuario,
                u.nombre_completo,
                u.rol,
                u.modulo,
                u.activo,
                u.ultimo_acceso,
                u.ultimo_acceso AS ultimo_inicio,
                u.ultimo_acceso AS ultima_actividad,
                NULL AS ultimo_cierre,
                NULL AS estado_sesion,
                NULL AS ip,
                NULL AS user_agent,
                0 AS sesiones_activas
            FROM usuarios u
            ORDER BY u.modulo ASC, u.rol ASC, u.nombre_completo ASC
        ");
        return $this->db->resultSet();
    }

    public function getResumenAccesos() {
        $usuarios = $this->getMonitoreoAccesos();
        $resumen = [
            'total' => count($usuarios),
            'online' => 0,
            'tierra' => 0,
            'activos' => 0
        ];

        foreach ($usuarios as $usuario) {
            if ((int)($usuario->sesiones_activas ?? 0) > 0) $resumen['online']++;
            if (($usuario->modulo ?? '') === 'TIERRA') $resumen['tierra']++;
            if ((int)($usuario->activo ?? 0) === 1) $resumen['activos']++;
        }

        return $resumen;
    }

    private function existeTablaSesiones() {
        try {
            $this->db->query("SHOW TABLES LIKE 'usuario_sesiones'");
            return (bool)$this->db->single();
        } catch (Exception $e) {
            return false;
        }
    }
}
