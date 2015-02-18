<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.gettimone');
jimport('integradora.notifications');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';

class MandatosControllerOdvform extends JControllerAdmin {

	protected $data;

    function sendform(){
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


        $this->app  = JFactory::getApplication();
        $this->data       = $this->app->input->getArray($post);
        $id         = $this->data['idOrden'];
        $tab        = $this->data['tab'];
        $numOrden   = $this->data['numOrden'];

        $valida = $this->validate($this->data);

        $this->jsonReturn($valida);

    }

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	private function validate( $data ) {

        $diccionario = array(
                'projectId'     => array('number' => true),
                'projectId2'    => array('number' => true),
                'clientId'      => array('number' => true, 'required' => true),
                'account'       => array('number' => true, 'required' => true),
                'paymentMethod' => array('number' => true, 'required' => true),
                'conditions'    => array('number' => true, 'required' => true),
                'placeIssue'    => array('number' => true, 'required' => true),
        );

        $dataDiccionario = array_diff_key($data, $diccionario);

//        foreach ( $dataDiccionario as $key => $val ) {
//            $typeValidation        = array('required' => true);
//            $diccionario[$key] = $typeValidation;
//        }
        $validator = new validador();
        $respuesta = $validator->procesamiento($data, $diccionario);

        $respuesta['success'] = $validator->allPassed();

        //validacion de los datos de los productos
        foreach ( $dataDiccionario as $key => $val ) {
            $diccionarioProd = array(
                'cantidad'      => array('number' => true,  'required'=> true),
                'descripcion'   => array('alphaNum' => true,  'required'=> true),
                'ieps'          => array('float' => true),
                'iva'           => array('float' => true,  'required'=> true),
                'p_unitario'    => array('float' => true,  'required'=> true),
                'producto'      => array('alphaNum' => true,  'required'=> true),
                'unidad'        => array('alphaNum' => true,  'required'=> true),
            );

            if ( is_array( $val ) ) {
                foreach ( $val as $id => $data ) {
                    $field = $validator->procesamiento(array($key => $data), array( $key => $diccionarioProd[$key]) );
                    if ( isset( $field[$key] ) ) {
                        $respuesta[$key.$id] = $field[$key];
                    }
                }
            }

        }

        return $respuesta;
    }

    private function jsonReturn( $respuesta ) {
        $document   = JFactory::getDocument();
        $document->setMimeEncoding('application/json');

        die( json_encode($respuesta) );
    }

}
