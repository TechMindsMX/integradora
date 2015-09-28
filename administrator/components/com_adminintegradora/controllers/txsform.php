<?php
/**
 * @version     1.0.1
 * @copyright   Copyright (C) 2014. Todos los derechos reservados.
 * @license     Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 * @author      Lutek <luis.magana@techminds.com.mx>
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');
jimport('integradora.validator');
jimport('integradora.gettimone');
jimport('integradora.notifications');

class AdminintegradoraControllertxsform extends JControllerAdmin{
    public $id_tx_banco;
    protected $data;

    public function save(){
    	JFactory::getApplication()->input->getArrya();
    }
}