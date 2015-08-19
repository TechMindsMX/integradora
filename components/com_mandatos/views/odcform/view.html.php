<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOdcform extends JViewLegacy {

    protected $integradoId;
    protected $permisos;

    function display($tpl = null){
        $app	            = JFactory::getApplication();
        $post               = array(
            'idOrden'       => 'INT',
            'integradoId'   => 'STRING',
            'confirmacion'  => 'INT',
            'numOrden'      => 'INT',
            'proyecto'      => 'INT',
            'proveedor'     => 'STRING',
            'bankId'        => 'INT',
            'paymentDate'   => 'STRING',
            'paymentMethod' => 'STRING',
            'observaciones' => 'STRING'
        );
        $data	            = $app->input->getArray($post);
        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );
        $this->proyectos 	= $this->get('proyectos');
        $this->proveedores	= $this->get('providers');
        $this->bancos       = $this->get('CatalogoBancos');

	    $this->catalogos = new stdClass();
	    $this->catalogos->paymentMethods    = $this->get('Catalogos');

        //si la confirmacion es diferente de nulo se hace el parseo del XML
        if(!is_null($data['confirmacion'])){
            $this->datos = $data;
            //validación del XML
            if($_FILES['factura']['size'] === 0 && $_FILES['factura']['type'] === 'text/xml'){
                $this->redirectforNotfiles($data);
            }else {
                $this->dataXML = $this->get('data2xml');
            }

            //Validación del PDF
            if($_FILES['facturaPdf']['size'] === 0 && $_FILES['factura']['type'] === 'application/pdf'){
                $this->redirectforNotfiles($data);
            }else{
                move_uploaded_file($_FILES['facturaPdf']['tmp_name'], "media/pdf_odc/" . $_FILES['facturaPdf']['name']);
                $this->datos['urlPDF'] = "media/pdf_odc/" . $_FILES['facturaPdf']['name'];
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
            $url = 'index.php?option=com_mandatos&view=odclist';
            $msg = JText::_('LBL_DOES_NOT_HAVE_PERMISSIONS');
            $app->redirect(JRoute::_($url), $msg, 'error');
        }

        parent::display($tpl);
    }

    private function redirectforNotfiles($data){
        $sesion = JFactory::getSession();
        $objeto = (object)$data;
        $objeto->id = $objeto->idOrden;
        //$sesion->set('datos',$objeto, 'misdatos');
        $sesion->set('msg','Falta el Archivo XML', 'misdatos');

        JFactory::getApplication()->redirect('index.php?option=com_mandatos&view=odcform&idOrden='.$data['idOrden']);
    }
}