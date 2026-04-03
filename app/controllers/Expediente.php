<?php
// Carga del autoloader de Composer
require_once '../vendor/autoload.php'; 

use setasign\Fpdi\Fpdi;

class Expediente extends Controller {
    protected $encuestaModel;

    public function __construct() {
        $this->encuestaModel = $this->model('EncuestaModelo'); 
    }

    /**
     * Función auxiliar para convertir texto a formato PDF (ISO-8859-1)
     * sin usar la función obsoleta utf8_decode.
     */
    private function toLatin1($txt) {
        if ($txt === null) return '';
        return iconv('UTF-8', 'windows-1252//TRANSLIT', $txt);
    }

    public function imprimirSolicitud($id) {
        // Limpiar cualquier salida previa para evitar el error "Some data has already been output"
        if (ob_get_contents()) ob_end_clean();

        $datos = $this->encuestaModel->getExpedienteCompleto($id);
        
        if(!$datos) {
            die("Error: Expediente no encontrado.");
        }
        
        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);
        $pdf->SetTitle("Solicitud_" . $datos->folio);

        // --- PÁGINA 1 ---
        $rutaTemplate = APPROOT . '/views/formatos/formatoProductores2026.pdf'; 
        $pdf->setSourceFile($rutaTemplate);
        $tplId = $pdf->importPage(1);
        $pdf->addPage();
        $pdf->useTemplate($tplId);

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(0, 0, 0);

        // A. Encabezado
        $pdf->SetXY(160, 23); $pdf->Write(0, $datos->folio);
        $pdf->SetXY(160, 48); $pdf->Write(0, date('d/m/Y', strtotime($datos->fecha_inicio)));

        // B. Identidad (Usando los nombres de columna REALES de tu base de datos)
        $pdf->SetXY(20, 68);  $pdf->Write(0, $this->toLatin1($datos->nombre));
        $pdf->SetXY(80, 68);  $pdf->Write(0, $this->toLatin1($datos->apellido_paterno));
        $pdf->SetXY(140, 68); $pdf->Write(0, $this->toLatin1($datos->apellido_materno));
        $pdf->SetXY(20, 78);  $pdf->Write(0, $datos->curp);
        $pdf->SetXY(140, 78); $pdf->Write(0, $datos->rfc);

        // C. Datos Generales (Corrigiendo propiedades: escolaridad y codigo_postal)
        $pdf->SetXY(20, 88);  $pdf->Write(0, $this->toLatin1($datos->tipo_id));
        $pdf->SetXY(110, 88); $pdf->Write(0, $this->toLatin1($datos->numero_id));
        
        $pdf->SetXY(20, 98);  $pdf->Write(0, $this->toLatin1($datos->estado_civil));
        $pdf->SetXY(80, 98);  $pdf->Write(0, $this->toLatin1($datos->escolaridad)); // Antes: grado_estudios
        $pdf->SetXY(140, 98); $pdf->Write(0, $this->toLatin1($datos->ocupacion));

        // Discapacidad y Etnia
        $pdf->SetXY(55, 103); $pdf->Write(0, $this->toLatin1($datos->tiene_discapacidad . " / " . $datos->cual_discapacidad));
        $pdf->SetXY(55, 108); $pdf->Write(0, $this->toLatin1($datos->grupo_etnico . " / " . $datos->grupo_etnico_cual));

        // D. Domicilio (Corrigiendo: codigo_postal)
        $pdf->SetXY(20, 118); $pdf->Write(0, $this->toLatin1($datos->calle));
        $pdf->SetXY(80, 118); $pdf->Write(0, $this->toLatin1($datos->colonia_nombre));
        $pdf->SetXY(175, 118); $pdf->Write(0, $datos->codigo_postal); // Antes: cp
        
        $pdf->SetXY(20, 128); $pdf->Write(0, $datos->tel_particular);
        $pdf->SetXY(80, 128); $pdf->Write(0, $datos->tel_casa);
        $pdf->SetXY(140, 128); $pdf->Write(0, $datos->tel_familiar);

        // E. Checklist de Requisitos
        $baseY = 138;
        if($datos->check_identidad) { $pdf->SetXY(192, $baseY); $pdf->Write(0, 'X'); }
        if($datos->check_domicilio) { $pdf->SetXY(192, $baseY + 7); $pdf->Write(0, 'X'); }
        if($datos->check_curp_doc)  { $pdf->SetXY(192, $baseY + 14); $pdf->Write(0, 'X'); }
        if($datos->check_rfc_doc)   { $pdf->SetXY(192, $baseY + 21); $pdf->Write(0, 'X'); }
        if($datos->check_propiedad) { $pdf->SetXY(192, $baseY + 28); $pdf->Write(0, 'X'); }
        if($datos->check_siniiga_doc){ $pdf->SetXY(192, $baseY + 35); $pdf->Write(0, 'X'); }
        if($datos->check_finiquito) { $pdf->SetXY(192, $baseY + 42); $pdf->Write(0, 'X'); }

        // F. Producción Primaria
        $pdf->SetXY(35, 195); $pdf->Write(0, $datos->num_total_predios);
        $pdf->SetXY(135, 195); $pdf->Write(0, $datos->superficie_total . ' ha');
        $pdf->SetXY(35, 205); $pdf->Write(0, $this->toLatin1($datos->tipo_documento_propiedad));
        $pdf->SetXY(35, 215); $pdf->Write(0, $this->toLatin1($datos->pueblo_colonia_up));
        $pdf->SetXY(135, 215); $pdf->Write(0, $this->toLatin1($datos->parajes));
        $pdf->SetXY(35, 225); $pdf->Write(0, $this->toLatin1($datos->tenencia_tierra));
        $pdf->SetXY(35, 235); $pdf->Write(0, $this->toLatin1($datos->especie_cultivo_principal));
        $pdf->SetXY(160, 235); $pdf->Write(0, $datos->numero_cabezas_colmenas);

        // --- PÁGINA 2 ---
        $tplId2 = $pdf->importPage(2);
        $pdf->addPage();
        $pdf->useTemplate($tplId2);
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(110, 258); 
        $nombreComp = $datos->nombre . ' ' . $datos->apellido_paterno . ' ' . $datos->apellido_materno;
        $pdf->Cell(80, 0, $this->toLatin1($nombreComp), 0, 0, 'C');

        // --- PÁGINA 3 ---
        $tplId3 = $pdf->importPage(3);
        $pdf->addPage();
        $pdf->useTemplate($tplId3);
        
        $pdf->SetXY(20, 258); 
        $pdf->Cell(80, 0, $this->toLatin1($nombreComp), 0, 0, 'C');

        $pdf->Output('I', "Solicitud_{$datos->folio}.pdf");
    }
}