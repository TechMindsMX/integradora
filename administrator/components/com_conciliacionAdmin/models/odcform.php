<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');
jimport('integradora.integrado');
jimport('integradora.catalogos');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');
jimport('integradora.classDB');

/**
 * Methods supporting a list of Facturas records.
 */
class conciliacionadminModelOdcform extends JModelList
{

    public function __construct($config = array())
    {
        $get = JFactory::getApplication()->input;
        $params = array('idOrden' => 'INT');
        $this->data = $get->getArray($params);
        parent::__construct($config);
    }

    public function getOrden(){
        $odcNum = $this->data['idOrden'];

        $data = getFromTimOne::getOrdenesCompra(null, $odcNum);
        $data = $data[0];
        $data->url_xml = $data->urlXML;
        unset ($data->urlXML);
        $data->factura = getFromTimOne::getDataFactura($data);
        $data->integradoName = $this->getIntegradoName($data->integradoId);

        return $data;
    }

    public function getIntegrados(){
        $integrados = getFromTimOne::getintegrados();

        return $integrados;
    }

    public function getIntegradoName($integardoId){
        $integrados = $this->getIntegrados();

        foreach ($integrados as $value) {
            if($value->integrado->integrado_id == $integardoId){
                $return = $value->datos_personales->nom_comercial;
            }
        }
        return $return;
    }

    public function getTransacciones(){
        $orden = $this->getOrden();
        $txs = getFromTimOne::getTxIntegradoSinMandato($orden->integradoId);

        foreach ($txs as $value) {
            if(is_null($value->conciliacionMandato)){
                $respuesta[] = $value;
            }
        }

        return $respuesta;
    }
}