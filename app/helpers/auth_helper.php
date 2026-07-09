<?php

if (!function_exists('tc_usuario_actual_bd')) {
    function tc_usuario_actual_bd() {
        static $usuarioActual = false;

        if ($usuarioActual !== false) {
            return $usuarioActual;
        }

        $usuarioActual = null;

        if (empty($_SESSION['user_id']) || !class_exists('Database')) {
            return null;
        }

        try {
            $db = new Database();
            $db->query("
                SELECT id, usuario, nombre_completo, rol, modulo, activo
                FROM usuarios
                WHERE id = :id
                  AND activo = 1
                LIMIT 1
            ");
            $db->bind(':id', (int)$_SESSION['user_id']);
            $usuario = $db->single();

            if ($usuario) {
                $_SESSION['usuario'] = $usuario->usuario;
                $_SESSION['rol'] = $usuario->rol;
                $_SESSION['nombre'] = $usuario->nombre_completo;
                $_SESSION['modulo'] = $usuario->modulo;
                $usuarioActual = $usuario;
            }
        } catch (Exception $e) {
            $usuarioActual = null;
        }

        return $usuarioActual;
    }
}

if (!function_exists('tc_normalizar_usuario')) {
    function tc_normalizar_usuario($valor) {
        return strtolower(trim((string)$valor));
    }
}

if (!function_exists('tc_refrescar_sesion_usuario')) {
    function tc_refrescar_sesion_usuario() {
        return tc_usuario_actual_bd();
    }
}

if (!function_exists('tc_es_usuario_aguillen')) {
    function tc_es_usuario_aguillen() {
        $usuario = tc_usuario_actual_bd();
        $nombreUsuario = $usuario->usuario ?? ($_SESSION['usuario'] ?? '');

        return tc_normalizar_usuario($nombreUsuario) === 'aguillen';
    }
}

if (!function_exists('tc_es_root_actual')) {
    function tc_es_root_actual() {
        $usuario = tc_usuario_actual_bd();
        $rol = $usuario->rol ?? ($_SESSION['rol'] ?? '');

        return $rol === 'root';
    }
}

if (!function_exists('tc_es_superusuario_aguillen')) {
    function tc_es_superusuario_aguillen() {
        return tc_es_usuario_aguillen() && tc_es_root_actual();
    }
}

if (!function_exists('tc_puede_ver_accesos_usuarios')) {
    function tc_puede_ver_accesos_usuarios() {
        return tc_es_superusuario_aguillen();
    }
}
