<?php
use Integralib\OdVenta;
use Integralib\Txs;

defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');
jimport('integradora.integrado');
jimport('integradora.gettimone');

class AdminIntegradoraModelOdvform extends JModelList {

    public function __construct($config = array()) {
        $get = JFactory::getApplication()->input;
        $params = array('idOrden'=>'INT');
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
        $idOrden = $this->data['idOrden'];
        $data = getFromTimOne::getOrdenesVenta(null,$idOrden);

        foreach($data as $value){
            $value->integradoName = $this->getIntegradoName($value->integradoId);
        }
        return $data[0];
    }

    public function getIntegrados(){
        $integrados = getFromTimOne::getintegrados();

        return $integrados;
    }

    public function getIntegradoName($integardoId){
        $integrados = $this->getIntegrados();

        foreach ($integrados as $value) {
            if($value->integrado->integradoId == $integardoId){
                $return = $value->datos_personales->nom_comercial;
            }
        }
        return $return;
    }

    public function getTransacciones(){
        $return     = array();
        $orden      = $this->getOrden();
        $db         = JFactory::getDbo();
        $query      = $db->getQuery(true);

        $query->select( 'tm.*, bi.referencia, bi.amount, bi.integradoId' )
            ->from($db->quoteName('#__txs_timone_mandato', 'tm'))
            ->join('LEFT', $db->quoteName('#__txs_banco_integrado', 'bi') . ' ON (bi.id = (SELECT rel.id_txs_banco FROM flpmu_txs_banco_timone_relation AS rel WHERE rel.id_txs_timone = tm.id))');

        try{
            $db->setQuery($query);
            $result = $db->loadObjectList();
        }catch (Exception $e){
            var_dump($e);
        }

        foreach ($result as $tx) {
            $tx->balance = $this->getTxBalance($tx);
            if( (($orden->integradoId == $tx->integradoId) || ($tx->integradoId == 0)) && (round($tx->balance,2) > 0) ) {
                $return[] = $tx;
            }
        }

        return $return;
    }

    private function getTxBalance( $trans ) {
        $txs = new Txs();

        return $txs->calculateBalance($trans);
    }
}
