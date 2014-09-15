<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOdcpreview extends JViewLegacy {
	
	function display($tpl = null){
		$data				= JFactory::getApplication()->input->getArray();
		$this->integradoId	= $data['integradoId'];

		$this->odc		 	= $this->get('ordenes');

		$this->integCurrent = $this->get('integrado');
		
        if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }


        if($this->integCurrent->usuarios[0]->permission_level >= 3) {
            $this->acciones->autoriza = true;
        }

		parent::display($tpl);
	}
}