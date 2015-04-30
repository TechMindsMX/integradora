<?php
defined('_JEXEC') or die('Restricted access');

// import joomla controller library
jimport('joomla.application.component.controller');

jimport('joomla.log.log');

$scope = JFactory::getApplication()->scope;
JLog::addLogger(array('text_file' => date('d-m-Y').'_'.$scope.'_errors.php', 'text_entry_format' => '{DATETIME} {PRIORITY} {MESSAGE} {CLIENTIP}'), JLog::ALL & ~JLog::WARNING & ~JLog::INFO & ~JLog::DEBUG);
JLog::addLogger(array('text_file' => date('d-m-Y').'_'.$scope.'_bitacora.php', 'text_entry_format' => '{DATETIME} {PRIORITY} {MESSAGE} {CLIENTIP}'), JLog::INFO + JLog::DEBUG, 'bitacora');

// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('Integrado');
 
// Perform the Request task
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
 
// Redirect if set by the controller
$controller->redirect();