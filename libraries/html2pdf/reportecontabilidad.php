<?php
defined('JPATH_PLATFORM') or die;
require('loader.php');

class reportecontabilidad{

    protected $fecha;

    function __construct($integ_id = null) {

        $integradora = new \Integralib\Integrado();
        $this->integradora = new IntegradoSimple($integradora->getIntegradoraUuid() );

        $session = JFactory::getSession();
        $this->integradoId 	= $session->get('integradoId', null, 'integrado');

        $integrado = new IntegradoSimple($this->integradoId);

        $integrado->getTimOneData();

        $this->integCurrent = $integrado;
    }

    public function facturaPDF($data, $facObjOdv, $facObj, $xml) {

        $this->fecha = $facObjOdv->createdDate;

        $fileName = explode('/', $xml);
        $fileName = explode('.', $fileName[2]);
        $path = JPATH_BASE.'/media/facturas/'.$fileName[0].'-'.$this->fecha.'-'.$facObjOdv->id.'.pdf';
        $createHtml = new Facpdf();
        $html = $createHtml->html($data, $this->integradora, $facObjOdv, $facObj);
        $html2pdf = new HTML2PDF();
        $html2pdf->WriteHTML($html);
        $html2pdf->Output($path, 'F');
        return $path;
    }

    public function createPDF($data, $tipo)
    {
        set_time_limit(180);

        list($html, $path) = $this->selectTipeOrder($data, $tipo);

        $html2pdf = new HTML2PDF();
        $html2pdf->WriteHTML($html);
        $html2pdf->Output($path, 'F');
        $this->path = $path ;
    }

    public  function readCss(){
        $this->readFile('<link type="text/css" href="'.JUri::base().'/templates/meet_gavern/css/template.css" rel="stylesheet">');
        $this->readFile('<link type="text/css" href="'.JUri::base().'/templates/meet_gavern/css/override.css" rel="stylesheet">');
        return $this->readFile(JUri::base().'/templates/meet_gavern/bootstrap/output/bootstrap.css');
    }

    /**
     * @param $data
     * @param $tipo
     * @return array
     */
    public function selectTipeOrder($data, $tipo)
    {
        $path = '';
        switch ($tipo) {
            case 'odv':
                $getHtml = new odvPdf();
                $orden = getFromTimOne::getOrdenesVenta($this->integradoId, $data);
                $html = $getHtml->odv($orden);
                $path = 'media/pdf_odv/'.$this->integradoId.'-'.$orden[0]->createdDate.'-'.$orden[0]->numOrden.'.pdf';
                break;
            case 'odc':
                $getHtml = new odcPdf();
                $orden = getFromTimOne::getOrdenesCompra(null, $data);
                $orden = $orden[0];
                $html = $getHtml->html($orden);
                $path = 'media/pdf_odc/'.$this->integradoId.'-'.$orden->createdDate.'-'.$orden->numOrden.'.pdf';
                break;
            case 'odd':
                $getHtml = new oddPdf($data);
                $html = $getHtml->createHTML();
                $path = 'media/pdf_odd/'.$this->integradoId.'-'.$data->createdDate.'-'.$data->numOrden.'.pdf';
                break;
            case 'odr':
                $getHtml = new odrPdf($data);
                $html = $getHtml->createHTML();
                $path = 'media/pdf_odr/'.$this->integradoId.'-'.$data->createdDate.'-'.$data->numOrden.'.pdf';
                break;
            case 'odp':
                $getHtml = new odpPdf($data);
                $html = $getHtml->createHTML();
                $path = 'media/pdf_odp/'.$this->integradoId.'-'.$data->createdDate.'-'.$data[0]->idMutuo.'.pdf';
                break;
           case 'mutuo':
                $getHtml = new mutuosPDF($data);
                $html = $getHtml->generateHtml($data);
                $path = 'media/pdf_mutuo/'.$this->integradoId.'-'.$data->createdDate.'-'.$data->id.'.pdf';
                break;
            case 'cashout':
                $getHtml = new cashoutPDF($data);
                $html = $getHtml->generateHtml();
                $path = 'media/pdf_cashOut/'.$this->integradoId.'-'.$this->fecha.'-'.$data->id.'.pdf';
                break;
            case 'cashin':
                $getHtml = new cashinPDF();
                $html = $getHtml->generateHtml($data);
                $path = 'media/pdf_cashIn/'.$this->integradoId.'-'.$this->fecha.'-'.'.pdf';
                break;
            case 'flujo':
                $getHtml = new flujoPDF();
                $html = $getHtml->generateHtml($data);
                $path = 'media/pdf_flujo/'.$this->integradoId.'-'.$this->fecha.'-start '.$data->this->fechas['startDate'].'-end '.$data->this->fechas['endDate'].'.pdf';
                break;
            case 'result':
                $getHtml = new resultPDF(null, $data);
                $html = $getHtml->generateHtml();
                $path = 'media/pdf_result/'.$this->integradoId.'-'.$this->fecha.'-start '.$data->inicio.'-end '.$data->fin.'.pdf';
                break;
            default:
                $operacion = '';
                return array($operacion, $path);
        }


        return array($html, $path);
    }

    public  function readFile ($url){

        $file = fopen($url, "r") or exit("Unable to open file!");
        while(!feof($file)) {
            $this->css .= fgets($file);
            $this->css = str_replace("inherit", "", $this->css);
        }
        return $this->css;

        fclose($file);
    }
}