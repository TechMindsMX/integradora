<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOdcform extends JViewLegacy {

    function display($tpl = null){
        $app	= JFactory::getApplication();
        $post   = array(
            'integradoId'   => 'INT',
            'confirmacion'  => 'INT',
            'numOrden'      => 'INT',
            'proyecto'      => 'INT',
            'proveedor'     => 'INT',
            'paymentDate'   => 'STRING',
            'paymentMethod' => 'STRING',
            'observaciones' => 'STRING'
        );
        $data	            = $app->input->getArray($post);
        $this->integradoId	= $data['integradoId'];
        $this->proyectos 	= $this->get('proyectos');
        $this->proveedores	= $this->get('providers');

        //si la confirmacion es diferente de nulo se hace el parseo del XML
        if(!is_null($data['confirmacion'])){
            $this->datos = $data;
            if($_FILES['factura']['size'] === 0){
                $sesion = JFactory::getSession();
                $objeto = (object)$data;
                $sesion->set('datos',$objeto, 'misdatos');
                $sesion->set('msg','Falta el Archivo XML', 'misdatos');

                JFactory::getApplication()->redirect('index.php?option=com_mandatos&view=odcform&integradoId='.$data['integradoId'].'&numOrden='.$data['numOrden']);
            }else {
                $this->dataXML = $this->get('data2xml');
            }
        }else {
            if (!is_null($data['numOrden']) && $data['numOrden'] != 0) {
                $this->orden = $this->get('Orden');
            } else {
                $ordenes 				= new stdClass;
                $ordenes->proyecto		= '';
                $ordenes->proveedor		= '';
                $ordenes->integradoId	= '';
                $ordenes->numOrden		= '';
                $ordenes->paymentDate	= '';
                $ordenes->paymentMethod	= '';
                $ordenes->observaciones	= '';

                $this->orden            = $ordenes;
            }
        }

        if (count($errors = $this->get('Errors'))) {
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
            return false;
        }

        $this->loadHelper('Mandatos');
        $this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

        if (!$this->permisos['canEdit']) {
            $url = 'index.php?option=com_mandatos&view=odclist&integradoId='.$this->integradoId;
            $msg = JText::_('JERROR_ALERTNOAUTHOR');
            $app->redirect(JRoute::_($url), $msg, 'error');
        }

        parent::display($tpl);
    }
}