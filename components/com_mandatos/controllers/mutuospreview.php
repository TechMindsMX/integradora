<?php
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT . '/helpers/mandatos.php';
jimport('integradora.gettimone');

/**
 * metodo de envio a TimOne
 * @property mixed parametros
 * @property mixed app
 * @property mixed permisos
 * @property mixed integradoId
 */
class MandatosControllerMutuospreview extends JControllerAdmin {

	function authorize() {
		$post               = array('integradoId' => 'INT', 'idOrden' => 'INT');
		$this->app 			= JFactory::getApplication();
		$this->parametros	= $this->app->input->getArray($post);
		$this->permisos     = MandatosHelper::checkPermisos(__CLASS__, $this->parametros['integradoId']);
		$this->integradoId = JFactory::getSession()->get('integradoId', null,'integrado');
		$this->integradoId = isset($this->integradoId) ? $this->integradoId : $this->parametros['integradoId'];

		$redirectUrl ='index.php?option=com_mandatos&view=mutuoslist&integradoId='.$this->integradoId;

		if($this->permisos['canAuth']) {
			// acciones cuando tiene permisos para autorizar
			$user = JFactory::getUser();
			$save = new sendToTimOne();

			$this->parametros['userId']   = (INT)$user->id;
			$this->parametros['authDate'] = time();
			unset($this->parametros['integradoId']);

			$save->formatData($this->parametros);

			$auths = getFromTimOne::getOrdenAuths($this->parametros['idOrden'],'mutuo_auth');

			$check = getFromTimOne::checkUserAuth($auths);

			if($check){
				$this->app->redirect($redirectUrl, JText::_('LBL_USER_AUTHORIZED'), 'error');
			}

			$resultado = $save->insertDB('auth_mutuo');

			if($resultado) {
				// autorización guardada
				$statusChange = $save->changeOrderStatus($this->parametros['idOrden'], 'mutuo', '1');
				if ($statusChange){
					$this->app->enqueueMessage(JText::_('ORDER_STATUS_CHANGED'));
				}

				$this->app->redirect($redirectUrl, JText::_('LBL_ORDER_AUTHORIZED'));
			}else{
				$this->app->redirect($redirectUrl, JText::_('LBL_ORDER_NOT_AUTHORIZED'), 'error');
			}
		} else {
			// acciones cuando NO tiene permisos para autorizar
			$this->app->redirect($redirectUrl, JText::_('LBL_DOES_NOT_HAVE_PERMISSIONS'), 'error');
		}
	}
}
