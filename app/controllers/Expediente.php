<?php
// Carga del autoloader de Composer para FPDI y FPDF
require_once '../vendor/autoload.php'; 

use setasign\Fpdi\Fpdi;

class Expediente extends Controller {
    protected $encuestaModel;

    public function __construct() {
        // Asegúrate de que el nombre del modelo coincida con tu archivo EncuestaModelo.php
        $this->encuestaModel = $this->model('EncuestaModelo'); 
    }

    /**
     * Función auxiliar robusta para convertir texto a formato PDF (ISO-8859-1)
     * Reemplaza a utf8_decode (obsoleta en PHP 8.2+) y maneja valores nulos.
     */
    private function toLatin1($txt) {
        if ($txt === null || $txt === '') return '';
        // Convertimos de UTF-8 a Windows-1252 para soporte de acentos y ñ en FPDF
        return iconv('UTF-8', 'windows-1252//TRANSLIT', $txt);
    }

    public function imprimirSolicitud($id) {
        // --- PASO CRÍTICO: Limpiar búfer de salida ---
        // Esto elimina cualquier Warning previo que pueda romper la generación del PDF
        if (ob_get_level()) {
            ob_end_clean();
        }

        // 1. Obtener datos del modelo
        $datos = $this->encuestaModel->getExpedienteCompleto($id);
        
        if (!$datos) {
            die("Error: Expediente no encontrado en la base de datos.");
        }
        
        // 2. Configuración inicial del PDF
        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);
        $pdf->SetTitle("Solicitud_" . ($datos->folio ?? 'SF'));

        // --- PÁGINA 1: DATOS GENERALES Y TÉCNICOS ---
        $rutaTemplate = APPROOT . '/views/formatos/formatoProductores2026.pdf'; 
        
        try {
            $pdf->setSourceFile($rutaTemplate);
        } catch (Exception $e) {
            die("Error al abrir la plantilla PDF: " . $e->getMessage());
        }

        $tplId = $pdf->importPage(1);
        $pdf->addPage();
        $pdf->useTemplate($tplId);

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(0, 0, 0);

        // A. Encabezado: Folio y Fecha
        $pdf->SetXY(160, 23); $pdf->Write(0, $datos->folio ?? '');
        $fechaCaptura = ($datos->fecha_inicio) ? date('d/m/Y', strtotime($datos->fecha_inicio)) : date('d/m/Y');
        $pdf->SetXY(160, 48); $pdf->Write(0, $fechaCaptura);

        // B. Identidad (Uso de toLatin1 y Null Coalescing para evitar Undefined Property)
        $pdf->SetXY(20, 68);  $pdf->Write(0, $this->toLatin1($datos->nombre ?? ''));
        $pdf->SetXY(80, 68);  $pdf->Write(0, $this->toLatin1($datos->apellido_paterno ?? ''));
        $pdf->SetXY(140, 68); $pdf->Write(0, $this->toLatin1($datos->apellido_materno ?? ''));
        $pdf->SetXY(20, 78);  $pdf->Write(0, $datos->curp ?? '');
        $pdf->SetXY(140, 78); $pdf->Write(0, $datos->rfc ?? '');

        // C. Datos Generales
        $pdf->SetXY(20, 88);  $pdf->Write(0, $this->toLatin1($datos->tipo_id ?? ''));
        $pdf->SetXY(110, 88); $pdf->Write(0, $this->toLatin1($datos->numero_id ?? ''));
        
        $pdf->SetXY(20, 98);  $pdf->Write(0, $this->toLatin1($datos->estado_civil ?? ''));
        $pdf->SetXY(80, 98);  $pdf->Write(0, $this->toLatin1($datos->escolaridad ?? ''));
        $pdf->SetXY(140, 98); $pdf->Write(0, $this->toLatin1($datos->ocupacion ?? ''));

        // Discapacidad y Etnia
        $discap = ($datos->tiene_discapacidad ?? 'NO') . " / " . ($datos->cual_discapacidad ?? 'NA');
        $pdf->SetXY(55, 103); $pdf->Write(0, $this->toLatin1($discap));
        $etnia = ($datos->grupo_etnico ?? 'NO') . " / " . ($datos->grupo_etnico_cual ?? 'NA');
        $pdf->SetXY(55, 108); $pdf->Write(0, $this->toLatin1($etnia));

        // D. Domicilio y Contacto
        $pdf->SetXY(20, 118); $pdf->Write(0, $this->toLatin1($datos->calle ?? ''));
        $pdf->SetXY(80, 118); $pdf->Write(0, $this->toLatin1($datos->colonia_nombre ?? ''));
        $pdf->SetXY(175, 118); $pdf->Write(0, $datos->codigo_postal ?? '');
        
        $pdf->SetXY(20, 128); $pdf->Write(0, $datos->tel_particular ?? '');
        $pdf->SetXY(80, 128); $pdf->Write(0, $datos->tel_casa ?? '');
        $pdf->SetXY(140, 128); $pdf->Write(0, $datos->tel_familiar ?? '');

        // E. Checklist de Requisitos (Marcado con X si el valor es 1)
        $baseY = 138;
        if (!empty($datos->check_identidad)) { $pdf->SetXY(192, $baseY); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_domicilio)) { $pdf->SetXY(192, $baseY + 7); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_curp_doc))  { $pdf->SetXY(192, $baseY + 14); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_rfc_doc))   { $pdf->SetXY(192, $baseY + 21); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_propiedad)) { $pdf->SetXY(192, $baseY + 28); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_siniiga_doc)){ $pdf->SetXY(192, $baseY + 35); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_finiquito)) { $pdf->SetXY(192, $baseY + 42); $pdf->Write(0, 'X'); }

        // F. Componente Producción Primaria
        $pdf->SetXY(35, 195); $pdf->Write(0, $datos->num_total_predios ?? '1');
        $pdf->SetXY(135, 195); $pdf->Write(0, ($datos->superficie_total ?? '0') . ' ha');
        $pdf->SetXY(35, 205); $pdf->Write(0, $this->toLatin1($datos->tipo_documento_propiedad ?? ''));
        $pdf->SetXY(35, 215); $pdf->Write(0, $this->toLatin1($datos->pueblo_colonia_up ?? ''));
        $pdf->SetXY(135, 215); $pdf->Write(0, $this->toLatin1($datos->parajes ?? ''));
        $pdf->SetXY(35, 225); $pdf->Write(0, $this->toLatin1($datos->tenencia_tierra ?? ''));
        $pdf->SetXY(35, 235); $pdf->Write(0, $this->toLatin1($datos->especie_cultivo_principal ?? ''));
        $pdf->SetXY(160, 235); $pdf->Write(0, $datos->numero_cabezas_colmenas ?? '0');

        // --- PÁGINA 2: COMPROMISOS Y FIRMA ---
        $tplId2 = $pdf->importPage(2);
        $pdf->addPage();
        $pdf->useTemplate($tplId2);
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(110, 258); 
        $nombreCompleto = ($datos->nombre ?? '') . ' ' . ($datos->apellido_paterno ?? '') . ' ' . ($datos->apellido_materno ?? '');
        $pdf->Cell(80, 0, $this->toLatin1(trim($nombreCompleto)), 0, 0, 'C');

        // --- PÁGINA 3: AVISO DE PRIVACIDAD ---
        $tplId3 = $pdf->importPage(3);
        $pdf->addPage();
        $pdf->useTemplate($tplId3);
        
        $pdf->SetXY(20, 258); 
        $pdf->Cell(80, 0, $this->toLatin1(trim($nombreCompleto)), 0, 0, 'C');

        // 3. Salida final al navegador (I = Inline/Visualizar)
        $pdf->Output('I', "Solicitud_{$datos->folio}.pdf");
    }
}