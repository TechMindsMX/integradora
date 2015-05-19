<?php
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.component.controller');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');
jimport('integradora.classDB');
jimport('integradora.notifications');
jimport('integradora.facturasComision');

/**
 * 
 */
class MandatosController extends JControllerLegacy {

    public function __construct(){
        parent::__construct();
        $this->app			= JFactory::getApplication();
        $this->document     = JFactory::getDocument();
        $this->currUser	 	= JFactory::getUser();
        $this->input_data	= $this->app->input;
        $this->post		 	= $this->input_data->getArray();
        $this->integradoId  = 1;
    }
	
	function display($cacheable = false, $urlparams = false) {
		$input = JFactory::getApplication()->input;
		$input->set('view', $input->get('view', 'Mutuoslist'));
		
		parent::display($cacheable);
		
	}

    public function search_rfc_cliente() {
        $this->document->setMimeEncoding( 'application/json' );
        $data = $this->input->getArray( array( 'integradoId' => 'INT', 'rfc' => 'STRING' ) );
        $tipo_rfc = $this->rfc_type($data['rfc']);

        $existe = $this->search_rfc_exists( $data['rfc'] );

        if(!empty($existe)){
            // Busca si existe la relacion entre el integrado actual y el resultado de la busqueda
	        $dbq = JFactory::getDbo();
            $relation = getFromTimOne::selectDB('integrado_clientes_proveedor', 'integradoId = '. $dbq->quote($this->integradoId) .' AND integradoIdCliente = '.$existe );

            $datos = new IntegradoSimple($existe);
            $datos->integrados[0]->success = true;

            $datos->integrados[0]->tipo_alta = isset($relation[0]->tipo_alta) ? $relation[0]->tipo_alta : '';

            echo json_encode($datos->integrados[0]);
        }elseif( is_numeric($tipo_rfc) ){
            $respuesta['success'] = false;
            $respuesta['msg'] = JText::_('MSG_RFC_NO_EXIST');
            $respuesta['bu_rfc'] = $tipo_rfc;

            echo json_encode( $respuesta );
        }else{
            $tipo_rfc['success'] = 'invalid';
            echo json_encode( array('bu_rfc' => $tipo_rfc) );
        }
    }

    public function rfc_type($rfc) {

        $diccionarioFisica = array( 'rfc' => array( 'rfc_fisica' => true, 'required' => true ) );
        $diccionarioMoral  = array( 'rfc' => array( 'rfc_moral' => true, 'required' => true ) );
        $validator         = new validador();
        $is_validFisica    = $validator->procesamiento( array('rfc' => $rfc), $diccionarioFisica );
        $is_validMoral     = $validator->procesamiento( array('rfc' => $rfc), $diccionarioMoral );

        $respuesta = '';

        if ( ! is_array($is_validMoral['rfc']) ) {
            $respuesta = 1;
        } elseif ( ! is_array($is_validFisica['rfc']) ) {
            $respuesta = 2;
        } else {
            $respuesta['success'] = false;
            $respuesta['msg']     = JText::_( 'MSG_RFC_INVALID' );
        }

        return $respuesta;
    }

    /**
     * @param $rfc
     *
     * @return array
     * @internal param $data
     *
     */
    public function search_rfc_exists( $rfc ) {
        $db        = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select($db->quoteName('integradoId'))->from('#__integrado_datos_personales')->where($db->quoteName('rfc').' = '.$db->quote($rfc));
        $db->setQuery($query);
        $personales = $db->loadResult();

        $query = $db->getQuery(true);
        $query->select($db->quoteName('integradoId'))->from('#__integrado_datos_empresa')->where($db->quoteName('rfc').' = '.$db->quote($rfc));
        $db->setQuery($query);
        $empresa = $db->loadResult();

        $integradoId = (!is_null($personales)) ? $personales : $empresa;

        return $integradoId;
    }

    public function tabla(){
        $this->document->setMimeEncoding('application/json');
        $input  = $this->input;
        $post   = array(
            'quantityPayments' => 'FLOAT',
            'paymentPeriod'    => 'FLOAT',
            'totalAmount'      => 'FLOAT',
            'interes'          => 'FLOAT'
        );

        $data   = (object) $input->getArray($post);

        $validacion = new validador();

        $diccionario = array(
            'quantityPayments' => array('float' => true, 'maxlength' => '10',  'required' => true, 'plazoMaximo' => true),
            'paymentPeriod'    => array('int'   => true, 'maxlength' => '10',  'required' => true, 'tipoPlazo'   => true),
            'totalAmount'      => array('float' => true, 'maxlength' => '100', 'required' => true),
            'interes'          => array('float' => true, 'maxlength' => '100', 'required' => true));

        $respuesta = $validacion->procesamiento($data,$diccionario);

        foreach($respuesta as $key => $campo){
            if(is_array($campo)){
                echo json_encode($respuesta);
                return false;
            }
        }

        $tabla  = getFromTimOne::getTablaAmotizacion($data);

        echo json_encode($tabla);
    }
}
