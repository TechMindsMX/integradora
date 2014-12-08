<?php
/**
 * @version     1.0.0
 * @package     com_facturasporcobrar
 * @copyright   Copyright (C) 2014. Todos los derechos reservados.
 * @license     Licencia Pública General GNU versión 2 o posterior. Consulte LICENSE.txt
 * @author      Nestor Ismael Aguilar Estrada <aguilar_2001@hotmail.com> - http://
 */


// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_facturasporcobrar')) 
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JControllerLegacy::getInstance('Facturasporcobrar');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
