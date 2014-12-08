<?php
defined('_JEXEC') or die;

jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');
jimport('integradora.classDB');

class conciliacionAdminController extends JControllerLegacy {
    public function __construct($config = array()) {
        $get            = JFactory::getApplication()->input;
        $params         = array('claveBanco'=>'ALNUM');
        $this->data     = $get->getArray($params);
        $this->document =  $document = JFactory::getDocument();
        parent::__construct($config);
    }

    public function display($cachable = false, $urlparams = false) {
        require_once JPATH_COMPONENT . '/helpers/conciliacionadmin.php';
        $view = JFactory::getApplication()->input->getCmd('view', 'facturas');
        JFactory::getApplication()->input->set('view', $view);
        parent::display($cachable, $urlparams);
        return $this;
    }

    public function cuentas() {
        $claveBanco = $this->data['claveBanco'];
        $this->document->setMimeEncoding('application/json');
        $cuentas = getFromTimOne::getNumCuenta();

        foreach ($cuentas as $key => $value) {
            if($claveBanco == $value->banco){
                $respuesta[] = $value;
            }
        }

        $response['data'] = $respuesta;

        echo json_encode($response);
    }

    public function conciliar(){
        $save = new sendToTimOne();
        $post = array(
            'idTx'          => 'INT',
            'type'          => 'STRING',
            'idOrden'       => 'INT',
            'integradoId'   => 'INT',
            'ordenPagada'   => 'INT',
            'referencia'    => 'STRING',
            'cuenta'        => 'STRING',
            'date'          => 'STRING',
            'amount'        => 'FLOAT'
        );
        $data = (object) JFactory::getApplication()->input->getArray($post);

        if($data->idTx == 0){
            $changeDateType = new DateTime($data->date);
            $fecha = $changeDateType->getTimestamp();

            $datos = array(
                'cuenta'      =>$data->cuenta,
                'referencia'  =>$data->referencia,
                'date'        =>$fecha,
                'amount'      =>$data->amount,
                'integradoId' =>$data->integradoId
            );

            $save->formatData($datos);
            $resultado = $save->insertDB('conciliacion_banco_integrado',null,null,true);

            if($resultado !== false){
                $data->idTx = $resultado;
            }
        }

        $remainder = getFromTimOne::getRemainderOrder($data->idOrden, $data->type, $data->amount);

        $datosConciliacion = array(
            'idTx'      => $data->idTx,
            'idOrden'   => $data->idOrden,
            'type'      => $data->type,
            'paid'      => $data->ordenPagada,
            'remainder' => $remainder
        );

        $save->formatData($datosConciliacion);

        $resultado = $save->insertDB('tx_orden');

        if($resultado){
            JFactory::getApplication()->redirect('index.php?option=com_conciliacionadmin&view=oddlist');
        }else{
            JFactory::getApplication()->enqueueMessage(JText::_('com_comciliacionadmin_error_save'),'error');
        }
    }
}
