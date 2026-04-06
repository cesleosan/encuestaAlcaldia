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
        $txtMayus = mb_strtoupper($txt, 'UTF-8');
        return iconv('UTF-8', 'windows-1252//TRANSLIT', $txtMayus);
    }

    /**
     * Escribe texto ajustando el tamaño de fuente.
     * Implementa lógica para MultiCell (Paraje) centrando verticalmente el bloque.
     */
    private function escribirAjustado($pdf, $x, $y, $texto, $anchoMax, $fuenteBase = 8, $esMulti = false) {
    $textoFinal = $this->toLatin1($texto);
    $fuente = $fuenteBase;
    $lineHeight = 2.8;

    $pdf->SetFont('Arial', '', $fuente);

    // 🔹 NUEVO: función para dividir texto sin cortar palabras
    $wrapTexto = function($pdf, $texto, $ancho) {
        $palabras = explode(' ', $texto);
        $lineas = [];
        $lineaActual = '';

        foreach ($palabras as $palabra) {
            $test = ($lineaActual == '') ? $palabra : $lineaActual . ' ' . $palabra;

            if ($pdf->GetStringWidth($test) <= $ancho) {
                $lineaActual = $test;
            } else {
                if ($lineaActual != '') {
                    $lineas[] = $lineaActual;
                }
                $lineaActual = $palabra;
            }
        }

        if ($lineaActual != '') {
            $lineas[] = $lineaActual;
        }

        return $lineas;
    };

    // 🔹 Si es multicelda (PARAJE)
    if ($esMulti) {

        // Generar líneas correctamente
        $lineas = $wrapTexto($pdf, $textoFinal, $anchoMax);

        // Reducir fuente si se pasa de 2 líneas
        while (count($lineas) > 2 && $fuente > 6) {
            $fuente -= 0.3;
            $pdf->SetFont('Arial', '', $fuente);
            $lineas = $wrapTexto($pdf, $textoFinal, $anchoMax);
        }

        // Máximo 2 líneas (evita desbordes)
        $lineas = array_slice($lineas, 0, 2);

        // 🔹 Centrado vertical REAL (mejor que tu ajuste manual)
        $numLineas = count($lineas);
        $alturaTotal = $numLineas * $lineHeight;
        $alturaCaja = 6; // puedes ajustar a 5.5 si quieres más precisión
        $yFinal = $y + (($alturaCaja - $alturaTotal) / 2);

        $pdf->SetXY($x, $yFinal);

        foreach ($lineas as $linea) {
            $pdf->Cell($anchoMax, $lineHeight, $linea, 0, 2, 'L');
        }

    } else {
        // 🔹 TU LÓGICA ORIGINAL (solo mejorada)
        while($pdf->GetStringWidth($textoFinal) > $anchoMax && $fuente > 6) {
            $fuente -= 0.3;
            $pdf->SetFont('Arial', '', $fuente);
        }

        $pdf->SetXY($x, $y);
        $pdf->Write(0, $textoFinal);
    }

    // Reset
    $pdf->SetFont('Arial', '', 8);
}

    public function imprimirSolicitud($id) {
        if (ob_get_level()) ob_end_clean();

        $datos = $this->encuestaModel->getExpedienteCompleto($id);
        if (!$datos) die("Error: Expediente no encontrado.");

        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);
        $pdf->SetTitle("Solicitud_" . ($datos->folio ?? 'SF'));

        $rutaTemplate = APPROOT . '/views/formatos/formatoProductores2026.pdf'; 
        $pdf->setSourceFile($rutaTemplate);
        
        // --- PÁGINA 1: DATOS ---
        $tplId = $pdf->importPage(1);
        $pdf->addPage();
        $pdf->useTemplate($tplId);
        $pdf->SetTextColor(0, 0, 0);

        // A. Encabezado
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(65, 53); $pdf->Write(0, $datos->folio ?? '');
        $pdf->SetXY(157, 55); $pdf->Write(0, date('d/m/Y'));

        // B. Identidad
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

        // D. Domicilio
        $this->escribirAjustado($pdf, 25, 116, $datos->calle ?? '', 45);
        $this->escribirAjustado($pdf, 75, 116, ($datos->pueblo_colonia ?? $datos->colonia_nombre ?? ''), 55);
        $pdf->SetXY(138, 116); $pdf->Write(0, $datos->codigo_postal ?? '');
        
        $pdf->SetXY(40, 124); $pdf->Write(0, $datos->tel_particular ?? '');
        $pdf->SetXY(95, 124); $pdf->Write(0, $datos->tel_casa ?? '');
        $pdf->SetXY(159, 124); $pdf->Write(0, $datos->tel_familiar ?? '');

        // E. Checklist
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
        $this->escribirAjustado($pdf, 75, 193, $datos->pueblo_colonia_up ?? '', 75);
        
// Coordenada X=150 (más a la izquierda), Y=193, Ancho=50, Fuente inicial=7
$this->escribirAjustado($pdf, 145, 193, $datos->parajes ?? '', 50, 7, true);

        // TENENCIA DE LA TIERRA
        $this->escribirAjustado($pdf, 75, 198, $datos->tenencia_tierra ?? 'NA', 45);

        $this->escribirAjustado($pdf, 75, 206, $datos->especie_cultivo_principal ?? '', 80);
        $pdf->SetXY(165, 206); $pdf->Write(0, $datos->numero_cabezas_colmenas ?? '0');

        // --- PÁGINA 2: FIRMAS ---
        $tplId2 = $pdf->importPage(2);
        $pdf->addPage();
        $pdf->useTemplate($tplId2);
        $nombreFull = trim(($datos->nombre ?? '') . ' ' . ($datos->apellido_paterno ?? '') . ' ' . ($datos->apellido_materno ?? ''));
        
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(114, 205); $pdf->Cell(80, 0, $this->toLatin1($nombreFull), 0, 0, 'C');
        
        // Firma Usuario Logueado (Izquierda) - Vacío si no hay sesión
        $pdf->SetXY(25, 205); 
        $usuarioFirma = (!empty($_SESSION['usuario_nombre'])) ? $_SESSION['usuario_nombre'] : '';
        $pdf->Cell(80, 0, $this->toLatin1(mb_strtoupper($usuarioFirma, 'UTF-8')), 0, 0, 'C');

        // --- PÁGINA 3: PRIVACIDAD ---
        $tplId3 = $pdf->importPage(3);
        $pdf->addPage();
        $pdf->useTemplate($tplId3);
        $pdf->SetFont('Arial', '', 8); 
        $pdf->SetXY(65, 70); $pdf->Write(0, $datos->folio ?? '');
        $pdf->SetXY(160, 68); $pdf->Write(0, date('d/m/Y'));
        
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(25, 209); $pdf->Cell(80, 0, $this->toLatin1($nombreFull), 0, 0, 'C');
        
        // Firma Usuario Logueado (Abajo Derecha) - Vacío si no hay sesión
        $pdf->SetXY(110, 209); 
        $pdf->Cell(80, 0, $this->toLatin1(mb_strtoupper($usuarioFirma, 'UTF-8')), 0, 0, 'C');

        $pdf->Output('I', "Solicitud_{$datos->folio}.pdf");
    }

    public function calibrar($id) {
        if (ob_get_level()) ob_end_clean();
        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);
        $pdf->setSourceFile(APPROOT . '/views/formatos/formatoProductores2026.pdf');
        $pdf->addPage();
        $pdf->useTemplate($pdf->importPage(1));
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetDrawColor(255, 0, 0); $pdf->SetTextColor(255, 0, 0);
        for ($y = 0; $y <= 297; $y += 5) { $pdf->Line(0, $y, 210, $y); if ($y % 10 == 0) { $pdf->SetXY(2, $y - 2); $pdf->Write(0, "Y=$y"); } }
        for ($x = 0; $x <= 210; $x += 5) { $pdf->Line($x, 0, $x, 297); if ($x % 10 == 0) { $pdf->SetXY($x + 1, 2); $pdf->Write(0, "X=$x"); } }
        $pdf->Output('I', 'CALIBRACION.pdf');
    }
}