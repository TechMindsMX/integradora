<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class MandatosViewSolicitudliquidacion extends JViewLegacy {

        protected $integradoId;

        function display($tpl = null){
                $session            = JFactory::getSession();
                $this->integradoId  = $session->get( 'integradoId', null, 'integrado' );

                $this->saldo 		= $this->get('saldo');
                $this->operaciones  = $this->get('operaciones');

                $this->loadHelper('Mandatos');

                foreach ($this->operaciones as $key => $value) {
                        $value->beneficiary = MandatosHelper::getClientsFromID($value->clientId, $this->integradoId);
                }

                // Check for errors.
                if (count($errors = $this->get('Errors'))){
                        JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
                        return false;
                }

                parent::display($tpl);
        }
}