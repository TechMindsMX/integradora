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
class conciliacionadminModelfactform extends JModelList {

    public function __construct($config = array()) {
        $get = JFactory::getApplication()->input;
        $params = array('factNum'=>'ALNUM');
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

    public function getFactura(){
        $factNum = $this->data['factNum'];

        $data = getFromTimOne::getFactura();
        $usuarios = $this->getUserIntegrado();

        foreach($data as $value){
            foreach($usuarios as $usuario){
                if($usuario->integrado_id == $value->integradoId){
                    $value->integradoName = $usuario->name;
                }
            }

            if($value->id == $factNum){
                $factura = $value;
            }
        }

        return $factura;
    }

    public function getNumCuentas () {
        $bancos = $this->getBancos();
        $cuentas = getFromTimOne::getNumCuenta();

        foreach($cuentas as $objeto){
            foreach($bancos as $objBanco){
                if($objeto->banco == $objBanco->clave){
                    $objeto->nomBanco = $objBanco->banco;
                }
            }
            $select[$objeto->nomBanco] = $objeto->banco;
        }
        $respuesta['select'] = $select;

        return $respuesta;
    }

    private function getBancos(){
        $catalogos = new Catalogos();
        $catalogos->getBancos();

        return $catalogos->bancos;
    }

}
