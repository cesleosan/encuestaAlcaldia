<?php
require_once '../vendor/autoload.php'; 
use setasign\Fpdi\Fpdi;

class Expediente extends Controller {
    protected $encuestaModel;

    public function __construct() {
        $this->encuestaModel = $this->model('EncuestaModelo'); 
    }

    /**
     * Convierte el texto a codificación Latin1 y lo transforma a MAYÚSCULAS
     */
    private function toLatin1($txt) {
        if ($txt === null || $txt === '') return '';
        // Convertimos a mayúsculas antes de la codificación para preservar caracteres especiales
        $txtMayus = mb_strtoupper($txt, 'UTF-8');
        return iconv('UTF-8', 'windows-1252//TRANSLIT', $txtMayus);
    }

    public function imprimirSolicitud($id) {
        // Limpiar cualquier salida previa para evitar corrupción del PDF
        if (ob_get_level()) ob_end_clean();

        $datos = $this->encuestaModel->getExpedienteCompleto($id);
        if (!$datos) die("Error: Expediente no encontrado.");

        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);
        $pdf->SetTitle("Solicitud_" . ($datos->folio ?? 'SF'));

        // --- PÁGINA 1: DATOS Y REQUISITOS ---
        $rutaTemplate = APPROOT . '/views/formatos/formatoProductores2026.pdf'; 
        $pdf->setSourceFile($rutaTemplate);
        $tplId = $pdf->importPage(1);
        $pdf->addPage();
        $pdf->useTemplate($tplId);

        // Fuente estándar para los datos
        $pdf->SetFont('Arial', '', 8); 
        $pdf->SetTextColor(0, 0, 0);

        // A. Encabezado (Folio y Fecha)
        $pdf->SetXY(155, 43); $pdf->Write(0, $datos->folio ?? '');
        $fecha = ($datos->fecha_inicio) ? date('d/m/Y', strtotime($datos->fecha_inicio)) : date('d/m/Y');
        $pdf->SetXY(155, 55); $pdf->Write(0, $fecha);

        // B. Identidad del Solicitante
        $pdf->SetXY(25, 83); $pdf->Write(0, $this->toLatin1($datos->nombre ?? ''));
        $pdf->SetXY(75, 83); $pdf->Write(0, $this->toLatin1($datos->apellido_paterno ?? ''));
        $pdf->SetXY(135, 83); $pdf->Write(0, $this->toLatin1($datos->apellido_materno ?? ''));
        
        $pdf->SetXY(75, 91); $pdf->Write(0, $datos->curp ?? '');
        $pdf->SetXY(160, 91); $pdf->Write(0, $datos->rfc ?? '');

        // C. Datos Generales
        $pdf->SetXY(25, 94); $pdf->Write(0, $this->toLatin1($datos->tipo_id ?? 'INE'));
        $pdf->SetXY(120, 94); $pdf->Write(0, $datos->numero_id ?? '');
        
        $pdf->SetXY(25, 102); $pdf->Write(0, $this->toLatin1($datos->estado_civil ?? ''));
        $pdf->SetXY(85, 102); $pdf->Write(0, $this->toLatin1($datos->escolaridad ?? ''));
        $pdf->SetXY(135, 102); $pdf->Write(0, $this->toLatin1($datos->ocupacion ?? ''));

        // Discapacidad y Etnia
        $pdf->SetXY(105, 109); $pdf->Write(0, $this->toLatin1($datos->tiene_discapacidad ?? 'NO'));
        $pdf->SetXY(105, 113); $pdf->Write(0, $this->toLatin1($datos->cual_discapacidad ?? 'NA'));
        
        $pdf->SetXY(158, 109); $pdf->Write(0, $this->toLatin1($datos->grupo_etnico ?? 'NO'));
        $pdf->SetXY(158, 113); $pdf->Write(0, $this->toLatin1($datos->grupo_etnico_cual ?? 'NA'));

        // D. Domicilio y Contacto
        $pdf->SetXY(25, 116); $pdf->Write(0, $this->toLatin1($datos->calle ?? ''));
        $pdf->SetXY(75, 116); $pdf->Write(0, $this->toLatin1($datos->pueblo_colonia ?? $datos->colonia_nombre ?? ''));
        $pdf->SetXY(138, 116); $pdf->Write(0, $datos->codigo_postal ?? '');
        
        $pdf->SetXY(40, 123); $pdf->Write(0, $datos->tel_particular ?? '');
        $pdf->SetXY(95, 123); $pdf->Write(0, $datos->tel_casa ?? '');
        $pdf->SetXY(159, 123); $pdf->Write(0, $datos->tel_familiar ?? '');

        // E. Checklist de Requisitos (Marcas 'X')
        $baseY = 139; 
        $intercalado = 5; 

        if (!empty($datos->check_identidad))   { $pdf->SetXY(180, $baseY); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_domicilio))   { $pdf->SetXY(180, $baseY + $intercalado); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_curp_doc))    { $pdf->SetXY(180, $baseY + ($intercalado * 2)); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_rfc_doc))     { $pdf->SetXY(180, $baseY + ($intercalado * 3)); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_propiedad))   { $pdf->SetXY(180, $baseY + ($intercalado * 4)); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_siniiga_doc)) { $pdf->SetXY(180, $baseY + ($intercalado * 5)); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_finiquito))   { $pdf->SetXY(180, $baseY + ($intercalado * 6)); $pdf->Write(0, 'X'); }

        // F. Producción Primaria
        $pdf->SetXY(85, 180); $pdf->Write(0, $datos->num_total_predios ?? '1');
        $pdf->SetXY(165, 180); $pdf->Write(0, ($datos->superficie_total ?? '0') . ' HA');
        
        $pdf->SetXY(80, 185); $pdf->Write(0, $this->toLatin1($datos->tipo_documento_propiedad ?? ''));
        
        $pdf->SetXY(95, 193); $pdf->Write(0, $this->toLatin1($datos->pueblo_colonia_up ?? ''));
        $pdf->SetXY(165, 193); $pdf->Write(0, $this->toLatin1($datos->parajes ?? ''));

        $pdf->SetXY(75, 205); $pdf->Write(0, $this->toLatin1($datos->especie_cultivo_principal ?? ''));
        $pdf->SetXY(165, 205); $pdf->Write(0, $datos->numero_cabezas_colmenas ?? '0');

        // --- PÁGINA 2: COMPROMISOS ---
        $tplId2 = $pdf->importPage(2);
        $pdf->addPage();
        $pdf->useTemplate($tplId2);
        
        $nombreFull = trim(($datos->nombre ?? '') . ' ' . ($datos->apellido_paterno ?? '') . ' ' . ($datos->apellido_materno ?? ''));
        $pdf->SetFont('Arial', 'B', 9);
        
        // Firma Solicitante
        $pdf->SetXY(110, 267); 
        $pdf->Cell(80, 0, $this->toLatin1($nombreFull), 0, 0, 'C');

        // --- PÁGINA 3: AVISO DE PRIVACIDAD ---
        $tplId3 = $pdf->importPage(3);
        $pdf->addPage();
        $pdf->useTemplate($tplId3);
        
        // Firma última página
        $pdf->SetXY(25, 267); 
        $pdf->Cell(80, 0, $this->toLatin1($nombreFull), 0, 0, 'C');

        // Salida del PDF
        $pdf->Output('I', "Solicitud_{$datos->folio}.pdf");
    }

    /**
     * Mantiene la cuadrícula de calibración intacta
     */
    public function calibrar($id) {
        if (ob_get_level()) ob_end_clean();

        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);
        $rutaTemplate = APPROOT . '/views/formatos/formatoProductores2026.pdf'; 
        $pdf->setSourceFile($rutaTemplate);
        $tplId = $pdf->importPage(1);
        $pdf->addPage();
        $pdf->useTemplate($tplId);

        $pdf->SetFont('Arial', '', 7);
        $pdf->SetDrawColor(255, 0, 0);
        $pdf->SetTextColor(255, 0, 0);

        for ($y = 0; $y <= 297; $y += 5) {
            $pdf->Line(0, $y, 210, $y);
            if ($y % 10 == 0) {
                $pdf->SetXY(2, $y - 2);
                $pdf->Write(0, "Y=$y");
            }
        }

        for ($x = 0; $x <= 210; $x += 5) {
            $pdf->Line($x, 0, $x, 297);
            if ($x % 10 == 0) {
                $pdf->SetXY($x + 1, 2);
                $pdf->Write(0, "X=$x");
            }
        }

        $datos = $this->encuestaModel->getExpedienteCompleto($id);
        if($datos){
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetTextColor(0, 0, 255);
            $pdf->SetXY(22, 105); 
            $pdf->Write(0, "PRUEBA: " . $this->toLatin1($datos->nombre));
        }

        $pdf->Output('I', 'CALIBRACION_COORDENADAS.pdf');
    }
}