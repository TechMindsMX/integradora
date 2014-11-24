<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOddform extends JViewLegacy {
	
	function display($tpl = null){
		$app 				= JFactory::getApplication();
		$data				= $app->input->getArray();
		$this->integradoId 	= $data['integradoId'];
		$this->odd		 	= $this->get('orden');
        $this->actionUrl    = !isset($data['confirmacion'])?JRoute::_('index.php?option=com_mandatos&view=oddform&integradoId='.$this->integradoId.'&confirmacion=1'):'#';
        $this->datos        = $data;

        if(isset($_FILES['attachment']['size'])){
            if($_FILES['attachment']['size'] != 0) {
                $this->file['name'] = $_FILES['attachment']['name'];
                $this->file['ruta'] = manejoImagenes::cargar_imagen('image/jpeg', $this->integradoId, $_FILES['attachment'], 'odd_attachment');
            }else{
                $this->file['name'] = '';
            }   $this->file['ruta'] = '';
        }


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