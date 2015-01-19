<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOdrform extends JViewLegacy {

    protected $integradoId;
    protected $permisos;

    function display($tpl = null){
		$app 				        = JFactory::getApplication();
		$data				        = $app->input->getArray();

        $session                    = JFactory::getSession();
        $this->integradoId          = $session->get( 'integradoId', null, 'integrado' );

        $this->odr		 	        = $this->get('ordenes');
		$this->integrado->balance   = $this->get('balance');
        $this->actionUrl            = !isset($data['confirmacion'])?JRoute::_('index.php?option=com_mandatos&view=odrform&confirmacion=1'):'#';
        $this->datos                = $data;

        if(isset($data['confirmacion'])){
            $this->confirmacion = true;
            $this->datos        = $data;
        }else{
            $this->confirmacion = false;
            $this->datos        = null;
        }

        if (count($errors = $this->get('Errors'))) {
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
            return false;
        }

		$this->loadHelper('Mandatos');

		// Verifica los permisos de edición y autorización
		$this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

		parent::display($tpl);
	}
}