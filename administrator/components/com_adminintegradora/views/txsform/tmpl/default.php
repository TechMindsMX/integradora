<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
$integrados = $this->integrados;

var_dump($integrados);
?>

<form>
    <div>
        <select id="integradoId" name="integradoId">
            <?php foreach ($integrados as $key => $integrados) { ?>

            <?php } ?>
        </select>
    </div>
</form>
