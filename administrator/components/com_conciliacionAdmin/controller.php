<?php
defined('_JEXEC') or die;

jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');
jimport('integradora.classDB');
jimport('integradora.notifications');

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
        $view = JFactory::getApplication()->input->getCmd('view', 'odclist');
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

        $changeDateType = new DateTime($data->date);
        $fecha = $changeDateType->getTimestamp();

        $datosTx = array(
            'cuenta'              =>$data->cuenta,
            'referencia'          =>$data->referencia,
            'date'                =>$fecha,
            'amount'              =>$data->amount,
            'integradoId'         =>$data->integradoId,
            'conciliacionMandato' => 1
        );

        if($data->idTx == 0){
            $save->formatData($datosTx);
            $resultado = $save->insertDB('conciliacion_banco_integrado',null,null,true);

            if($resultado !== false){
                $data->idTx = $resultado;
            }
        }else{
            $save->formatData($datosTx);
            $resultado = $save->updateDB('conciliacion_banco_integrado',null,'id ='.$data->idTx);

            if(!$resultado){
                JFactory::getApplication()->enqueueMessage('Error al intetar Almacenar los Datos', 'error');
            }
        }

        $remainder = getFromTimOne::getRemainderOrder($data->idOrden, $data->type, $data->amount);
        if($remainder == 0){
            $data->ordenPagada = 1;
        }

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

            /*NOTIFICACIONES 20*/
            $integradoSimple     = new IntegradoSimple($this->integradoId);
            $getCurrUser         = new Integrado($this->integradoId);

            $titulo = JText::_('TITULO_20');

            $contenido = JText::_('NOTIFICACIONES_20');

            $dato['titulo']         = $titulo;
            $dato['body']           = $contenido;
            $dato['email']          = JFactory::getUser()->email;
            $send                   = new Send_email();
            //$info = // $send->notification($dato);

            JFactory::getApplication()->redirect('index.php?option=com_conciliacionadmin&view='.$data->type.'list');
        }else{
            JFactory::getApplication()->enqueueMessage(JText::_('com_comciliacionadmin_error_save'),'error');
        }
    }
}
