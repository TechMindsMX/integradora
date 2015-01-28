<?php
defined('_JEXEC') or die('Restricted access');

// import joomla controller library
jimport('joomla.application.component.controller');
jimport('joomla.log.log');

JLog::addLogger(array('text_file' => date('d-m-Y').'_com_mandatos_errors.php'), JLog::ALL & ~JLog::INFO & ~JLog::DEBUG);
JLog::addLogger(array('text_file' => date('d-m-Y').'_com_mandatos_bitacora.php'), JLog::INFO + JLog::DEBUG, 'bitacora');


// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('Mandatos');
 
// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();