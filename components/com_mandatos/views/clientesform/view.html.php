<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('integradora.gettimone');

class MandatosViewClientesform extends JViewLegacy {
	
	function display($tpl = null){
		$app 				= JFactory::getApplication();
        $post               = array('integradoId'=>'INT', 'idCliPro' => 'INT');
		$data				= $app->input->getArray($post);
		$this->integradoId 	= $data['integradoId'];
        $this->idCliPro     = $data['idCliPro'];

        if( !is_null($this->idCliPro) ){
			$this->titulo   = 'COM_MANDATOS_CLIENT_LBL_EDITAR';
            $this->datos    = $this->get('Cliente');
		}else{
			$this->titulo = 'COM_MANDATOS_CLIENT_LBL_AGREGAR';
            $datos    = new stdClass();
            $datos->id             = null;
            $datos->type           = null;
            $datos->integrado_id   = null;
            $datos->status         = null;
            $datos->rfc            = null;
            $datos->tradeName      = null;
            $datos->corporateName  = null;
            $datos->contact        = null;
            $datos->phone          = null;
            $datos->bancos         = null;

            $this->datos = $datos;
        }
		
		$this->catalogos = $this->get('catalogos');
		
		$this->token = getFromTimOne::token();
		
		// Check for errors.
        if (count($errors = $this->get('Errors'))){
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
			return false;
        }
		
		$this->loadHelper('Mandatos');

		// Verifica los permisos de edición y autorización
		$this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

		if (!$this->permisos['canEdit']) {
			$url = 'index.php?option=com_mandatos&view=clienteslist&integradoId='.$this->integradoId;
			$msg = JText::_('JERROR_ALERTNOAUTHOR');
			$app->redirect(JRoute::_($url), $msg, 'error');
		}
		
		parent::display($tpl);
	}
}