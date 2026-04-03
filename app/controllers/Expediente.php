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
        $pdf->SetFont('Arial', '', 9); 
        $pdf->SetTextColor(0, 0, 0);

        // A. Encabezado (Folio y Fecha)
        $pdf->SetXY(155, 43); $pdf->Write(0, $datos->folio ?? '');
        $fecha = ($datos->fecha_inicio) ? date('d/m/Y', strtotime($datos->fecha_inicio)) : date('d/m/Y');
        $pdf->SetXY(155, 61); $pdf->Write(0, $fecha);

        // B. Identidad del Solicitante (Ajuste de altura para centrar en fila)
        $pdf->SetXY(22, 105); $pdf->Write(0, $this->toLatin1($datos->nombre ?? ''));
        $pdf->SetXY(85, 105); $pdf->Write(0, $this->toLatin1($datos->apellido_paterno ?? ''));
        $pdf->SetXY(145, 105); $pdf->Write(0, $this->toLatin1($datos->apellido_materno ?? ''));
        
        $pdf->SetXY(22, 118); $pdf->Write(0, $datos->curp ?? '');
        $pdf->SetXY(145, 118); $pdf->Write(0, $datos->rfc ?? '');

        // C. Datos Generales
        $pdf->SetXY(22, 133); $pdf->Write(0, $this->toLatin1($datos->tipo_id ?? 'INE'));
        $pdf->SetXY(145, 133); $pdf->Write(0, $datos->numero_id ?? '');
        
        $pdf->SetXY(22, 146); $pdf->Write(0, $this->toLatin1($datos->estado_civil ?? ''));
        $pdf->SetXY(85, 146); $pdf->Write(0, $this->toLatin1($datos->escolaridad ?? ''));
        $pdf->SetXY(145, 146); $pdf->Write(0, $this->toLatin1($datos->ocupacion ?? ''));

        // Discapacidad y Etnia (Ajustado a los cuadros de respuesta)
        $pdf->SetXY(60, 154); $pdf->Write(0, $this->toLatin1($datos->tiene_discapacidad ?? 'NO'));
        $pdf->SetXY(155, 154); $pdf->Write(0, $this->toLatin1($datos->cual_discapacidad ?? 'NA'));
        
        $pdf->SetXY(60, 161); $pdf->Write(0, $this->toLatin1($datos->grupo_etnico ?? 'NO'));
        $pdf->SetXY(155, 161); $pdf->Write(0, $this->toLatin1($datos->grupo_etnico_cual ?? 'NA'));

        // D. Domicilio y Contacto
        $pdf->SetXY(22, 178); $pdf->Write(0, $this->toLatin1($datos->calle ?? ''));
        $pdf->SetXY(85, 178); $pdf->Write(0, $this->toLatin1($datos->pueblo_colonia ?? $datos->colonia_nombre ?? ''));
        $pdf->SetXY(150, 178); $pdf->Write(0, $datos->codigo_postal ?? '');
        
        $pdf->SetXY(22, 190); $pdf->Write(0, $datos->tel_particular ?? '');
        $pdf->SetXY(85, 190); $pdf->Write(0, $datos->tel_casa ?? '');
        $pdf->SetXY(145, 190); $pdf->Write(0, $datos->tel_familiar ?? '');

        // E. Checklist de Requisitos (Marcas 'X')
        $baseY = 205; 
        if (!empty($datos->check_identidad)) { $pdf->SetXY(192, $baseY); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_domicilio)) { $pdf->SetXY(192, $baseY + 7); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_curp_doc))  { $pdf->SetXY(192, $baseY + 14); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_rfc_doc))   { $pdf->SetXY(192, $baseY + 21); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_propiedad)) { $pdf->SetXY(192, $baseY + 28); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_siniiga_doc)){ $pdf->SetXY(192, $baseY + 35); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_finiquito)) { $pdf->SetXY(192, $baseY + 42); $pdf->Write(0, 'X'); }

        // F. Producción Primaria
        $pdf->SetXY(40, 248); $pdf->Write(0, $datos->num_total_predios ?? '1');
        $pdf->SetXY(150, 248); $pdf->Write(0, ($datos->superficie_total ?? '0') . ' ha');
        
        $pdf->SetXY(40, 258); $pdf->Write(0, $this->toLatin1($datos->tipo_documento_propiedad ?? ''));
        
        $pdf->SetXY(40, 268); $pdf->Write(0, $this->toLatin1($datos->pueblo_colonia_up ?? ''));
        $pdf->SetXY(150, 268); $pdf->Write(0, $this->toLatin1($datos->parajes ?? ''));

        $pdf->SetXY(40, 278); $pdf->Write(0, $this->toLatin1($datos->especie_cultivo_principal ?? ''));
        $pdf->SetXY(150, 278); $pdf->Write(0, $datos->numero_cabezas_colmenas ?? '0');

        // --- PÁGINA 2: COMPROMISOS ---
        $tplId2 = $pdf->importPage(2);
        $pdf->addPage();
        $pdf->useTemplate($tplId2);
        
        $nombreFull = trim(($datos->nombre ?? '') . ' ' . ($datos->apellido_paterno ?? '') . ' ' . ($datos->apellido_materno ?? ''));
        $pdf->SetFont('Arial', 'B', 9);
        
        // Firma Solicitante (Centrada sobre la línea)
        $pdf->SetXY(110, 267); 
        $pdf->Cell(80, 0, $this->toLatin1($nombreFull), 0, 0, 'C');

        // --- PÁGINA 3: AVISO DE PRIVACIDAD ---
        $tplId3 = $pdf->importPage(3);
        $pdf->addPage();
        $pdf->useTemplate($tplId3);
        
        // Firma última página (Centrada sobre la línea)
        $pdf->SetXY(25, 267); 
        $pdf->Cell(80, 0, $this->toLatin1($nombreFull), 0, 0, 'C');

        // Salida del PDF
        $pdf->Output('I', "Solicitud_{$datos->folio}.pdf");
    }
}