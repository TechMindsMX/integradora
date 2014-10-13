<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');
jimport('integradora.classDB');


class MandatosController extends JControllerLegacy {
	
	public function __construct()
	{
		parent::__construct();
		$integrado	 		= new Integrado;

		$this->app			= JFactory::getApplication();
		$this->input_data	= $this->app->input;

		$data		 		= $this->input_data->getArray();
		$integradoId 		= isset($integrado->integrados[0]) ? $integrado->integrados[0]->integrado_id : $data['integradoId'];
		$this->currUser	 	= JFactory::getUser();
        // $isValid 	 		= $integrado->isValidPrincipal($integradoId, $this->currUser->id);
        
		if($this->currUser->guest){
			$this->app->redirect('index.php/login', JText::_('MSG_REDIRECT_LOGIN'), 'Warning');
		}
		if(is_null($integradoId)){
			$this->app->redirect('index.php?option=com_integrado&view=solicitud', JText::_('MSG_REDIRECT_INTEGRADO_PRINCIPAL'), 'Warning');
		}
	}
	
	function editarproyecto(){
		$data 				= $this->input_data->getArray();
		$integradoId		= $data['integradoId'];
		$proyectos 			= getFromTimOne::getProyects($integradoId);
		
		if($this->currUser->guest){
			$this->app->redirect('index.php/login');
		}

		foreach ($proyectos as $key => $value) {
			if($data['proyId'] == $value->id){
				if($value->parentId == 0){
					$this->app->redirect(JRoute::_('index.php?option=com_mandatos&view=proyectosform&proyId='.$data['proyId'].'&integradoId='.$integradoId));
				}else{
					$this->app->redirect(JRoute::_('index.php?option=com_mandatos&view=subproyectosform&proyId='.$data['proyId'].'&integradoId='.$integradoId));
				}
			}
		}
		exit;
	}

	function editarproducto(){
		$data 			= $this->input_data->getArray();
		$integrado_id	= $data['integradoId'];
		$productos 		= getFromTimOne::getProducts($integrado_id);

		if($this->currUser->guest){
			$this->app->redirect('index.php/login');
		}
		
		foreach ($productos as $key => $value) {
			if( $data['prodId'] == $value->id ){
				$this->app->redirect(JRoute::_('index.php?option=com_mandatos&view=productosform&prodId='.$data['prodId'].'&integradoId='.$data['integradoId']));
			}
		}
	}
	
	function editarclientes(){
		$this->app->redirect(JRoute::_('index.php?option=com_mandatos&view=clientes'), 'Por el momento no es posible crear ni editar');
	}

	function simulaenvio(){
		$this->app->redirect(JRoute::_('index.php?option=com_mandatos'), 'Datos recibidos');
	}

	public function envioTimOne($envio)
	{
		$request = new sendToTimOne();
		$serviceUrl = new IntRoute();

		$request->setServiceUrl($serviceUrl->saveComisionServiceUrl());
		$request->setJsonData($envio);

		$respuesta = $request->to_timone(); // realiza el envio

		return $respuesta;
	}
	
	function searchrfc(){
		$data 			= $this->input_data->getArray();
		$db		= JFactory::getDbo();
		$where	= $db->quoteName('rfc').' = '.$db->quote($data['rfc']);
		$respuesta = '';
		
		$rfcPersonas = validador::valida_rfc($data, 'rfc', 'dp_');
		$rfcEmpresa	= validador::valida_rfc($data, 'rfc', 'de_');
		
		if($rfcEmpresa){
			$tipo_rfc = 1;
		}elseif($rfcPersonas){
			$tipo_rfc = 2;
		}else{
			$respuesta['success'] = false;
			$respuesta['msg'] = JText::_('MSG_RFC_INVALID');
			
			echo json_encode($respuesta);
			exit;
		}
		
		$existe = querysDB::checkData('integrado_datos_personales', $where);
		if(empty($existe)){
			$existe = querysDB::checkData('integrado_datos_empresa', $where);
		}
		
		if(!empty($existe)){
			$datos = new Integrado;
			$datos->integrados[0]->success = true;
			echo json_encode($datos->integrados[0]);
		}else{
			$respuesta['success'] = false;
			$respuesta['msg'] = JText::_('MSG_RFC_NO_EXIST');
			$respuesta['pj_pers_juridica'] = $tipo_rfc;
			
			echo json_encode($respuesta);
		}
	}

	function agregarBanco(){
        $document = JFactory::getDocument();
        // Set the MIME type for JSON output.
        $document->setMimeEncoding('application/json');
        // Change the suggested filename.
        JResponse::setHeader('Content-Disposition','attachment; filename="result.json"');

		$data 			= $this->input_data->getArray();
		$validacion 	= validador::valida_banco_clabe($data, 'db_banco_clabe');
		
		$respuesta['banco'] 	= $data['db_banco_nombre'];
  		$respuesta['cuenta'] 	= $data['db_banco_cuenta'];
  		$respuesta['sucursal'] 	= $data['db_banco_sucursal'];
  		$respuesta['clabe']	 	= $data['db_banco_clabe'];
		$respuesta['idCuenta']	= ($data['db_banco_sucursal']*1)+1;

		echo json_encode($respuesta);
	}

    function saveforms(){
        //$filtro = array('campo'=>'tipodato');
        $data   =   $this->input_data->getArray();
        $odv    = array();

        $validacion = new validador();

        $diccionario = array('account'       => array('tipo'=>'number', 'length' => ''),
                             'clientId'      => array('tipo'=>'number', 'length' => ''),
                             'conditions'    => array('tipo'=>'number', 'length' => ''),
                             'paymentMethod' => array('tipo'=>'number', 'length' => ''),
                             'placeIssue'	 => array('tipo'=>'number', 'length' => ''),
                             'projectId'	 => array('tipo'=>'number', 'length' => ''),
                             'projectId2'	 => array('tipo'=>'number', 'length' => ''));
        foreach($diccionario as $key => $value){
            $envio[$key] = $data[$key];
        }

        foreach ($data as $key => $value) {
            if(!is_bool(strpos($key, 'cantidad'))){
                $cantidades[] = $value;
                $diccionario[$key] = array('tipo' => 'number', 'length' => '6');
            }
            if(!is_bool(strpos($key, 'productos'))){
                $productos[] = $value;
                $diccionario[$key] = array('tipo' => 'number', 'length' => '6');
            }
            if(!is_bool(strpos($key, 'descripcion'))){
                $descriptions[] = $value;
                $diccionario[$key] = array('tipo' => 'alphaNumber', 'length' => '2500');
            }
            if(!is_bool(strpos($key, 'p_unitario'))){
                $punitario[] = $value;
                $diccionario[$key] = array('tipo' => 'alphaNumber', 'length' => '50');
            }
            if(!is_bool(strpos($key, 'unidad'))){
                $unidades[] = $value;
                $diccionario[$key] = array('tipo' => 'alphaNumber', 'length' => '60');
            }
            if(!is_bool(strpos($key, 'iva'))){
                $iva[] = $value;
                $diccionario[$key] = array('tipo' => 'alphaNumber', 'length' => '60');
            }
            if(!is_bool(strpos($key, 'ieps'))){
                $ieps[] = $value;
                $diccionario[$key] = array('tipo' => 'alphaNumber', 'length' => '60');
            }

        }

        $resultValidacion  = $validacion->procesamiento($data, $diccionario);
        foreach($resultValidacion as $value){
            if(!is_bool($value)){
                echo json_encode($resultValidacion);
                return;
            }
        }
        exit;

        for($i = 0; $i < count($cantidades); $i++){
            $obj = new stdClass();

            $obj->productos     = $productos[$i];
            $obj->cantidades    = $cantidades[$i];
            $obj->descripcion   = $descriptions[$i];
            $obj->unidades      = $unidades[$i];
            $obj->pUnitario     = $punitario[$i];
            $obj->iva           = $iva[$i];
            $obj->ieps          = $ieps[$i];

            $odv[]= $obj;
        }

        $envio['odv'] = json_encode($odv);

        echo json_encode($envio);
    }

    function  cargaProducto(){
        $data = $this->input_data->getArray();
        $productos = getFromTimOne::getProducts($data['integradoId']);
        foreach ($productos as $key => $val) {
                if($data['id'] == $val->id){
                    $producto = $val;
                }
        }

        echo json_encode($producto);
    }
}