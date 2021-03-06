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
    protected $catalogos;

    public function __construct(){
        $post              = array( 'idOrden' => 'INT');
        $this->inputVars   = JFactory::getApplication()->input->getArray($post);
        $this->id          = $this->inputVars['idOrden'];

        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

        parent::__construct();
    }

    public function getOrden(){
        if (!isset($this->dataModelo)) {
            $this->dataModelo = getFromTimOne::getOrdenesCompra($this->integradoId, $this->id);
        }
        $this->dataModelo = $this->dataModelo[0];

        $dataxml = $this->getdata2xml($this->dataModelo->urlXML);

        foreach ($dataxml->conceptos as $key => $value) {
            $this->dataModelo->productos[] = $value;
        }
        $this->dataModelo->dataxml = $dataxml;

        return $this->dataModelo;
    }

    public function getProyectos(){
        $proyectos = getFromTimOne::getActiveProyects($this->integradoId);

        foreach($proyectos as $proyecto){
            $proyecto->subproyectos = getFromTimOne::getActiveSubProyects($proyecto->id_proyecto);
        }

        return $proyectos;
    }

    public function getProviders(){
        $proveedores = getFromTimOne::getClientes($this->integradoId,1);
        //TODO: traer siempre los clientes/proveedores como objetos integrado
        $respuesta = array();

        foreach ( $proveedores as $key =>$value ) {
            $integ = new IntegradoSimple($value->id);
            $integ->displayName = $integ->getDisplayName();

            $proveedores[$key] = $integ;

            if($value->status != 0) {
                $respuesta[$key] = $proveedores[$key];
            }
        }

        return $respuesta;
    }

    public function getdata2xml($urlFile = null){
        if(is_null($urlFile)) {
            move_uploaded_file($_FILES['factura']['tmp_name'], "media/archivosJoomla/" . $_FILES['factura']['name']);
            $urlFile = "media/archivosJoomla/" . $_FILES['factura']['name'];
        }
        $xmlFileData    = file_get_contents(JPATH_ROOT.DIRECTORY_SEPARATOR.$urlFile);
        $data 			= new xml2Array();
        $datos 			= $data->manejaXML($xmlFileData);
        $datos->urlXML = $urlFile;

        return $datos;
    }

    public function getCatalogoBancos(){
        $this->catalogos = new Catalogos();

        return $this->catalogos->getBancos();
    }

    public function getCatalogos( ){
        return $this->catalogos->getPaymentMethods();

    }
}

