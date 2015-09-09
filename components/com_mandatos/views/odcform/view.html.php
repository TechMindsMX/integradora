<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

class MandatosViewOdcform extends JViewLegacy
{

    protected $integradoId;
    protected $permisos;

    function display($tpl = null)
    {
        $app               = JFactory::getApplication();
        $post              = array (
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
        $data              = $app->input->getArray($post);
        $session           = JFactory::getSession();
        $this->integradoId = $session->get('integradoId', null, 'integrado');

        $model             = $this->getModel();
        $this->proyectos   = $model->getProyectos();
        $this->proveedores = $model->getProviders();
        $this->bancos      = $model->getCatalogoBancos();

        $this->catalogos                 = new stdClass();
        $this->catalogos->paymentMethods = $model->getCatalogos();

        //si la confirmacion es diferente de nulo se hace el parseo del XML
        if ( ! is_null($data['confirmacion'])) {
            $this->datos = $data;
            //validación del XML
            try {
                $model->validateFileSizeAndExtension($_FILES['factura'], 'text/xml');
                $model->validateFileSizeAndExtension($_FILES['facturaPdf'], 'application/pdf');

                $model->sendXmlForExternalValidation($_FILES['factura']);

                $this->dataXML = $model->getdata2xml($_FILES['factura']['tmp_name'], $_FILES['factura']['name']);

                $emisor = new IntegradoSimple($this->datos['proveedor']);
                $receptor = new IntegradoSimple($this->integradoId);
                $model->checkXmlClientAndProvider($this->dataXML, $emisor, $receptor);

                $model->saveOdcPdfFile($_FILES['facturaPdf']);

            } catch (Exception $e) {
                $this->redirectforNotfiles($data, $e->getMessage());
                unlink($this->dataXml->urlXML);
            }

            //Validación del PDF
        } else {
            if ( ! is_null($data['idOrden']) && $data['idOrden'] != 0) {
                $this->orden = $model->getOrden();
            } else {
                $ordenes = new stdClass;

                $ordenes->id            = '0';
                $ordenes->proyecto      = '';
                $ordenes->proveedor     = '';
                $ordenes->integradoId   = '';
                $ordenes->numOrden      = '0';
                $ordenes->paymentDate   = '';
                $ordenes->paymentMethod = '';
                $ordenes->observaciones = '';

                $this->orden = $ordenes;
            }
        }

        if (count($errors = $this->get('Errors'))) {
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

            return false;
        }

        $this->loadHelper('Mandatos');
        $this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

        if ( ! $this->permisos['canEdit']) {
            $url = 'index.php?option=com_mandatos&view=odclist';
            $msg = JText::_('LBL_DOES_NOT_HAVE_PERMISSIONS');
            $app->redirect(JRoute::_($url), $msg, 'error');
        }

        parent::display($tpl);
    }

    private function redirectforNotfiles($data, $msg)
    {
        $sesion     = JFactory::getSession();
        $objeto     = (object) $data;
        $objeto->id = $objeto->idOrden;
        //$sesion->set('datos',$objeto, 'misdatos');
        $sesion->set('msg', $msg, 'misdatos');

        JFactory::getApplication()->redirect('index.php?option=com_mandatos&view=odcform&idOrden=' . $data['idOrden']);
    }

}