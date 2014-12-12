<?php
// no direct access
defined('_JEXEC') or die;

$base_url = 'index.php?option=com_conciliacion&view=detalle&txId=';
?>

<form id="adminForm" name="adminForm" method="post" action="/Integradora/administrator/index.php?option=com_comciliacion">

    <table class="adminlist table">
        <thead>
        <tr>
            <th><?php echo JText::_('COM_FACTURAS_FROM_ODD_REFERENCIA'); ?></th>
            <th><?php echo JText::_('LBL_INTEGRADO'); ?></th>
            <th><?php echo JText::_('LBL_CANTIDAD'); ?></th>
            <th><?php echo JText::_('LBL_FECHA'); ?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php if ( ! empty( $this->stp ) ) {
	        foreach ($this->stp as $key => $value) {
		        ?>
	        <tr class="row0">
	            <td><a title="Ver detalle de Referencia" ><?php echo $value->data->referencia; ?></a></td>
	            <td><?php echo $value->integradoName; ?></td>
	            <td><?php echo '$'.number_format($value->data->amount, 2); ?></td>
	            <td><?php echo date("d/m/Y",$value->data->date); ?></td>
	            <td><?php echo '<a class="btn btn-success" href="'.$base_url.$value->idTx.'">'.JText::_('LBL_ASOCIAR_MANDATO').'</a>'; ?></td>
	        </tr>
	        <?php }
        } else {
	        ?>
            <tr>
	            <td colspan="4"><?php echo JText::_('LBL_SIN_TX'); ?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</form>