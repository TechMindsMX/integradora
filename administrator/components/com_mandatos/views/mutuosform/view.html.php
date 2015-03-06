<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('integradora.gettimone');

class MandatosViewMutuosform extends JViewLegacy {

    function display($tpl = null){
        $model              = $this->getModel();
        $app                = JFactory::getApplication();
        $this->data			= $this->get('InputData');

        if($this->data->layout === 'confirm'){
            $this->data->jsonTabla = MandatosModelMutuosform::getTablaAmortizacion($this->data);
        }

        $this->integradoId 	= $this->data->integradoId;
        $this->idMutuo      = $this->data->id;
        $this->tipoPago     = $this->get('TiposPago');
        $this->catalogos    = $this->get('catalogos');
        $this->montoSaldo   = $this->get('MontoSaldo');

        if( ($this->idMutuo != 0) and ($this->data->layout != 'confirm') ){
            $this->data = $model->getMutuo($this->idMutuo);
        }elseif($this->idMutuo == 0 && $this->data->layout != 'confirm'){
            $objeto = new stdClass();

            $objeto->id                 = null;
            $objeto->integradoIdE       = null;
            $objeto->integradoIdR       = null;
            $objeto->idCuenta           = null;
            $objeto->paymentPeriod      = null;
            $objeto->quantityPayments   = null;
            $objeto->jsonTabla          = null;
            $objeto->totalAmount        = null;
            $objeto->interes            = null;
            $objeto->cuotaOcapital      = null;
            $objeto->status             = null;
            $objeto->integradoAcredor   = new stdClass();
            $objeto->integradoAcredor->nombre           = null;
            $objeto->integradoAcredor->rfc              = null;
            $objeto->integradoAcredor->datosBancarios   = array();

            $objeto->integradoDeudor    = new stdClass();
            $objeto->integradoDeudor->nombre           = null;
            $objeto->integradoDeudor->rfc              = null;
            $objeto->integradoDeudor->datosBancarios   = array();

            $this->data = $objeto;
        }

        $this->titulo   = 'COM_MANDATOS_MUTUO_LBL_EDITAR';

        // Check for errors.
        if (count($errors = $this->get('Errors'))){
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
            return false;
        }

        $this->loadHelper('Mandatos');

        // Verifica los permisos de edición y autorización
        $this->permisos = MandatosHelper::checkPermisos(__CLASS__, $this->integradoId);

        if (!$this->permisos['canEdit']) {
            $url = 'index.php?option=com_mandatos&view=clienteslist&integradoId='.$this->integradoId;
            $msg = JText::_('JERROR_ALERTNOAUTHOR');
            $app->redirect(JRoute::_($url), $msg, 'error');
        }

        parent::display($tpl);
    }
}