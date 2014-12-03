<?php

/**
 * @version     1.0.1
 * @package     com_donde_comprar
 * @copyright   Copyright (C) 2014. Todos los derechos reservados.
 * @license     Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 * @author      ismael <aguilar_2001@hotmail.com> - http://
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');


class ConciliacionbancoViewConciliacionbanco extends JViewLegacy {

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/conciliacionbanco.php';
        JToolBarHelper::title(JText::_('COM_CONCILIACION_BANCO_TITLE'), '');


    }

}


