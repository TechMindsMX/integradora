<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');

$document	= JFactory::getDocument();

$integrado 	= $this->integCurrent->integrados[0];

echo $this->loadTemplate('body');
echo $this->loadTemplate('preview_btns');
