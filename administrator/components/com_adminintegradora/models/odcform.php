<?php
defined('_JEXEC') or die;
jimport('integradora.gettimone');

/**
 * Methods supporting a list of Facturas records.
 */
class AdminintegradoraModelOdcform extends JModelList
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
//        $data->factura = getFromTimOne::getDataFactura($data);
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
	    $return = '';
        $orden = $this->getOrden();
        $respuesta = getFromTimOne::getTxIntegradoSinMandato();

	    if (!empty($respuesta)) {
		    foreach ($respuesta as $tx) {
			    if( ( ($orden->integradoId == $tx->integradoId) || ($tx->integradoId == 0) ) && $tx->conciliacionMandato == 0 ) {
				    $return[] = $tx;
			    }
		    }
	    }

        return $return;
    }
}