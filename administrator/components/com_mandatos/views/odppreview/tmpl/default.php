<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');

$document	= JFactory::getDocument();
$app 		= JFactory::getApplication();

echo $this->loadTemplate('body');
echo $this->loadTemplate('preview_btns');
