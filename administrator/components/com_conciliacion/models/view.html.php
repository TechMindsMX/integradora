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

/**
 * View class for a list of Donde_comprar.
 */
class DcomprarViewEliminar extends JViewLegacy {

    /**
     * Display the view
     */
   
    
    public function display($tpl = null) {
       $dat=(JRequest::get("get"));       
              
        $this->assignRef("datawhere", $this->getModel()->getDataWhere($dat['etr']));
        
        //$this->assignRef("data", $this->getModel()->getData());
        
        
        parent::display($tpl);
    }

}


