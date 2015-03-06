<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewOdvform extends JViewLegacy {

    protected $integradoId;

    function display($tpl = null){
        $session            = JFactory::getSession();
        $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

        $inputVars 		    = JFactory::getApplication()->input->getArray();
        $this->clientes     = $this->get('clientes');
        $this->proyectos    = $this->get('proyectos');
        $this->subprojects  = $this->get('Subprojects');
        $this->estados      = $this->get('estados');
        $this->solicitud    = $this->get('datosSolicitud');
        $this->products     = $this->get('productos');
        $this->cuentas      = $this->get('Cuentas');
        $this->catalogoIva  = $this->get('CatalogoIva');

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
            $orden->paymentMethod->id = 0;
            $orden->conditions    = '';
            $orden->placeIssue    = '';
            $orden->productos     = '';
            $orden->createdDate   = '';
            $orden->paymentDate   = '';
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
