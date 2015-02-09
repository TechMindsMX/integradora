<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
jimport('integradora.gettimone');
jimport('integradora.classDB');


class ReportesController extends JControllerLegacy {

    protected $integradoId;
    protected $permisos;

    public function __construct()
    {
        parent::__construct();

        $this->app = JFactory::getApplication();

        $sesion = JFactory::getSession();
        $this->integradoId = $sesion->get('integradoId', null, 'integrado');

        $this->currUser	 	= JFactory::getUser();
        // $isValid 	 		= $integrado->isValidPrincipal($this->integradoId, $this->currUser->id);

        $this->permisos = Integrado::checkPermisos(__CLASS__, $this->currUser->id, $this->integradoId);

        if($this->currUser->guest){
            $this->app->redirect('index.php?option=com_users&view=login', JText::_('MSG_REDIRECT_LOGIN'), 'Warning');
        }
        if(is_null($this->integradoId)){
            $this->app->redirect('index.php?option=com_integrado&view=solicitud', JText::_('MSG_REDIRECT_INTEGRADO_PRINCIPAL'), 'Warning');
        }
        else {
            $integrado	    = new IntegradoSimple($this->integradoId);
            $canOperate     = $integrado->canOperate();
            if(!$canOperate){
                $this->app->redirect('index.php?option=com_integrado&view=integrado', JText::_('MSG_REDIRECT_INTEGRADO_CANT_OPERATE'), 'Warning');
            }
        }
    }


    function simulaenvio(){
        $this->app->redirect(JRoute::_('index.php?option=com_mandatos'), 'Datos recibidos');
    }



    function saveforms(){
    }
}