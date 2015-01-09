<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('integradora.gettimone');

/**
 * @property array permisos
 * @property array data
 * @property string titulo
 * @property mixed orders
 */
class MandatosViewTxsinmandatoform extends JViewLegacy {

	protected $integradoId;

	function display($tpl = null){
		$app 				= JFactory::getApplication();
        $post               = array('txnum'=>'INT');
        $data				= $app->input->getArray($post);

		$sesion             = JFactory::getSession();
		$this->integradoId        = $sesion->get('integradoId', null, 'integrado');

		$integ = new IntegradoSimple($this->integradoId);
		$this->integrado->displayName = $integ->getDisplayName();

		// get the model
		if( !is_null($data['txnum']) ){
			$model = $this->getModel();
			$this->titulo = 'COM_MANDATOS_TXSINMANDTO_FORM_TITLE';
			$this->data     = $model->getItem($data['txnum']);
			$this->orders   = $model->getOrders($this->integradoId);
		}

		// Check for errors.
        if (count($errors = $this->get('Errors'))){
			JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
			return false;
        }

		$this->loadHelper('Mandatos');

		// Verifica los permisos de edición y autorización
		$this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

		if (!$this->permisos['canEdit']) {
			$url = 'index.php?option=com_mandatos&view=txsinmandatolist&';
			$msg = JText::_('JERROR_ALERTNOAUTHOR');
			$app->redirect(JRoute::_($url), $msg, 'error');
		}

		parent::display($tpl);
	}
}