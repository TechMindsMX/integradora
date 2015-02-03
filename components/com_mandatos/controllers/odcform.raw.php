<?php
defined('_JEXEC') or die('Restricted access');

jimport('integradora.gettimone');
jimport('integradora.validator');
jimport('integradora.notifications');
require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerOdcform extends JControllerLegacy {

    public function __construct(){
        $this->app          = JFactory::getApplication();
        $this->inputVars    = $this->app->input;
        $post = array('idOrden'  => 'INT',
                'numOrden'       => 'INT',
                'proyecto'       => 'STRING',
                'proveedor'      => 'STRING',
                'paymentDate'    => 'STRING',
                'paymentMethod'  => 'STRING',
                'totalAmount'    => 'STRING',
                'urlXML'         => 'STRING',
                'observaciones'  => 'STRING',
            );

        $this->parametros   = $this->inputVars->getArray($post);

        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

        parent::__construct();
    }

    function saveODC() {
        $db = JFactory::getDbo();

        $datos = $this->parametros;
        $save  = new sendToTimOne();
        $date  = new DateTime($datos['paymentDate']);
        $id    = $datos['idOrden'];

        $datos['paymentDate'] = $date->getTimestamp();

        $this->permisos  = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

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
            $datos['numOrden'] = $save->getNextOrderNumber('odc', $this->integradoId);
            $datos['status'] = 1;
            $datos['integradoId'] = $this->integradoId;

            $save->formatData($datos);
            $salvado = $save->insertDB('ordenes_compra');

            $id = $db->insertid();
        }else{
            unset($datos['idOrden']);
            $save->formatData($datos);
            $salvado = $save->updateDB('ordenes_compra', null,'numOrden = '.$datos['numOrden']);
        }

        if($salvado) {
            $sesion = JFactory::getSession();
            $sesion->set('msg','Datos Almacenados', 'odcCorrecta');

            $respuesta = array(
                'urlRedireccion' => 'index.php?option=com_mandatos&view=odcpreview&idOrden=' . $id .'&success=true',
                'redireccion' => true
            );


            /*NOTIFICACIONES 11*/

            /*$currentIntegradoId= JFactory::getSession()->get('integradoId', null, 'integrado');
            $int = new IntegradoSimple($currentIntegradoId);

            $titulo = JText::_('TITULO_11');
            $titulo = str_replace('$idOrden', '<strong style="color: #000000">'.$this->parametros['idOrden'].'</strong>',$titulo);

            $contenido = JText::_('NOTIFICACIONES_11');
            $contenido = str_replace('$integrado', '<strong style="color: #000000">'.$int->user->username.'</strong>',$contenido);
            $contenido = str_replace('$usuario', '<strong style="color: #000000">'.$int->user->username.'</strong>',$contenido);
            $contenido = str_replace('$idOrden', '<strong style="color: #000000">'.$this->parametros['idOrden'].'</strong>',$contenido);
            $contenido = str_replace('$cliente', '<strong style="color: #000000">'.$data['cliente'].'</strong>',$contenido);
            $contenido = str_replace('$fecha', '<strong style="color: #000000">'.date('d-m-Y').'</strong>',$contenido);
            $contenido = str_replace('$odv', '<strong style="color: #000000">'.$data['odv'].'</strong>',$contenido);

            $data['titulo']         = $titulo;
            $data['body']           = $contenido;

            $send                   = new Send_email();
            $send->notification($data);


            /*if ($send->isError()){
                $resp = $send->getErrorMsg();
                $this->app->enqueueMessage($send->getErrorMsg());
            }else{
                $resp = 'enviado';
                $this->app->enqueueMessage('Correos enviados');
            }*/
        }else{
            $respuesta = array('redireccion' => false);
        }


        JFactory::getDocument()->setMimeEncoding('application/json');
        $respuesta['idOrden']= $id;

        echo json_encode($respuesta);
    }

    function valida(){
        $validacion = new validador();
        $document = JFactory::getDocument();

        $parametros = $this->parametros;

        $diccionario = array(
            'integradoId'   => array('number' => true, 'maxlength' => 10),
            'numOrden'      => array('number' => true, 'maxlength' => 10),
            'proveedor'     => array('number' => true, 'maxlength' => 10, 'notNull' => true),
            'proyecto'      => array('number' => true, 'maxlength' => 10),
            'paymentDate'   => array('date' => true,   'maxlength' => 10),
            'paymentMethod' => array('number' => true, 'maxlength' => 10),
            'observaciones' => array('text' => true,   'maxlength' => 1000));

        $respuesta = $validacion->procesamiento($parametros,$diccionario);
        $respuesta['proveedor'] = $parametros['proveedor']==0?array('success'=>false,'msg'=>'Seleccione el proveedor'):$respuesta['proveedor'];

        $document->setMimeEncoding('application/json');
        echo json_encode($respuesta);
    }
}
