<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('integradora.gettimone');

/**
 * @property array permisos
 * @property array data
 * @property string titulo
 */
class MandatosViewTxsinmandatoform extends JViewLegacy {
	
	function display($tpl = null){
		$app 				= JFactory::getApplication();
        $post               = array('txnum'=>'INT', 'integradoId' => 'INT');
        $data				= $app->input->getArray($post);

		$sesion             = JFactory::getSession();
		$integradoId        = $sesion->get('integradoId', null, 'integrado');
		$integradoId	    = isset($integradoId) ? $integradoId : $data['integradoId'];

		// get the model
		if( !is_null($data['txnum']) ){
			$this->titulo = 'COM_MANDATOS_TXSINMANDTO_TITLE';
			$model = $this->getModel();
			$this->data = $model->getItem($data['txnum']);

		}

		// Check for errors.
        if (count($errors = $this->get('Errors'))){
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
			return false;
        }

		$this->loadHelper('Mandatos');

		// Verifica los permisos de edición y autorización
		$this->permisos = MandatosHelper::checkPermisos(__CLASS__, $integradoId);

		if (!$this->permisos['canEdit']) {
			$url = 'index.php?option=com_mandatos&view=txsinmandatolist&integradoId='.$integradoId;
			$msg = JText::_('JERROR_ALERTNOAUTHOR');
			$app->redirect(JRoute::_($url), $msg, 'error');
		}

		parent::display($tpl);
	}
}