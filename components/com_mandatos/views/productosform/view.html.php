<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('integradora.gettimone');

class MandatosViewProductosform extends JViewLegacy {

	protected $integradoId;
	protected $permisos;

	function display($tpl = null){
		$post               = array('id_producto' => 'INT');
        $app 				= JFactory::getApplication();
        $data               = $app->input->getArray($post);
		$session            = JFactory::getSession();
		$this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

		$this->currencies   = $this->get('CatalogoCurrencies');
		$this->catalogoIva  = $this->get('CatalogoIva');

        if( $data['id_producto'] != 0 ){
			$this->titulo = 'COM_MANDATOS_PRODUCTOS_LBL_EDITAR';
			$this->producto = $this->get('Producto');
		}else{
			$this->titulo = 'COM_MANDATOS_PRODUCTOS_LBL_AGREGAR';
            $this->producto 				= new stdClass;
            $this->producto->id_producto    = null;
            $this->producto->integradoId	= $this->integradoId;
            $this->producto->productName	= '';
            $this->producto->measure		= '';
            $this->producto->price		    = '';
            $this->producto->iva			= '';
            $this->producto->ieps		    = '';
            $this->producto->currency	    = 'MXN';
            $this->producto->status		    = 1;
            $this->producto->description   = '';
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
			$url = 'index.php?option=com_mandatos&view=productoslist';
			$msg = JText::_('JERROR_ALERTNOAUTHOR');
			$app->redirect(JRoute::_($url), $msg, 'error');
		}
		
		parent::display($tpl);
	}
}