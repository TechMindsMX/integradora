<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.gettimone');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerOdvform extends JControllerAdmin {

    function safeform(){
        $post       = array('integradoId'   => 'INT',
            'idOdv'         => 'INT',
            'projectId'     => 'STRING',
            'projectId2'    => 'STRING',
            'clientId'      => 'STRING',
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
        $db	       = JFactory::getDbo();
        $document   = JFactory::getDocument();
        $this->app  = JFactory::getApplication();
        $data       = $this->app->input->getArray($post);
        $idOdv      = $data['idOdv'];
        $save       = new sendToTimOne();

        $document->setMimeEncoding('application/json');

        if($data['tab'] == 'seleccion'){
            $respuesta['tab'] = 'ordenventa';
        }
        unset($data['idOdv']);
        unset($data['tab']);
        foreach ($data['producto'] as $indice => $valor) {
            if($data['producto'][$indice] != '') {
                $productos = new stdClass();

                $productos->name        = $data['producto'][$indice];
                $productos->descripcion = $data['descripcion'][$indice];
                $productos->cantidad    = $data['cantidad'][$indice];
                $productos->unidad      = $data['unidad'][$indice];
                $productos->p_unitario  = $data['p_unitario'][$indice];
                $productos->iva         = $data['iva'][$indice];
                $productos->ieps        = $data['ieps'][$indice];

                $productosArray[]       = $productos;
            }
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

        if($idOdv === 0){
            $query 	= $db->getQuery(true);
            $query->select('UNIX_TIMESTAMP(CURRENT_TIMESTAMP)');

            try {
                $db->setQuery($query);
                $results = $db->loadColumn();
            }catch (Exception $e){
                var_dump($e->getMessage());
                exit;
            }
            $columnas[] = 'created';
            $valores[]  = $results[0];
            $save->insertDB('ordenes_venta', $columnas, $valores);

            $idOdv = $db->insertid();
        }else{
            $save->updateDB('ordenes_venta',$update,$db->quoteName('idOdv').' = '.$db->quote($idOdv));
        }

        $strIdodv = ''.$idOdv.'';
        $numOrden = str_pad($strIdodv,6,'0',STR_PAD_LEFT);


        $respuesta['success']  = true;
        $respuesta['idOdv']    = $idOdv;
        $respuesta['numOrden'] = $idOdv;

        echo json_encode($respuesta);
    }
}
