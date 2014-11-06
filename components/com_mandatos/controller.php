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
        $post           = array('integradoId'=>'INT', 'id_proyecto'=>'INT');
		$data 			= $this->input_data->getArray($post);
		$proyectos 		= getFromTimOne::getProyects($data['integradoId']);
        $count          = 0;

        if($this->currUser->guest){
			$this->app->redirect('index.php/login');
		}

		foreach ($proyectos as $key => $value) {
            if($data['id_proyecto'] == $value->id_proyecto){
                if($value->parentId == 0){
                    $this->app->redirect(JRoute::_('index.php?option=com_mandatos&view=proyectosform&id_proyecto='.$data['id_proyecto'].'&integradoId='.$data['integradoId']));
                }else{
                    $this->app->redirect(JRoute::_('index.php?option=com_mandatos&view=subproyectosform&id_proyecto='.$data['id_proyecto'].'&integradoId='.$data['integradoId']));
                }
			}else{
                $count++;
            }
		}

        if( $count == count($proyectos) ){
            $this->app->redirect(JRoute::_('index.php?option=com_mandatos&view=proyectoslist&integradoId='.$data['integradoId']));
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

    function saveProyects(){
        $campos = array('integradoId'=>'INT', 'parentId'=>'INT','name'=>'STRING','description'=>'STRING','status'=>'INT', 'id_proyecto'=>'INT');

        $data = $this->input_data->getArray($campos);
        $save = new sendToTimOne();

        if( $data['id_proyecto'] == 0 ){
            unset($data['id_proyecto']);
            $save->saveProyect($data);
        }else{
            $id_proyecto = $data['id_proyecto'];
            unset($data['id_proyecto']);
            $save->updateProject($data,$id_proyecto);
        }

        JFactory::getApplication()->redirect('index.php/component/mandatos/?view=proyectoslist&integradoId='.$data['integradoId']);
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