<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOdcform extends JViewLegacy {
	
	function display($tpl = null){
		$app				= JFactory::getApplication();
		$data				= $app->input->getArray();

        if(isset($data['confirmacion'])){
            $this->datos = $data;
			$this->dataXML = $this->get('data2xml');
        }
		
        $this->integradoId	= $data['integradoId'];
        $this->data 		= $this->get('orden');
        $this->proyectos 	= $this->get('proyectos');
        $this->proveedores	= $this->get('providers');

        if (count($errors = $this->get('Errors'))) {
	        JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
            return false;
        }
		
		$this->loadHelper('Mandatos');
		$this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

		if (!$this->permisos['canEdit']) {
			$url = 'index.php?option=com_mandatos&view=odclist&integradoId='.$this->integradoId;
			$msg = JText::_('JERROR_ALERTNOAUTHOR');
			$app->redirect(JRoute::_($url), $msg, 'error');
		}
		
		parent::display($tpl);
	}
}