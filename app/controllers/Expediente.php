<?php
require_once '../vendor/autoload.php'; 
use setasign\Fpdi\Fpdi;

class Expediente extends Controller {
    protected $encuestaModel;

    public function __construct() {
        $this->encuestaModel = $this->model('EncuestaModelo'); 
    }

    private function toLatin1($txt) {
        if ($txt === null || $txt === '') return '';
        return iconv('UTF-8', 'windows-1252//TRANSLIT', $txt);
    }

    public function imprimirSolicitud($id) {
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

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);

        // A. Encabezado (Folio y Fecha) [cite: 3, 10]
        $pdf->SetXY(152, 44); $pdf->Write(0, $datos->folio ?? '');
        $fecha = ($datos->fecha_inicio) ? date('d/m/Y', strtotime($datos->fecha_inicio)) : date('d/m/Y');
        $pdf->SetXY(152, 63); $pdf->Write(0, $fecha);

        // B. Identidad del Solicitante 
        $pdf->SetXY(22, 102); $pdf->Write(0, $this->toLatin1($datos->nombre ?? ''));
        $pdf->SetXY(85, 102); $pdf->Write(0, $this->toLatin1($datos->apellido_paterno ?? ''));
        $pdf->SetXY(145, 102); $pdf->Write(0, $this->toLatin1($datos->apellido_materno ?? ''));
        
        $pdf->SetXY(22, 115); $pdf->Write(0, $datos->curp ?? '');
        $pdf->SetXY(145, 115); $pdf->Write(0, $datos->rfc ?? '');

        // C. Datos Generales [cite: 17, 18, 20, 23, 27, 28]
        $pdf->SetXY(22, 130); $pdf->Write(0, $this->toLatin1($datos->tipo_id ?? 'INE'));
        $pdf->SetXY(145, 130); $pdf->Write(0, $datos->numero_id ?? '');
        
        $pdf->SetXY(22, 142); $pdf->Write(0, $this->toLatin1($datos->estado_civil ?? ''));
        $pdf->SetXY(85, 142); $pdf->Write(0, $this->toLatin1($datos->escolaridad ?? ''));
        $pdf->SetXY(145, 142); $pdf->Write(0, $this->toLatin1($datos->ocupacion ?? ''));

        // Discapacidad y Etnia [cite: 20, 23]
        $pdf->SetXY(60, 148); $pdf->Write(0, $this->toLatin1(($datos->tiene_discapacidad ?? 'NO') . " / " . ($datos->cual_discapacidad ?? 'NA')));
        $pdf->SetXY(60, 154); $pdf->Write(0, $this->toLatin1(($datos->grupo_etnico ?? 'NO') . " / " . ($datos->grupo_etnico_cual ?? 'NA')));

        // D. Domicilio y Contacto [cite: 24]
        $pdf->SetXY(22, 172); $pdf->Write(0, $this->toLatin1($datos->calle ?? ''));
        $pdf->SetXY(85, 172); $pdf->Write(0, $this->toLatin1($datos->colonia_nombre ?? ''));
        $pdf->SetXY(150, 172); $pdf->Write(0, $datos->codigo_postal ?? '');
        
        $pdf->SetXY(22, 184); $pdf->Write(0, $datos->tel_particular ?? '');
        $pdf->SetXY(85, 184); $pdf->Write(0, $datos->tel_casa ?? '');
        $pdf->SetXY(145, 184); $pdf->Write(0, $datos->tel_familiar ?? '');

        // E. Checklist de Requisitos (Coordenadas ajustadas a la derecha) 
        $baseY = 198;
        if (!empty($datos->check_identidad)) { $pdf->SetXY(192, $baseY); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_domicilio)) { $pdf->SetXY(192, $baseY + 7); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_curp_doc))  { $pdf->SetXY(192, $baseY + 14); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_rfc_doc))   { $pdf->SetXY(192, $baseY + 21); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_propiedad)) { $pdf->SetXY(192, $baseY + 28); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_siniiga_doc)){ $pdf->SetXY(192, $baseY + 35); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_finiquito)) { $pdf->SetXY(192, $baseY + 42); $pdf->Write(0, 'X'); }

        // F. Fortalecimiento Producción Primaria (Tabla inferior) 
        $pdf->SetXY(40, 245); $pdf->Write(0, $datos->num_total_predios ?? '1');
        $pdf->SetXY(150, 245); $pdf->Write(0, ($datos->superficie_total ?? '0') . ' ha');
        $pdf->SetXY(40, 255); $pdf->Write(0, $this->toLatin1($datos->tipo_documento_propiedad ?? ''));
        $pdf->SetXY(40, 265); $pdf->Write(0, $this->toLatin1($datos->pueblo_colonia_up ?? ''));
        $pdf->SetXY(150, 265); $pdf->Write(0, $this->toLatin1($datos->parajes ?? ''));

        // --- PÁGINA 2: COMPROMISOS ---
        $tplId2 = $pdf->importPage(2);
        $pdf->addPage();
        $pdf->useTemplate($tplId2);
        
        $nombreFull = trim(($datos->nombre ?? '') . ' ' . ($datos->apellido_paterno ?? '') . ' ' . ($datos->apellido_materno ?? ''));
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(110, 260); // Posición sobre la línea de firma del solicitante [cite: 72]
        $pdf->Cell(80, 0, $this->toLatin1($nombreFull), 0, 0, 'C');

        // --- PÁGINA 3: AVISO DE PRIVACIDAD ---
        $tplId3 = $pdf->importPage(3);
        $pdf->addPage();
        $pdf->useTemplate($tplId3);
        
        $pdf->SetXY(25, 260); // Firma en la última página [cite: 99]
        $pdf->Cell(80, 0, $this->toLatin1($nombreFull), 0, 0, 'C');

        $pdf->Output('I', "Solicitud_{$datos->folio}.pdf");
    }
}