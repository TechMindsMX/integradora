<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOdrpreview extends JViewLegacy {

	protected $integradoId;
	protected $permisos;

	function display($tpl = null){
		$app 				= JFactory::getApplication();
		$data				= $app->input->getArray( array('idOrden' => 'INT') );

		$session            = JFactory::getSession();
		$this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

		$this->odr		 	= $this->get('ordenes');

		$this->integCurrent = $this->get('integrado');
        $this->odr->cuenta = $this->integCurrent->integrados[0]->datos_bancarios[$this->odr->cuentaId];


        if (count($errors = $this->get('Errors'))) {
                JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                return false;
        }

		$this->loadHelper('Mandatos');

		// Boton de impresion
		$url = 'index.php?option=com_mandatos&view=odrpreview&layout=printview&idOrden=' . $data['idOrden'];
		$this->printBtn = MandatosHelper::getPrintButton($url);

		// Verifica los permisos de edición y autorización
		$this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

		parent::display($tpl);
	}
}