<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
$facturas = $this->facturas;
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$vName = 'listadofactcomisiones';

JSubMenuHelper::addEntry(
    JText::_('COM_FACTURAS_FACTURAS'),
    'index.php?option=com_facturas',
    $vName == 'facturas');

JSubMenuHelper::addEntry(
    JText::_('COM_FACTURAS_LISTADO_FACT_COMISIONES'),
    'index.php?option=com_facturas&view=factcomisioneslist',
    $vName == 'listadofactcomisiones');
?>
<div class="table-responsive">
    <table id="myTable" class="table table-bordered tablesorter">
        <thead>
        <tr>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ORDENES_NUM_FACT'); ?></span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ORDENES_FECHA_ORDEN'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('LBL_BENEFICIARIO'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ORDENES_MONTO'); ?> </span> </th>
        </tr>
        </thead>
        <tbody>
        <?php
        if( !is_null($facturas) ){
            foreach ($facturas as $key => $value) {

                echo '<tr class="client_'.$value->receptor.'">';
                echo '	<td style="text-align: center; vertical-align: middle;" class="margen-fila" >'.$value->id.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="" >'.$value->created.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="" >'.$value->userName.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="" >$'.number_format($value->totalAmount,2).'</td>';
                echo '</tr>';
            }
        }else{
            JFactory::getApplication()->enqueueMessage(JText::_('MSG_NO_PRODUCTS'));
        }
        ?>
        </tbody>
    </table>
</div>
