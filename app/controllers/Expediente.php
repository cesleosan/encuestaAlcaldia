<?php
require_once '../vendor/autoload.php'; 
use setasign\Fpdi\Fpdi;

class Expediente extends Controller {
    protected $encuestaModel;

    public function __construct() {
        $this->encuestaModel = $this->model('EncuestaModelo'); 
    }

    /**
     * Convierte el texto a Latin1 y MAYÚSCULAS
     */
    private function toLatin1($txt) {
        if ($txt === null || $txt === '') return '';
        $txtMayus = mb_strtoupper($txt, 'UTF-8');
        return iconv('UTF-8', 'windows-1252//TRANSLIT', $txtMayus);
    }

    /**
     * Función Quirúrgica para escribir texto con ajuste dinámico de fuente
     * Evita que los textos largos desborden el recuadro
     */
    private function escribirAjustado($pdf, $x, $y, $texto, $anchoMax, $fuenteBase = 8) {
        $textoFinal = $this->toLatin1($texto);
        $pdf->SetFont('Arial', '', $fuenteBase);
        
        // Mientras el texto sea más ancho que el recuadro, bajamos la fuente
        while($pdf->GetStringWidth($textoFinal) > $anchoMax && $fuenteBase > 5) {
            $fuenteBase -= 0.5;
            $pdf->SetFont('Arial', '', $fuenteBase);
        }
        
        $pdf->SetXY($x, $y);
        $pdf->Write(0, $textoFinal);
        
        // Restauramos fuente original
        $pdf->SetFont('Arial', '', 8);
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

        $pdf->SetFont('Arial', '', 8); 
        $pdf->SetTextColor(0, 0, 0);

        // A. Encabezado
        $pdf->SetXY(65, 53); $pdf->Write(0, $datos->folio ?? '');
        $pdf->SetXY(157, 55); $pdf->Write(0, date('d/m/Y'));

        // B. Identidad (Con ajuste dinámico por si son nombres muy largos)
        $this->escribirAjustado($pdf, 25, 83, $datos->nombre ?? '', 45);
        $this->escribirAjustado($pdf, 75, 83, $datos->apellido_paterno ?? '', 55);
        $this->escribirAjustado($pdf, 135, 83, $datos->apellido_materno ?? '', 50);
        
        $pdf->SetXY(75, 91); $pdf->Write(0, $datos->curp ?? '');
        $pdf->SetXY(160, 91); $pdf->Write(0, $datos->rfc ?? '');

        // C. Datos Generales
        $pdf->SetXY(25, 94); $pdf->Write(0, $this->toLatin1($datos->tipo_id ?? 'INE'));
        $pdf->SetXY(125, 94); $pdf->Write(0, $datos->numero_id ?? '');
        
        $pdf->SetXY(30, 102); $pdf->Write(0, $this->toLatin1($datos->estado_civil ?? ''));
        $this->escribirAjustado($pdf, 90, 102, $datos->escolaridad ?? '', 40);
        $this->escribirAjustado($pdf, 135, 102, $datos->ocupacion ?? '', 50);

        $pdf->SetXY(105, 109); $pdf->Write(0, $this->toLatin1($datos->tiene_discapacidad ?? 'NO'));
        $this->escribirAjustado($pdf, 157, 109, $datos->cual_discapacidad ?? 'NA', 35);
        
        $pdf->SetXY(105, 113); $pdf->Write(0, $this->toLatin1($datos->grupo_etnico ?? 'NO'));
        $this->escribirAjustado($pdf, 158, 113, $datos->grupo_etnico_cual ?? 'NA', 35);

        // D. Domicilio y Contacto
        $this->escribirAjustado($pdf, 25, 116, $datos->calle ?? '', 45);
        $this->escribirAjustado($pdf, 75, 116, ($datos->pueblo_colonia ?? $datos->colonia_nombre ?? ''), 55);
        $pdf->SetXY(138, 116); $pdf->Write(0, $datos->codigo_postal ?? '');
        
        $pdf->SetXY(40, 124); $pdf->Write(0, $datos->tel_particular ?? '');
        $pdf->SetXY(95, 124); $pdf->Write(0, $datos->tel_casa ?? '');
        $pdf->SetXY(159, 124); $pdf->Write(0, $datos->tel_familiar ?? '');

        // E. Checklist de Requisitos
        if (!empty($datos->check_identidad))   { $pdf->SetXY(180, 141); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_domicilio))   { $pdf->SetXY(180, 145); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_curp_doc))    { $pdf->SetXY(180, 149); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_rfc_doc))     { $pdf->SetXY(180, 154); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_propiedad))   { $pdf->SetXY(180, 158); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_siniiga_doc)) { $pdf->SetXY(180, 162); $pdf->Write(0, 'X'); }
        if (!empty($datos->check_finiquito))   { $pdf->SetXY(180, 166); $pdf->Write(0, 'X'); }

        // F. Producción Primaria
        $pdf->SetXY(85, 180); $pdf->Write(0, $datos->num_total_predios ?? '1');
        $pdf->SetXY(165, 180); $pdf->Write(0, ($datos->superficie_total ?? '0') . ' HA');
        
        $this->escribirAjustado($pdf, 75, 187, $datos->tipo_documento_propiedad ?? '', 100);
        $this->escribirAjustado($pdf, 75, 193, $datos->pueblo_colonia_up ?? '', 80);
        
        // AJUSTE PARAJES (Texto propenso a ser muy largo)
        $this->escribirAjustado($pdf, 165, 193, $datos->parajes ?? '', 35);

        $this->escribirAjustado($pdf, 75, 205, $datos->especie_cultivo_principal ?? '', 80);
        $pdf->SetXY(165, 205); $pdf->Write(0, $datos->numero_cabezas_colmenas ?? '0');

        // --- PÁGINA 2: COMPROMISOS ---
        $tplId2 = $pdf->importPage(2);
        $pdf->addPage();
        $pdf->useTemplate($tplId2);
        
        $nombreFull = trim(($datos->nombre ?? '') . ' ' . ($datos->apellido_paterno ?? '') . ' ' . ($datos->apellido_materno ?? ''));
        
        // Firma Solicitante (Derecha)
        $pdf->SetXY(114, 205); 
        $pdf->SetFont('Arial', 'B', 8); // Fuente un poco más pequeña por seguridad
        $pdf->Cell(80, 0, $this->toLatin1($nombreFull), 0, 0, 'C');

        // Firma Usuario Logueado (Izquierda)
        $pdf->SetXY(25, 205); 
        $usuarioFirma = (!empty($_SESSION['usuario_nombre'])) ? $_SESSION['usuario_nombre'] : 'USUARIO NO IDENTIFICADO';
        $pdf->Cell(80, 0, $this->toLatin1(mb_strtoupper($usuarioFirma, 'UTF-8')), 0, 0, 'C');

        // --- PÁGINA 3: AVISO DE PRIVACIDAD ---
        $tplId3 = $pdf->importPage(3);
        $pdf->addPage();
        $pdf->useTemplate($tplId3);
        
        $pdf->SetFont('Arial', '', 8); 
        $pdf->SetXY(65, 70); $pdf->Write(0, $datos->folio ?? '');
        $pdf->SetXY(160, 68); $pdf->Write(0, date('d/m/Y'));
        
        $pdf->SetFont('Arial', 'B', 8);
        // Firma Solicitante (Abajo Izquierda)
        $pdf->SetXY(25, 209); 
        $pdf->Cell(80, 0, $this->toLatin1($nombreFull), 0, 0, 'C');

        // Firma Usuario Logueado (Abajo Derecha)
        $pdf->SetXY(110, 209); 
        $pdf->Cell(80, 0, $this->toLatin1(mb_strtoupper($usuarioFirma, 'UTF-8')), 0, 0, 'C');

        // Salida del PDF
        $pdf->Output('I', "Solicitud_{$datos->folio}.pdf");
    }

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
            if ($y % 10 == 0) { $pdf->SetXY(2, $y - 2); $pdf->Write(0, "Y=$y"); }
        }
        for ($x = 0; $x <= 210; $x += 5) {
            $pdf->Line($x, 0, $x, 297);
            if ($x % 10 == 0) { $pdf->SetXY($x + 1, 2); $pdf->Write(0, "X=$x"); }
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