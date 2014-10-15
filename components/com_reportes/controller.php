<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');
jimport('integradora.classDB');


class ReportesController extends JControllerLegacy {

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

    function saveforms(){
    }
}