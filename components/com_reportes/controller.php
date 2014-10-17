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



    function saveforms(){
    }
}