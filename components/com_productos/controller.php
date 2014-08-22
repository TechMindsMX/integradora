<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('integradora.validator');
jimport('integradora.integrado');
jimport('integradora.imagenes');
$app = JFactory::getApplication();
$currUser	= JFactory::getUser();
if($currUser->guest){
	$app->redirect('index.php/login');
}


class ProductosController extends JControllerLegacy {
}