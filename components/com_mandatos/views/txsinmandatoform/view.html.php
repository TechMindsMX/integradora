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
        $post               = array(
	        'txnum'=>'INT',
	        'idOrden'=>'INT',
	        'orderType'=>'STRING'
        );
        $data				= $app->input->getArray($post);

		$sesion             = JFactory::getSession();
		$this->integradoId        = $sesion->get('integradoId', null, 'integrado');

		$integ = new IntegradoSimple($this->integradoId);
		$this->integrado->displayName = $integ->getDisplayName();

		// get the model
		if ( !is_null($data['txnum']) ){
			$model = $this->getModel();
			$this->titulo = 'COM_MANDATOS_TXSINMANDTO_FORM_TITLE';
			$this->data     = $model->getItem($data['txnum']);
			$this->orders   = $model->getOrdersCxC($this->integradoId);

			if ( isset( $model->vars['layout'] ) && $model->vars['layout'] == 'confirm') {
				$this->orders->order = $model->getOrderByIdAndType($this->orders, $data['idOrden'], $data['orderType'] );
			}
		}

		if (empty($this->orders->odv) && empty($this->orders->odd)) {
			$url = 'index.php?option=com_mandatos&view=txsinmandatolist';
			$msg = JText::_('MSG_NO_ORDERS');
			$app->redirect(JRoute::_($url), $msg, 'error');
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
			$url = 'index.php?option=com_mandatos&view=txsinmandatolist';
			$msg = JText::_('JERROR_ALERTNOAUTHOR');
			$app->redirect(JRoute::_($url), $msg, 'error');
		}

		parent::display($tpl);
	}
}