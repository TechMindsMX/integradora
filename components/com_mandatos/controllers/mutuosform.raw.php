<?php
defined('_JEXEC') or die('Restricted access');

jimport('integradora.gettimone');
jimport('integradora.validator');
require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerMutuosform extends JControllerLegacy {

    public function __construct(){
        $this->app          = JFactory::getApplication();
        $this->inputVars    = $this->app->input;
        $post               = array(
            'integradoId'       => 'INT',
            'idMutuo'           => 'INT',
            'integradoIdR'      => 'INT',
            'paymentPeriod'     => 'INT',
            'idCuenta'          => 'INT',
            'cuotaOcapital'     => 'INT',
            'quantityPayments'  => 'FLOAT',
            'totalAmount'       => 'FLOAT',
            'interes'           => 'FLOAT',
            'beneficiario'      => 'STRING',
            'jsonTabla'         => 'STRING',
            'rfc'               => 'STRING',
            'layout'            => 'STRING',
            'banco_codigo'      => 'STRING',
            'banco_cuenta'      => 'STRING',
            'banco_sucursal'    => 'STRING',
            'banco_clabe'       => 'STRING'
        );

        $this->parametros   = (object) $this->inputVars->getArray($post);

        parent::__construct();
    }

    function saveMutuo() {
        $db    = JFactory::getDbo();
        $datos = $this->parametros;
        $save  = new sendToTimOne();

        if($datos->integradoIdR == 0){
            $id = 5;//getFromTimOne::newintegradoId(0);

            $data_integrado = array(
                'nombre_representante' => $datos->beneficiario,
                'rfc'                  => $datos->rfc,
                'integrado_id'         => $id
            );

            $save->formatData($data_integrado);
            $creado = $save->insertDB('integrado_datos_personales');

            $dataBancaria = array(
                'banco_codigo'      => $datos->banco_codigo,
                'banco_cuenta'      => $datos->banco_cuenta,
                'banco_sucursal'    => $datos->banco_sucursal,
                'banco_clabe'       => $datos->banco_clabe,
                'integrado_id'       => $id
            );

            $save->formatData($dataBancaria);
            $datos->idCuenta = $save->insertDB('integrado_datos_bancarios',null,null,true);

        }else{
            $id = $datos->integradoIdR;
        }
        $dataMutuo = array(
            'integradoIdE'      => $datos->integradoId,
            'integradoIdR'      => $id,
            'idCuenta'          => $datos->idCuenta,
            'paymentPeriod'     => $datos->paymentPeriod,
            'quantityPayments'  => $datos->quantityPayments,
            'jsonTabla'         => $datos->jsonTabla,
            'totalAmount'       => $datos->totalAmount,
            'interes'           => $datos->interes,
            'cuotaOcapital'     => $datos->cuotaOcapital
        );

        $save->formatData($dataMutuo);
        $id_mutuo = $save->insertDB('mandatos_mutuos', null, null,true);

        var_dump($id_mutuo);
        exit;
        /* $id    = $datos['idOrden'];

         $datos['paymentDate'] = $date->getTimestamp();

         $this->permisos  = MandatosHelper::checkPermisos(__CLASS__, $datos['integradoId']);

         if($this->permisos['canAuth']) {
             // acciones cuando tiene permisos para autorizar
             $this->app->enqueueMessage('aqui enviamos a timone la autorizacion y redireccion con mensaje');
         } else {
             // acciones cuando NO tiene permisos para autorizar
             $this->app->redirect(JRoute::_(''), JText::_(''), 'error');
         }

         if( $id === 0 ) {
             unset($datos['idOrden']);
             $datos['createdDate'] = time();
             $datos['numOrden'] = $save->getNextOrderNumber('odc', $datos['integradoId']);

             $save->formatData($datos);
             $salvado = $save->insertDB('ordenes_compra');

             $id = $db->insertid();
         }else{
             $save->formatData($datos);
             $salvado = $save->updateDB('ordenes_compra', null,'numOrden = '.$datos['numOrden']);
         }

         if($salvado) {
             $sesion = JFactory::getSession();
             $sesion->set('msg','Datos Almacenados', 'odcCorrecta');

             $respuesta = array(
                 'urlRedireccion' => 'index.php?option=com_mandatos&view=odcpreview&integradoId=' . $datos['integradoId'] . '&idOrden=' . $id .'&success=true',
                 'redireccion' => true
             );
         }else{
             $respuesta = array('redireccion' => false);
         }


         JFactory::getDocument()->setMimeEncoding('application/json');
         $respuesta['idOrden']= $id;

         echo json_encode($respuesta);*/
    }

    function valida(){
        $validacion = new validador();
        $document = JFactory::getDocument();

        $parametros = $this->parametros;

        $diccionario = array(
            'integradoIdE'   => array('tipo' => 'string', 'length' => '100'),
            'integradoIdR'   => array('tipo' => 'string', 'length' => '100'),
            'expirationDate' => array('tipo' => 'date', 'length' => '10'),
            'payments'       => array('tipo' => 'string', 'length' => '10'),
            'totalAmount'    => array('tipo' => 'float', 'length' => '100'),
            'interes'        => array('tipo' => 'string', 'length' => '100'));

        $respuesta = $validacion->procesamiento($parametros,$diccionario);

        $document->setMimeEncoding('application/json');
        echo json_encode($respuesta);
    }
}
