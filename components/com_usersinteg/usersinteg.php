<?php
defined('_JEXEC') or die('Restricted Access');

$app = JFactory::getApplication();
$currUser	= JFactory::getUser();

$controller = JControllerLegacy::getInstance('UsersInteg');

$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));

$controller->redirect();