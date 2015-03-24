<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
$facturas = $this->facturas;
?>
<div class="table-responsive">
    <table id="myTable" class="table table-bordered tablesorter">
        <thead>
        <tr>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ORDENES_NUM_FACT'); ?></span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ORDENES_FECHA_ORDEN'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('LBL_RECEPTOR'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ORDENES_MONTO'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if( !is_null($facturas) ){
            foreach($facturas as $factura){
            ?>
                <tr>
                    <td><?php echo $factura->id; ?></td>
                    <td><?php echo date('d-m-Y',$factura->createdDate); ?></td>
                    <td><?php echo $factura->detalleFact->receptor['attrs']['NOMBRE']; ?></td>
                    <td>$<?php echo number_format($factura->detalleFact->comprobante['TOTAL'],2); ?></td>
                    <td><a class="btn btn-primary" target="_blank" href="../<?php echo $factura->urlXML; ?>"><?php echo JText::_('COM_ADMININTEGRADORA_ABRIR_FACTURA'); ?></a> </td>
                </tr>
            <?php
            }
        }else{
            JFactory::getApplication()->enqueueMessage(JText::_('MSG_NO_PRODUCTS'));
        }
        ?>
        </tbody>
    </table>
</div>
