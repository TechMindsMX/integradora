<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modelitem');

jimport('integradora.integrado');
jimport('integradora.rutas');
jimport('integradora.catalogos');
jimport('integradora.xmlparser');

/**
 * Modelo de datos para Formulario p/generar Ordenes de Compra de un integrado
 */
class MandatosModelOdcform extends JModelItem {
    protected $dataModelo;

    public function __construct(){
        $post              = array('integradoId' => 'INT', 'numOrden' => 'INT');
        $this->inputVars   = JFactory::getApplication()->input->getArray($post);
        $this->integradoId = $this->inputVars['integradoId'];
        $this->numOrden    = $this->inputVars['numOrden'];
        parent::__construct();
    }

    public function getOrden(){
        if (!isset($this->dataModelo)) {
            $this->dataModelo = getFromTimOne::getOrdenesCompra($this->integradoId, $this->numOrden);
        }

        $dataxml = $this->getdata2xml($this->dataModelo->urlXML);

        foreach ($dataxml->conceptos as $key => $value) {
            $this->dataModelo->productos[] = $value;
        }
        $this->dataModelo->dataxml = $dataxml;

        return $this->dataModelo;
    }

    public function getProyectos(){
        $proyectos = getFromTimOne::getProyects($this->integradoId);

        return $proyectos;
    }

    public function getProviders(){
        $proveedores = getFromTimOne::getClientes($this->integradoId,1);

        return $proveedores;
    }

    public function getdata2xml($urlFile = null){
        if(is_null($urlFile)) {
            move_uploaded_file($_FILES['factura']['tmp_name'], "media/archivosJoomla/" . $_FILES['factura']['name']);
            $urlFile = "media/archivosJoomla/" . $_FILES['factura']['name'];
        }
        $xmlFileData    = file_get_contents($urlFile);
        $data 			= new xml2Array();
        $datos 			= $data->manejaXML($xmlFileData);
        $datos->urlXML = $urlFile;

        return $datos;
    }
}

