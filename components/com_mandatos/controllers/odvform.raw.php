<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.gettimone');
jimport('integradora.notifications');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerOdvform extends JControllerAdmin {

    function safeform(){
        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

        $post       = array(
            'idOrden'       => 'INT',
            'projectId'     => 'STRING',
            'projectId2'    => 'STRING',
            'clientId'      => 'STRING',
            'numOrden'      => 'INT',
            'account'       => 'STRING',
            'paymentMethod' => 'STRING',
            'conditions'    => 'STRING',
            'placeIssue'    => 'STRING',
            'tab'           => 'STRING',
            'descripcion'   => 'ARRAY',
            'unidad'        => 'ARRAY',
            'producto'      => 'ARRAY',
            'cantidad'      => 'ARRAY',
            'p_unitario'    => 'ARRAY',
            'iva'           => 'ARRAY',
            'ieps'          => 'ARRAY');
        $db	        = JFactory::getDbo();

        $this->app  = JFactory::getApplication();
        $data       = $this->app->input->getArray($post);
        $id         = $data['idOrden'];
        $tab        = $data['tab'];
        $numOrden   = $data['numOrden'];

        $valida = $this->validate($data);
        if(!$valida['success']) {
            $this->jsonReturn($valida);
        }

        $save       = new sendToTimOne();


        if($data['tab'] == 'seleccion'){
            $respuesta['tab'] = 'ordenventa';
        }
        unset($data['numOrden']);
        unset($data['tab']);
        unset($data['idOrden']);

        if($tab != 'seleccion') {
	        if ( ! empty( $data['producto'][0] ) ) {
		        foreach ($data['producto'] as $indice => $valor) {
			        if ($data['producto'][$indice] != '') {
				        $productos = new stdClass();

				        $productos->name = $data['producto'][$indice];
				        $productos->descripcion = $data['descripcion'][$indice];
				        $productos->cantidad = $data['cantidad'][$indice];
				        $productos->unidad = $data['unidad'][$indice];
				        $productos->p_unitario = $data['p_unitario'][$indice];
				        $productos->iva = $data['iva'][$indice];
				        $productos->ieps = $data['ieps'][$indice];

				        $productosArray[] = $productos;
			        }
		        }
	        } else {
		        $respuesta['success']  = false;
		        $respuesta['id']       = $id;
		        $respuesta['numOrden'] = $numOrden;
		        $respuesta['redirect'] = null;

		        echo json_encode($respuesta);
		        exit;
	        }
        }else{
            $productosArray = array();
        }

        foreach ($data as $key => $value) {
            if( gettype($value) === 'array' ){
                unset($data[$key]);
            }
        }

        $data['productos'] = json_encode($productosArray);

        $data['integradoId'] = $this->integradoId;
        $save->formatData($data);

        if($id === 0){
            $query 	= $db->getQuery(true);
            $query->select('UNIX_TIMESTAMP(CURRENT_TIMESTAMP)');

            try {
                $db->setQuery($query);
                $results = $db->loadColumn();
            }catch (Exception $e){
                var_dump($e->getMessage());
                exit;
            }

            $numOrden = $save->getNextOrderNumber('odv', $this->integradoId);

            $data['numOrden'] = $numOrden;
            $data['createdDate'] = $results[0];
            $data['status'] = 1;

            $save->formatData($data);

            $data['id'] = $id;
            $save->insertDB('ordenes_venta');

            $id = $db->insertid();
            $this->sendMail($data);



        }else{
            $save->updateDB('ordenes_venta',null,$db->quoteName('id').' = '.$db->quote($id));
        }

        $url = null;
        if($tab == 'ordenVenta'){
            $url = 'index.php?option=com_mandatos&view=odvpreview&idOrden='.$id.'&layout=confirmOdv';
        }

        $respuesta['success']  = true;
        $respuesta['id']       = $id;
        $respuesta['numOrden'] = $numOrden;
        $respuesta['redirect'] = $url;

        echo json_encode($respuesta);
    }

    public function getTotalAmount($productos){
        $totalAmount = 0;

        foreach ($productos as $producto) {
            if($producto->iva == 1){
                $producto->iva = 0;
            }
            if($producto->iva == 2){
                $producto->iva =11;
            }
            if($producto->iva == 3){
                $producto->iva = 16;
            }

            $total = ($producto->cantidad*$producto->p_unitario);
            $montoIva = $total*($producto->iva/100);
            $montoIeps = $total*($producto->ieps/100);

            $totalAmount = $total+$montoIva+$montoIeps+$totalAmount;
        }

        return $totalAmount;
    }

    /**
     * @param $data
     */
    public function sendMail($data)
    {
        /*
         * Notificaciones 6
         */
        $clientes = getFromTimOne::getClientes($this->integradoId, 0);

        $totalAmount = self::getTotalAmount(json_decode($data['productos']));
        $getCurrUser = new IntegradoSimple($this->integradoId);

        $array = array($getCurrUser->getUserPrincipal()->name, $data['numOrden'], JFactory::getUser()->name, date('d-m-Y'), $totalAmount, $clientes[3]->tradeName);

        $sendEmail = new Send_email();
        $sendEmail->setIntegradoEmailsArray($getCurrUser);

        $reportEmail = $sendEmail->sendNotifications('2', $array);

    }

    private function validate( $data ) {

        $diccionario = array(
                'idOrden'       => array('number' => true, 'required' => true),
                'projectId'     => array('number' => true),
                'projectId2'    => array('number' => true),
                'clientId'      => array('number' => true, 'required' => true),
                'numOrden'      => array('number' => true, 'required' => true),
                'account'       => array('number' => true, 'required' => true),
                'paymentMethod' => array('number' => true, 'required' => true),
                'conditions'    => array('number' => true, 'required' => true),
                'placeIssue'    => array('number' => true, 'required' => true),
        );

        $validator = new validador();
        $respuesta = $validator->procesamiento($data, $diccionario);

        $respuesta['success'] = $validator->allPassed();

        return $respuesta;
    }

    private function jsonReturn( $respuesta ) {
        $document   = JFactory::getDocument();
        $document->setMimeEncoding('application/json');

        die( json_encode($respuesta) );
    }
}
