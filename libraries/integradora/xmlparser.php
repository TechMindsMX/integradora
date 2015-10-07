<?php

class xml2Array {

    var $arrOutput = array();
    var $resParser;
    var $strXmlData;

    function parse($strInputXML) {
        $this->resParser = xml_parser_create ();
        xml_set_object($this->resParser,$this);
        xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");

        xml_set_character_data_handler($this->resParser, "tagData");

        $this->strXmlData = xml_parse($this->resParser,$strInputXML );
        if(!$this->strXmlData) {
            die(sprintf("XML error: %s at line %d",
            xml_error_string(xml_get_error_code($this->resParser)),
            xml_get_current_line_number($this->resParser)));
        }

        xml_parser_free($this->resParser);

        return $this->arrOutput;
    }

    function tagOpen($parser, $name, $attrs) {
        $tag=array("name"=>$name,"attrs"=>$attrs);
        array_push($this->arrOutput,$tag);
    }

    function tagData($parser, $tagData) {
        if(trim($tagData)) {
            if(isset($this->arrOutput[count($this->arrOutput)-1]['tagData'])) {
                $this->arrOutput[count($this->arrOutput)-1]['tagData'] .= $tagData;
            }
            else {
                $this->arrOutput[count($this->arrOutput)-1]['tagData'] = $tagData;
            }
        }
    }

    function tagClosed($parser, $name) {
        $this->arrOutput[count($this->arrOutput)-2]['children'][] = $this->arrOutput[count($this->arrOutput)-1];
        array_pop($this->arrOutput);
    }

    function manejaXML($xmlFileData){

        $xml                = $this->parse($xmlFileData);

        $datosXML           = new stdClass();
        $comprobante        = $xml[0]['attrs'];
        $emisor             = $xml[0]['children'][0];
        $receptor           = $xml[0]['children'][1];
        $conceptos          = $xml[0]['children'][2];
        $impuestos          = $xml[0]['children'][3];
        $complemento        = $xml[0]['children'][4];

        foreach($conceptos['children'] as $key => $value){
            $datosXML->conceptos[$key]   = $value['attrs'];
        }
        $this->trasladados($datosXML, $impuestos);

        $datosXML->comprobante                  = $comprobante;
        $datosXML->emisor                       = $emisor;
        $datosXML->receptor                     = $receptor;
        $datosXML->complemento                  = $complemento;

        return $datosXML;
    }

    public function trasladados($datosXML, $impuestos){
        $datosXML->impuestos = new stdClass();
        $datosXML->impuestos->iva = new stdClass();
        $datosXML->impuestos->ieps = new stdClass();

        if(isset($impuestos['attrs']['TOTALIMPUESTOSTRASLADADOS'])){
            $datosXML->impuestos->totalTrasladados  = (FLOAT)$impuestos['attrs']['TOTALIMPUESTOSTRASLADADOS'];
        }else{
            $datosXML->impuestos->totalTrasladados = (FLOAT)$impuestos['children'][0]['children'][0]['attrs']['IMPORTE']+ (FLOAT)$impuestos['children'][0]['children'][1]['attrs']['IMPORTE'];
        }

        foreach($impuestos['children'][0]['children'] as $key => $value){
            if($value['attrs']['IMPUESTO'] == 'IVA' && $value['attrs']['TASA'] != 0){
                $datosXML->impuestos->iva->tasa    += (INT)$value['attrs']['TASA'];
                $datosXML->impuestos->iva->importe += (FLOAT)$value['attrs']['IMPORTE'];
            }elseif($value['attrs']['IMPUESTO'] == 'IEPS' && $value['attrs']['TASA'] != 0){
                $datosXML->impuestos->ieps->tasa    += (INT)$value['attrs']['TASA'];
                $datosXML->impuestos->ieps->importe += (FLOAT)$value['attrs']['IMPORTE'];
            }
        }

        if( empty($datosXML->impuestos->ieps) ){
            $datosXML->impuestos->ieps->tasa    = 0;
            $datosXML->impuestos->ieps->importe = 0;
        }
    }
}