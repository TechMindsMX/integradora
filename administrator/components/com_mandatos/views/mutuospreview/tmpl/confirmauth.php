<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');

$document	= JFactory::getDocument();
$app 		= JFactory::getApplication();

$document->addScript('../libraries/integradora/js/confirm-btns.js', "text/javascript",true);
// Datos
$params 	= $app->input->getArray();

$integrado 	= $this->integCurrent->integrados[0];

echo $this->loadTemplate('confirm_btns');
echo $this->loadTemplate('body');