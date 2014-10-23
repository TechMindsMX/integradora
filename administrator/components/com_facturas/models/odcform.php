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
class FacturasModelOdcform extends JModelList {

    public function __construct($config = array()) {
        $get = JFactory::getApplication()->input;
        $params = array('odcNum'=>'ALNUM');
        $this->data = $get->getArray($params);
        parent::__construct($config);
    }

    public function getUserIntegrado(){
       $factura = new Integrado();
       $integrados = $factura->getIntegrados();

       return $integrados;
    }

    public function getSolicitud()
    {
        if (!isset($this->dataModelo)) {
            $this->dataModelo = new Integrado;
        }

        return $this->dataModelo;
    }

    public function getOrden(){
        $odcNum = $this->data['odcNum'];

        $data = getFromTimOne::getOrdenesCompra();
        $usuarios = $this->getUserIntegrado();

        foreach($data as $value){
            foreach($usuarios as $usuario){
                if($usuario->integrado_id == $value->integradoId){
                    $value->integradoName = $usuario->name;
                }
            }

            if($value->folio == $odcNum){
                $orden = $value;
            }
        }
        $data = $orden;

        return $data;
    }
}
