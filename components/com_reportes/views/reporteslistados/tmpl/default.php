<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

echo '<h1>'.JText::_('COM_REPORTES_TITLE_LISTADOS').'</h1>';
?>
<div style="margin-top: 50px;">
   mostrar listado de los reportes
</div>
