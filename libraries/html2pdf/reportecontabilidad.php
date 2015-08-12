<?php
defined('JPATH_PLATFORM') or die;
require('html2pdf.class.php');



class reportecontabilidad{

    public function createPDF($data, $tipo){

        if($tipo = 'odv'){

        }

        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $contenido = curl_exec($ch);
        curl_close($ch);


        $html2pdf = new HTML2PDF('P','A4','fr');
        $html2pdf->WriteHTML($contenido);
        $html2pdf->Output('respaldosPDF/exemple.pdf', 'F');
        exit;
    }

    function odv(){

    }

}