<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');

$document	= JFactory::getDocument();

$document->addScript('libraries/integradora/js/confirm-btns.js');

$integrado 	= $this->integCurrent->integrados[0];

echo $this->loadTemplate('confirm_btns');
echo $this->loadTemplate('body');