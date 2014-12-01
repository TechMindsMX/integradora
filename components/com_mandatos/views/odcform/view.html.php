<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOdcform extends JViewLegacy {

    function display($tpl = null){
        $app	            = JFactory::getApplication();
        $post               = array(
            'idOrden'       => 'INT',
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
        $integradoId        = JFactory::getSession()->get('integradoId', null,'integrado');
        $this->integradoId	= isset($integradoId)?$integradoId:$data['integradoId'];
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

                JFactory::getApplication()->redirect('index.php?option=com_mandatos&view=odcform&integradoId='.$data['integradoId'].'&id='.$data['id']);
            }else {
                $this->dataXML = $this->get('data2xml');
            }
        }else {
            if (!is_null($data['idOrden']) && $data['idOrden'] != 0) {
                $this->orden = $this->get('Orden');
            } else {
                $ordenes 				= new stdClass;

                $ordenes->id            = '0';
                $ordenes->proyecto		= '';
                $ordenes->proveedor		= '';
                $ordenes->integradoId	= '';
                $ordenes->numOrden		= '0';
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