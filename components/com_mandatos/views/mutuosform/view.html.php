<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('integradora.gettimone');

class MandatosViewMutuosform extends JViewLegacy {
	
	function display($tpl = null){
		$app 				= JFactory::getApplication();
        $post               = array(
            'integradoId'    => 'INT',
            'idMutuo'        => 'INT',
            'integradoIdR'   => 'INT',
            'beneficiario'   => 'STRING',
            'rfc'            => 'STRING',
            'layout'         => 'STRING',
            'expirationDate' => 'FLOAT',
            'payments'       => 'FLOAT',
            'totalAmount'    => 'FLOAT',
            'interes'        => 'FLOAT'
        );
		$this->data			= (object) $app->input->getArray($post);
		$this->integradoId 	= $this->data->integradoId;
        $this->idMutuo      = $this->data->idMutuo;
        $this->tipoPago     = $this->get('TipoPago');

        var_dump($this->data);

		$this->titulo   = 'COM_MANDATOS_MUTUO_LBL_EDITAR';

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