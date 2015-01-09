<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * @property int integradoId
 * @property mixed permisos
 * @property mixed data
 * @property mixed token
 */
class MandatosViewOdvlist extends JViewLegacy {

	protected $integradoId;
	protected $permisos;

	function display($tpl = null){
		$session            = JFactory::getSession();
		$this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

		$model              = $this->getModel();
		$this->data         = $model->getOrdenes($this->integradoId);
		$this->token        = getFromTimOne::token();
		
        if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }
		
		$this->loadHelper('Mandatos');

		foreach ($this->data as $key => $odv) {

			$odv->proveedor = MandatosHelper::getClientsFromID($odv->clientId, $this->integradoId);
			
			$this->data[$key] = $odv;
		}
		
		$this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

		parent::display($tpl);
	}
}