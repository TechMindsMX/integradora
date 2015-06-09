<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOdrform extends JViewLegacy {

    protected $integradoId;
    protected $permisos;

    function display($tpl = null){
		$app 				        = JFactory::getApplication();
		$data				        = $app->input->getArray();

        $session                    = JFactory::getSession();
        $this->integradoId          = $session->get( 'integradoId', null, 'integrado' );

        if( isset($data['idOrden']) && $data['idOrden'] != '' ) {
            $this->odr = $this->get('ordenes');
        } else {
	        $sesData = json_decode( $session->get('data', null, 'odr'), true );
	        if ( isset( $data['idOrden'] ) ) {
		        if ( $sesData['idOrden'] == $data['idOrden'] && $data['confirmacion'] == 1 ) {
			        $data = array_replace_recursive($data, $sesData);
		        }
	        }
        }

        $this->integrado = new stdClass();
		$this->integrado->balance   = $this->get('balance');
        $this->actionUrl            = !isset($data['confirmacion']) ? JRoute::_('index.php?option=com_mandatos&view=odrform&confirmacion=1') : '#';
        $this->datos                = $data;

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

        if(!$this->permisos['canAuth'] && !$this->permisos['canEdit'] ){
            JFactory::getApplication()->enqueueMessage(JText::_('LBL_DOES_NOT_HAVE_PERMISSIONS'), 'error');
            JFactory::getApplication()->redirect('index.php?option=com_mandatos&view=odvlist');
        }

		parent::display($tpl);
	}
}