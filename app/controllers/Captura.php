<?php

class Captura extends Controller {
    private $encuestaModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Seguridad: Solo roles autorizados
        if (!isset($_SESSION['user_id']) || !in_array($_SESSION['rol'], ['root', 'supervisor', 'capturista'])) {
            header('Location: ' . URLROOT . '/Auth');
            exit;
        }
        
        $this->encuestaModel = $this->model('EncuestaModelo');

        if (!defined('PUBROOT')) {
            define('PUBROOT', dirname(APPROOT) . '/public');
        }
    }

    public function index() {
        $this->view('captura/index');
    }

    public function actualizar() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        $id = $_POST['id'];
        
        // 1. Obtener registro actual para conocer el Folio y JSON previo
        $registro = $this->encuestaModel->getEncuestaById($id);
        if (!$registro) {
            echo json_encode(['status' => 'error', 'msg' => 'No se encontró el registro']);
            return;
        }

        $folioCarpeta = str_replace(['/', ' ', '\\'], '-', $registro->folio);
        $rutaDocs = PUBROOT . '/uploads/expedientes/' . $folioCarpeta;
        $rutaEvidencias = PUBROOT . '/uploads/verificaciones/' . $folioCarpeta;
        $lat_verif = filter_var($_POST['latitud_verif'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$lon_verif = filter_var($_POST['longitud_verif'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        // Seguridad de flujo: Capturista/supervisor no pueden enviar APROBADO/RECHAZADO aunque manipulen el HTML.
        $rolSesion = $_SESSION['rol'] ?? '';
        $puedeFaseFinal = in_array($rolSesion, ['root', 'admin'], true);
        $faseSolicitada = $_POST['fase_proceso'] ?? $registro->fase_proceso;
        $fasesBasicas = ['EMPADRONADO', 'SOLICITUD_INGRESADA', 'VALIDACION_DOCS', 'EN_REVISION', 'COMITE'];
        $fasesFinales = ['APROBADO', 'RECHAZADO'];

        if (!$puedeFaseFinal && in_array($faseSolicitada, $fasesFinales, true)) {
            // Preserva la fase actual del registro para evitar regresiones o aprobaciones no autorizadas.
            $faseSolicitada = $registro->fase_proceso;
        }
        if (!$puedeFaseFinal && !in_array($faseSolicitada, $fasesBasicas, true) && !in_array($faseSolicitada, $fasesFinales, true)) {
            $faseSolicitada = $registro->fase_proceso;
        }

        // 2. MAPEO DE DOCUMENTOS (Checks y Archivos de la Pestaña 3)
        $mapeoDoc = [
            'solicitud'   => ['file' => 'SOLICITUD',   'col' => 'check_solicitud'],
            'identidad'   => ['file' => 'IDENTIDAD',   'col' => 'check_identidad'],
            'domicilio'   => ['file' => 'DOMICILIO',   'col' => 'check_domicilio'],
            'curp_doc'    => ['file' => 'CURP',        'col' => 'check_curp_doc'],
            'rfc_doc'     => ['file' => 'RFC',         'col' => 'check_rfc_doc'],
            'manifiesto'  => ['file' => 'MANIFIESTO',   'col' => 'check_manifiesto'],
            'propiedad'   => ['file' => 'PROPIEDAD',   'col' => 'check_propiedad'],
            'finiquito'   => ['file' => 'FINIQUITO',   'col' => 'check_finiquito'],
            'siniiga_doc' => ['file' => 'SINIIGA',     'col' => 'check_siniiga_doc']
        ];

        $dbChecks = [];

        foreach ($mapeoDoc as $key => $info) {
            $columna = $info['col'];
            $prefijo = $info['file'];

            // Prioridad 1: Valor manual del checkbox enviado por el JS (1 o 0)
            $dbChecks[$columna] = (isset($_POST[$columna]) && $_POST[$columna] == '1') ? 1 : 0;

            // Prioridad 2: Borrado físico si se presionó la 'X'
            if (isset($_POST['delete_' . $key]) && $_POST['delete_' . $key] == '1') {
                $patron = $rutaDocs . '/' . $folioCarpeta . '_' . $prefijo . '.*';
                $archivosViejos = glob($patron);
                if ($archivosViejos) {
                    foreach ($archivosViejos as $f) { @unlink($f); }
                }
                $dbChecks[$columna] = 0; 
            } 
            
            // Prioridad 3: Subida de archivo nuevo / REEMPLAZO SEGURO
            if (isset($_FILES['file_' . $key]) && $_FILES['file_' . $key]['error'] === UPLOAD_ERR_OK) {
                if (!is_dir($rutaDocs)) { @mkdir($rutaDocs, 0775, true); }

                $ext = strtolower(pathinfo($_FILES['file_' . $key]['name'], PATHINFO_EXTENSION));
                $nombreFinal = $folioCarpeta . "_" . $prefijo . "." . $ext;
                $destino = $rutaDocs . "/" . $nombreFinal;

                // Guardamos la lista de archivos anteriores del mismo tipo ANTES de mover el nuevo.
                // Esto corrige el caso: viejo JPG, nuevo PDF/PNG => quedaban ambos y el frontend veía el viejo.
                $patronViejo = $rutaDocs . '/' . $folioCarpeta . '_' . $prefijo . '.*';
                $archivosViejos = glob($patronViejo) ?: [];

                if (move_uploaded_file($_FILES['file_' . $key]['tmp_name'], $destino)) {
                    chmod($destino, 0664);

                    // Si existían archivos anteriores del mismo requisito con otra extensión, se eliminan.
                    foreach ($archivosViejos as $viejo) {
                        if (realpath($viejo) !== realpath($destino)) {
                            @unlink($viejo);
                        }
                    }

                    $dbChecks[$columna] = 1;
                }
            }
        }

        // 3. PREPARACIÓN DE DATA PARA MODELO (Mapeo a columnas físicas)
        $data = [
            'id'                => $id,
            'nombre'            => mb_strtoupper($_POST['nombre_productor'], 'UTF-8'),
            'apellido_paterno'  => mb_strtoupper($_POST['paterno'], 'UTF-8'),
            'apellido_materno'  => mb_strtoupper($_POST['materno'], 'UTF-8'),
            'curp'              => mb_strtoupper($_POST['curp'], 'UTF-8'),
            'rfc'               => mb_strtoupper($_POST['rfc'] ?? '', 'UTF-8'),
            'tipo_id'           => $_POST['tipo_id'] ?? $registro->tipo_id,
            'numero_id'         => $_POST['numero_id'] ?? '',
            
            'estado_civil'      => $_POST['estado_civil'] ?? '',
            'escolaridad'       => $_POST['grado_estudios'] ?? '',
            'ocupacion'         => mb_strtoupper($_POST['ocupacion'], 'UTF-8'),
            'tiene_discapacidad'=> $_POST['tiene_discapacidad'] ?? 'NO',
            'cual_discapacidad' => mb_strtoupper($_POST['cual_discapacidad'] ?? 'NA', 'UTF-8'),
            'grupo_etnico'      => $_POST['grupo_etnico'] ?? 'NO',
            'grupo_etnico_cual' => mb_strtoupper($_POST['grupo_etnico_cual'] ?? 'NA', 'UTF-8'),
            
            'calle'             => mb_strtoupper($_POST['calle_numero'], 'UTF-8'),
            'colonia_nombre'    => mb_strtoupper($_POST['pueblo_colonia'], 'UTF-8'),
            'codigo_postal'     => $_POST['cp'] ?? '',
            'tel_particular'    => $_POST['tel_particular'] ?? '',
            'tel_casa'          => $_POST['tel_casa'] ?? '',
            'tel_familiar'      => $_POST['tel_recados'] ?? '',
            
            'linea_ayuda'       => $this->normalizarLineaAyuda($_POST['tipo_produccion'] ?? ($registro->linea_ayuda ?? 'AGRICOLA')),
            'registro_siniiga'  => $_POST['siniiga_status'] ?? 'NO',
            'num_total_predios' => $_POST['num_total_predios'] ?? 1,
            'superficie_total'  => $_POST['superficie_prod'] ?? 0,
            'tipo_documento_propiedad' => mb_strtoupper($_POST['tipo_documento_prop'] ?? '', 'UTF-8'),
            'pueblo_colonia_up' => mb_strtoupper($_POST['pueblo_colonia_up'] ?? '', 'UTF-8'),
            'parajes'           => mb_strtoupper($_POST['parajes'] ?? 'NA', 'UTF-8'),
            'tenencia_tierra'   => !empty($_POST['tenencia_tierra']) ? $_POST['tenencia_tierra'] : 'NA',
            'especie_cultivo_principal' => mb_strtoupper($_POST['cultivo_principal'] ?? '', 'UTF-8'),
            'numero_cabezas_colmenas'   => $_POST['num_animales'] ?? 0,
            
            // Fases y Observaciones
            'fase_proceso'      => $faseSolicitada,
            'estatus'           => $this->estatusPorFase($faseSolicitada),
            'observaciones_capturista' => mb_strtoupper($_POST['observaciones_capturista'] ?? '', 'UTF-8'),
            
            // VERIFICACIÓN (Pestaña 4)
            'latitud_verif'  => (is_numeric($lat_verif)) ? $lat_verif : null,
            'longitud_verif' => (is_numeric($lon_verif)) ? $lon_verif : null,

            // Checklist de Documentos (inyectamos el resultado del bucle anterior)
            'check_solicitud'   => $dbChecks['check_solicitud'],
            'check_identidad'   => $dbChecks['check_identidad'],
            'check_domicilio'   => $dbChecks['check_domicilio'],
            'check_curp_doc'    => $dbChecks['check_curp_doc'],
            'check_rfc_doc'     => $dbChecks['check_rfc_doc'],
            'check_manifiesto'  => $dbChecks['check_manifiesto'],
            'check_propiedad'   => $dbChecks['check_propiedad'],
            'check_finiquito'   => $dbChecks['check_finiquito'],
            'check_siniiga_doc' => $dbChecks['check_siniiga_doc'],
            'check_formatos_tecnicos' => 0,
            
            'json'              => $registro->respuestas_json 
        ];

        // 4. EJECUTAR ACTUALIZACIÓN PRINCIPAL
        $formatosActuales = $this->encuestaModel->contarEvidencias($id, 'FORMATOS_TECNICOS');
        $formatosNuevos = [];
        if (!empty($_FILES['formatos_tecnicos']['name'][0])) {
            foreach ($_FILES['formatos_tecnicos']['name'] as $key => $nombreOriginal) {
                if ($_FILES['formatos_tecnicos']['error'][$key] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
                    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                        http_response_code(422);
                        echo json_encode(['status' => 'error', 'msg' => 'Formatos tÃ©cnicos solo acepta imÃ¡genes JPG, PNG o WEBP.']);
                        return;
                    }
                    $formatosNuevos[] = $key;
                }
            }
        }
        if (($formatosActuales + count($formatosNuevos)) > 3) {
            http_response_code(422);
            echo json_encode(['status' => 'error', 'msg' => 'Formatos tÃ©cnicos permite un mÃ¡ximo de 3 imÃ¡genes.']);
            return;
        }
        $data['check_formatos_tecnicos'] = ($formatosActuales + count($formatosNuevos)) > 0 ? 1 : 0;

        if ($this->encuestaModel->actualizarExpediente($data)) {
            if (!empty($formatosNuevos)) {
                $rutaFormatos = PUBROOT . '/uploads/formatos_tecnicos/' . $folioCarpeta;
                if (!is_dir($rutaFormatos)) { @mkdir($rutaFormatos, 0775, true); }

                foreach ($formatosNuevos as $key) {
                    $nombreOriginal = $_FILES['formatos_tecnicos']['name'][$key];
                    $ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

                    $nombreFoto = 'FT_' . date('Ymd_His') . '_' . uniqid() . '_' . ($key + 1) . '.' . $ext;
                    $destinoFoto = $rutaFormatos . '/' . $nombreFoto;
                    if (move_uploaded_file($_FILES['formatos_tecnicos']['tmp_name'][$key], $destinoFoto)) {
                        $rutaRelativa = 'uploads/formatos_tecnicos/' . $folioCarpeta . '/' . $nombreFoto;
                        $this->encuestaModel->guardarEvidenciaFoto($id, $rutaRelativa, 'FORMATOS_TECNICOS');
                    }
                }
                $this->encuestaModel->actualizarCheckFormatosTecnicos($id);
            }
            
            // 5. PROCESAR EVIDENCIAS FOTOGRÁFICAS (Pestaña 4)
            if (!empty($_FILES['fotos_evidencia']['name'][0])) {
                if (!is_dir($rutaEvidencias)) { @mkdir($rutaEvidencias, 0775, true); }

                $fotos = $_FILES['fotos_evidencia'];
                foreach ($fotos['name'] as $key => $nombreOriginal) {
                    if ($fotos['error'][$key] === UPLOAD_ERR_OK) {
                        $ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
                        $nombreFoto = "EV_" . date('Ymd_His') . "_" . ($key + 1) . "." . $ext;
                        $destinoFoto = $rutaEvidencias . "/" . $nombreFoto;

                        if (move_uploaded_file($fotos['tmp_name'][$key], $destinoFoto)) {
                            // Guardar ruta en la tabla relacional
                            $rutaRelativa = 'uploads/verificaciones/' . $folioCarpeta . '/' . $nombreFoto;
                            $this->encuestaModel->guardarEvidenciaFoto($id, $rutaRelativa);
                        }
                    }
                }
            }

            echo json_encode(['status' => 'success', 'msg' => 'Expediente digital actualizado correctamente']);
        } else {
            $error = $this->encuestaModel->getError();
            echo json_encode(['status' => 'error', 'msg' => 'Error al guardar: ' . $error]);
        }
    }
}

// En Captura.php
public function verificarArchivos($id) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {
        if (!is_numeric($id)) {
            throw new Exception('ID inválido');
        }

        $registro = $this->encuestaModel->getEncuestaById($id);
        if (!$registro) {
            echo json_encode([]);
            exit;
        }

        $folioCarpeta = str_replace(['/', ' ', '\\'], '-', $registro->folio);
        $ruta = PUBROOT . '/uploads/expedientes/' . $folioCarpeta;

        $porTipo = [];
        if (is_dir($ruta)) {
            $files = scandir($ruta);
            foreach ($files as $f) {
                if ($f === '.' || $f === '..') continue;

                $rutaFisica = $ruta . '/' . $f;
                if (!is_file($rutaFisica)) continue;

                $partes = explode('_', pathinfo($f, PATHINFO_FILENAME));
                $tipo = strtoupper(end($partes));
                $mtime = filemtime($rutaFisica) ?: time();

                // Si por algún motivo hubiera duplicados, mostramos el más reciente.
                if (!isset($porTipo[$tipo]) || $mtime > $porTipo[$tipo]['mtime']) {
                    $porTipo[$tipo] = [
                        'tipo' => $tipo,
                        'url'  => URLROOT . '/ArchivoSeguro/expediente/' . rawurlencode($folioCarpeta) . '/' . rawurlencode($f) . '?v=' . $mtime,
                        'mtime' => $mtime
                    ];
                }
            }
        }

        echo json_encode(array_values($porTipo), JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}
public function getFotosEvidencia($id) {
    // 1. Limpiar cualquier salida previa (errores, espacios, etc)
    if (ob_get_length()) ob_clean();
    
    // 2. Forzar encabezado JSON
    header('Content-Type: application/json; charset=utf-8');

    try {
        // Validar que el ID sea numérico
        if (!is_numeric($id)) {
            throw new Exception("ID inválido");
        }

        $fotos = $this->encuestaModel->getEvidencias($id);
        $data = [];
        
        if ($fotos) {
            foreach ($fotos as $f) {
                // Construir la URL completa
                $data[] = [
                    'id' => $f->id,
                    'url' => URLROOT . '/ArchivoSeguro/evidencia/' . (int)$f->id
                ];
            }
        }
        
        // 3. Imprimir solo el JSON y nada más
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        // Si algo falla, mandamos el error en formato JSON, no HTML
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    
    // 4. Detener la ejecución de PHP aquí mismo
    exit;
}

public function eliminarEvidencia($fotoId) {
    // Limpieza de salida para evitar errores de JSON
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    try {
        // 1. Pedir al modelo la información de la foto
        $foto = $this->encuestaModel->getEvidenciaById($fotoId);

        if ($foto) {
            // 2. Borrar archivo físico del servidor
            $rutaFisica = PUBROOT . '/' . $foto->ruta_archivo;
            if (file_exists($rutaFisica)) {
                unlink($rutaFisica);
            }

            // 3. Pedir al modelo que borre el registro en la BD
            if ($this->encuestaModel->eliminarEvidenciaRow($fotoId)) {
                if (($foto->tipo_evidencia ?? '') === 'FORMATOS_TECNICOS') {
                    $this->encuestaModel->actualizarCheckFormatosTecnicos($foto->encuesta_id);
                }
                echo json_encode(['status' => 'success', 'msg' => 'Evidencia eliminada']);
            } else {
                throw new Exception("Error al eliminar el registro de la base de datos");
            }
        } else {
            throw new Exception("La evidencia no existe");
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
    exit;
}

public function getFormatosTecnicos($id) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json; charset=utf-8');

    try {
        if (!is_numeric($id)) throw new Exception('ID invÃ¡lido');
        $fotos = $this->encuestaModel->getEvidencias($id, 'FORMATOS_TECNICOS');
        $data = [];
        foreach ($fotos as $f) {
            $data[] = [
                'id' => $f->id,
                'url' => URLROOT . '/ArchivoSeguro/evidencia/' . (int)$f->id
            ];
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

private function normalizarLineaAyuda($valor) {
    $valor = strtoupper(trim((string)$valor));
    $permitidas = ['AGRICOLA', 'PECUARIA', 'GRANJA_INTEGRAL', 'HUERTO_URBANO'];
    return in_array($valor, $permitidas, true) ? $valor : 'AGRICOLA';
}

private function estatusPorFase($fase) {
    $estatus = [
        'EMPADRONADO' => 'Empadronado',
        'SOLICITUD_INGRESADA' => 'Solicitud ingresada',
        'VALIDACION_DOCS' => 'ValidaciÃ³n docs',
        'EN_REVISION' => 'En revisiÃ³n',
        'COMITE' => 'ComitÃ©',
        'APROBADO' => 'Aprobado',
        'RECHAZADO' => 'Rechazado'
    ];
    return $estatus[$fase] ?? $fase;
}

}
