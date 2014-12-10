<?php
// no direct access
defined('_JEXEC') or die;
?>

<form id="adminForm" name="adminForm" method="post" action="/Integradora/administrator/index.php?option=com_integrado">

    <table class="adminlist table">
        <thead>
        <tr>
            <th><?php echo JText::_('COM_FACTURAS_FROM_ODD_REFERENCIA'); ?></th>
            <th><?php echo JText::_('LBL_INTEGRADO'); ?></th>
            <th><?php echo JText::_('LBL_CANTIDAD'); ?></th>
            <th><?php echo JText::_('LBL_FECHA'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($this->stp as $key => $value) { ?>

        <tr class="row0">
            <td><a title="Ver detalle de Referencia" ><?php echo $value->referencia; ?></a></td>
            <td><?php echo $value->integradoName; ?></td>
            <td><?php echo '$'.number_format($value->amount, 2); ?></td>
            <td><?php echo date("d/m/Y",$value->date); ?></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</form>