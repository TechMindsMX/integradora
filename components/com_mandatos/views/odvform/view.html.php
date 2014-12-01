<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOdvform extends JViewLegacy {

    function display($tpl = null){
        $inputVars 		    = JFactory::getApplication()->input->getArray();
        $this->integradoId  = $inputVars['integradoId'];
        $this->clientes     = $this->get('clientes');
        $this->proyectos    = $this->get('proyectos');
        $this->estados      = $this->get('estados');
        $this->solicitud    = $this->get('datosSolicitud');
        $this->products     = $this->get('productos');
        $this->cuentas      = $this->get('Cuentas');

        if(isset($inputVars['idOrden'])){
            $this->orden = $this->get('Orden');
        }else{
            $orden = new stdClass();

            $orden->id            = '';
            $orden->integradoId   = $this->integradoId;
            $orden->numOrden      = '';
            $orden->projectId     = '';
            $orden->projectId2    = '';
            $orden->clientId      = '';
            $orden->account       = '';
            $orden->paymentMethod = '';
            $orden->conditions    = '';
            $orden->placeIssue    = '';
            $orden->productos     = '';
            $orden->created       = '';
            $orden->payment       = '';
            $orden->status        = '';
            $orden->creatednumero = '';
            $orden->paymentnumero = '';

            $this->orden = $orden;
        }

        if (count($errors = $this->get('Errors'))) {
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
            return false;
        }


        parent::display($tpl);
    }
}
