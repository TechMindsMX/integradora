<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOddform extends JViewLegacy {

    private $integradoId;

    function display($tpl = null){
		$app 				= JFactory::getApplication();
		$data				= $app->input->getArray();

        $session = JFactory::getSession();
        $this->integradoId    = $session->get('integradoId', null, 'integrado');

		$this->odd		 	= $this->get('orden');
        $this->actionUrl    = !isset($data['confirmacion']) ? JRoute::_('index.php?option=com_mandatos&view=oddform&confirmacion=1') : '#';
        $this->datos        = $data;

        if(isset($_FILES['attachment']['size'])){
            if($_FILES['attachment']['size'] != 0) {
                $this->file['name'] = $_FILES['attachment']['name'];
                $this->file['ruta'] = manejoImagenes::cargar_imagen('image/jpeg', $this->integradoId, $_FILES['attachment'], 'odd_attachment');

                if($this->file['ruta'] == 'verificar') {
                    $msg = JText::_('UNSUPPORTED_FILE').'<br /> '.JText::sprintf('LBL_FILE_TYPES_ALLOWED', 'JPG, PNG, GIF, PDF').'. '.JText::sprintf('LBL_MAX_FILE_SIZE', '10MB').'. ';
                    $app->redirect('index.php?option=com_mandatos&view=oddform', $msg, 'error');
                }
            }else{
                $this->file['name'] = '';
                $this->file['ruta'] = '';
            }
        }


        if(isset($data['confirmacion'])){
            $this->confirmacion = true;
            $this->datos        = $data;
        }else{
            $this->confirmacion = false;
            if( isset($data['idOrden']) ) {
                $this->datos = $this->odd[0];
            }else{
                $datos = new stdClass();
                $datos->paymentMethod = new stdClass();

                $datos->id = '';
                $datos->numOrden = '';
                $datos->paymentDate = '';
                $datos->totalAmount = '';
                $datos->paymentMethod->id = 0;
                $datos->attachment = '';

                $this->datos = $datos;
            }
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