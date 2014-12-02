<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.gettimone');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerOdvform extends JControllerAdmin {

    function safeform(){
        $post       = array('integradoId'   => 'INT',
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

        foreach ($data as $key => $value) {
            $columnas[] = $key;
            $valores[]  = $db->quote($value);
            $update[]   = $db->quoteName($key).' = '.$db->quote($value);
        }

        if($id === 0){
            $data['id'] = $id;

            $query 	= $db->getQuery(true);
            $query->select('UNIX_TIMESTAMP(CURRENT_TIMESTAMP)');

            try {
                $db->setQuery($query);
                $results = $db->loadColumn();
            }catch (Exception $e){
                var_dump($e->getMessage());
                exit;
            }

            $numOrden = $save->getNextOrderNumber('odv', $data['integradoId']);

            $columnas[] = 'numOrden';
            $valores[]  = $numOrden;

            $columnas[] = 'created';
            $valores[]  = $results[0];

            $save->insertDB('ordenes_venta', $columnas, $valores);

            $id = $db->insertid();
        }else{
            $save->updateDB('ordenes_venta',$update,$db->quoteName('id').' = '.$db->quote($id));
        }

        $url = null;
        if($tab == 'ordenVenta'){
            $url = 'index.php?option=com_mandatos&view=odvpreview&integradoId=1&idOrden='.$id.'&layout=confirmOdv';
        }

        $respuesta['success']  = true;
        $respuesta['id']       = $id;
        $respuesta['numOrden'] = $numOrden;
        $respuesta['redirect'] = $url;

        echo json_encode($respuesta);
    }
}
