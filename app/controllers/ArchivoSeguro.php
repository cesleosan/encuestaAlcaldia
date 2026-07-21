<?php

class ArchivoSeguro extends Controller {
    private $encuestaModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!defined('PUBROOT')) {
            define('PUBROOT', dirname(APPROOT) . '/public');
        }

        $this->encuestaModel = $this->model('EncuestaModelo');
    }

    public function evidencia($id) {
        if (!$this->haySesion()) return $this->denegar(401, 'Sesion expirada.');
        if (!is_numeric($id)) return $this->denegar(400, 'Archivo invalido.');

        $evidencia = $this->encuestaModel->getEvidenciaCompletaById((int)$id);
        if (!$evidencia) return $this->denegar(404, 'Archivo no encontrado.');

        $rol = $_SESSION['rol'] ?? '';
        $tipo = strtoupper((string)($evidencia->tipo_evidencia ?? ''));

        if ($rol === 'consulta') {
            if (($evidencia->fase_proceso ?? '') !== 'COMITE') {
                return $this->denegar(404, 'Archivo no disponible para Comite.');
            }

            if (!in_array($tipo, ['VERIFICACION_CAMPO', 'FORMATOS_TECNICOS'], true)) {
                return $this->denegar(403, 'Documento protegido para el perfil Comite.');
            }
        } elseif (!in_array($rol, ['root', 'supervisor', 'capturista', 'admin'], true)) {
            return $this->denegar(403, 'Archivo no autorizado para este perfil.');
        }

        return $this->servirRutaRelativa($evidencia->ruta_archivo);
    }

    public function expediente($folio, $archivo) {
        if (!$this->haySesion()) return $this->denegar(401, 'Sesion expirada.');

        $folio = $this->limpiarSegmento($folio);
        $archivo = $this->limpiarSegmento($archivo);
        if ($folio === '' || $archivo === '') return $this->denegar(400, 'Archivo invalido.');

        $rol = $_SESSION['rol'] ?? '';
        if ($rol === 'consulta') {
            return $this->denegar(403, 'Documento protegido para el perfil Comite.');
        }

        if (!in_array($rol, ['root', 'supervisor', 'capturista', 'admin'], true)) {
            return $this->denegar(403, 'Archivo no autorizado para este perfil.');
        }

        return $this->servirRutaRelativa('uploads/expedientes/' . $folio . '/' . $archivo);
    }

    private function haySesion() {
        return !empty($_SESSION['user_id']);
    }

    private function limpiarSegmento($valor) {
        $valor = rawurldecode((string)$valor);
        $valor = str_replace(["\0", '/', '\\'], '', $valor);
        return trim($valor);
    }

    private function servirRutaRelativa($rutaRelativa) {
        $rutaRelativa = str_replace(['\\', "\0"], ['/', ''], (string)$rutaRelativa);
        $rutaRelativa = ltrim($rutaRelativa, '/');

        if (strpos($rutaRelativa, '..') !== false || strpos($rutaRelativa, 'uploads/') !== 0) {
            return $this->denegar(400, 'Ruta invalida.');
        }

        $base = realpath(PUBROOT . '/uploads');
        $archivo = realpath(PUBROOT . '/' . $rutaRelativa);

        if (!$base || !$archivo || strpos($archivo, $base) !== 0 || !is_file($archivo)) {
            return $this->denegar(404, 'Archivo no encontrado.');
        }

        $mime = function_exists('mime_content_type') ? mime_content_type($archivo) : 'application/octet-stream';
        if (!$mime) $mime = 'application/octet-stream';

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($archivo));
        header('Content-Disposition: inline; filename="' . basename($archivo) . '"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store, max-age=0');
        readfile($archivo);
        exit;
    }

    private function denegar($codigo, $mensaje) {
        http_response_code($codigo);
        header('Content-Type: text/plain; charset=utf-8');
        echo $mensaje;
        exit;
    }
}
