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
     * 🔥 FUNCIÓN MEJORADA (NO rompe tu sistema, solo lo mejora)
     */
    private function escribirAjustado($pdf, $x, $y, $texto, $anchoMax, $fuenteBase = 8, $esMulti = false) {
        $textoFinal = $this->toLatin1($texto);
        $fuente = $fuenteBase;
        $lineHeight = 2.8;

        $pdf->SetFont('Arial', '', $fuente);

        // 🔹 Divide texto sin cortar palabras
        $wrapTexto = function($pdf, $texto, $ancho) {
            $palabras = explode(' ', $texto);
            $lineas = [];
            $lineaActual = '';

            foreach ($palabras as $palabra) {
                $testLinea = ($lineaActual == '') ? $palabra : $lineaActual . ' ' . $palabra;
                
                if ($pdf->GetStringWidth($testLinea) <= $ancho) {
                    $lineaActual = $testLinea;
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

        // 🔹 Generar líneas
        $lineas = $wrapTexto($pdf, $textoFinal, $anchoMax);

        // 🔹 Reducir fuente si hay más de 2 líneas
        while (count($lineas) > 2 && $fuente > 6) {
            $fuente -= 0.3;
            $pdf->SetFont('Arial', '', $fuente);
            $lineas = $wrapTexto($pdf, $textoFinal, $anchoMax);
        }

        if ($esMulti) {
            // 🔹 Máximo 2 líneas
            $lineas = array_slice($lineas, 0, 2);

            // 🔹 Centrado vertical
            $numLineas = count($lineas);
            $alturaTotal = $numLineas * $lineHeight;

            $alturaCaja = 6; // altura del recuadro
            $yAjustado = $y + (($alturaCaja - $alturaTotal) / 2);

            $pdf->SetXY($x, $yAjustado);

            foreach ($lineas as $linea) {
                $pdf->Cell($anchoMax, $lineHeight, $linea, 0, 2, 'L');
            }

        } else {
            // 🔹 Ajuste de fuente para una sola línea
            while ($pdf->GetStringWidth($textoFinal) > $anchoMax && $fuente > 6) {
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
        
        // --- PÁGINA 1 ---
        $tplId = $pdf->importPage(1);
        $pdf->addPage();
        $pdf->useTemplate($tplId);
        $pdf->SetTextColor(0, 0, 0);

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(65, 53); $pdf->Write(0, $datos->folio ?? '');
        $pdf->SetXY(157, 55); $pdf->Write(0, date('d/m/Y'));

        // Identidad
        $this->escribirAjustado($pdf, 25, 83, $datos->nombre ?? '', 45);
        $this->escribirAjustado($pdf, 75, 83, $datos->apellido_paterno ?? '', 55);
        $this->escribirAjustado($pdf, 135, 83, $datos->apellido_materno ?? '', 50);
        
        $pdf->SetXY(75, 91); $pdf->Write(0, $datos->curp ?? '');
        $pdf->SetXY(160, 91); $pdf->Write(0, $datos->rfc ?? '');

        // Generales
        $pdf->SetXY(25, 94); $pdf->Write(0, $this->toLatin1($datos->tipo_id ?? 'INE'));
        $pdf->SetXY(125, 94); $pdf->Write(0, $datos->numero_id ?? '');
        $pdf->SetXY(30, 102); $pdf->Write(0, $this->toLatin1($datos->estado_civil ?? ''));
        $this->escribirAjustado($pdf, 90, 102, $datos->escolaridad ?? '', 40);
        $this->escribirAjustado($pdf, 135, 102, $datos->ocupacion ?? '', 50);

        // Domicilio
        $this->escribirAjustado($pdf, 25, 116, $datos->calle ?? '', 45);
        $this->escribirAjustado($pdf, 75, 116, ($datos->pueblo_colonia ?? $datos->colonia_nombre ?? ''), 55);
        $pdf->SetXY(138, 116); $pdf->Write(0, $datos->codigo_postal ?? '');

        // Producción
        $pdf->SetXY(85, 180); $pdf->Write(0, $datos->num_total_predios ?? '1');
        $pdf->SetXY(165, 180); $pdf->Write(0, ($datos->superficie_total ?? '0') . ' HA');
        
        $this->escribirAjustado($pdf, 75, 187, $datos->tipo_documento_propiedad ?? '', 100);
        $this->escribirAjustado($pdf, 75, 193, $datos->pueblo_colonia_up ?? '', 75);

        // 🔥 PARAJE (YA PERFECTO)
        $this->escribirAjustado($pdf, 145, 193, $datos->parajes ?? '', 50, 7, true);

        $this->escribirAjustado($pdf, 75, 198, $datos->tenencia_tierra ?? 'NA', 45);
        $this->escribirAjustado($pdf, 75, 206, $datos->especie_cultivo_principal ?? '', 80);
        $pdf->SetXY(165, 206); $pdf->Write(0, $datos->numero_cabezas_colmenas ?? '0');

        $pdf->Output('I', "Solicitud_{$datos->folio}.pdf");
    }
}