<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.gettimone');

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
        $document   = JFactory::getDocument();
        $this->app  = JFactory::getApplication();
        $data       = $this->app->input->getArray($post);
        $id         = $data['idOrden'];
        $save       = new sendToTimOne();
        $tab        = $data['tab'];
        $numOrden   = $data['numOrden'];

        $document->setMimeEncoding('application/json');

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

        /*NOTIFICACIONES 6

        /*if($respuesta['success']==true){
            $contenido = JText::_('NOTIFICACIONES_6');
            $contenido = str_replace('$integrado', '<strong style="color: #000000">'.$data['nameIntegrado'].'</strong>',$contenido);
            $contenido = str_replace('$odvId', '<strong style="color: #000000">'.$data['name'].'</strong>',$contenido);
            $contenido = str_replace('$usuario', '<strong style="color: #000000">$'.$data['corrUser'].'</strong>',$contenido);
            $contenido = str_replace('$monto', '<strong style="color: #000000">$'.$data['monto'].'</strong>',$contenido);
            $contenido = str_replace('$cliente', '<strong style="color: #000000">$'.$data['cliente'].'</strong>',$contenido);
            $contenido = str_replace('$fecha', '<strong style="color: #000000">'.date('d-m-Y').'</strong>',$contenido);


            $data['titulo']         = JText::_('TITULO_6');
            $data['body']           = $contenido;

            $send                   = new Send_email();
            $info = $send->notification($data);
        }*/

        echo json_encode($respuesta);
    }
}
