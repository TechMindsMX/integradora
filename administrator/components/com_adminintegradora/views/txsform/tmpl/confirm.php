<?php
defined('_JEXEC') or die('Restricted access');
?>
<script>

</script>
<h1><?php echo JText::_('IDENTIFICAR_CONFIRMACION'); ?></h1>

<div>
    <h4>Esta seguro de ligar la siguiente transaccion al integrado: <strong><?php echo $this->datosConfirmacion->integrado->getDisplayName(); ?></strong> </h4>

    <div>Datos de la transacción:</div>
    <div>Fecha: <?php echo $this->datosConfirmacion->txInfo->date; ?></div>
    <div>Banco: <?php echo $this->datosConfirmacion->txInfo->cuenta->bankName; ?></div>
    <div>Cuneta: <?php echo $this->datosConfirmacion->txInfo->cuenta->banco_cuenta; ?></div>
    <div>Monto: $<?php echo number_format($this->datosConfirmacion->txInfo->amount,2); ?></div>
</div>
<br />

<form action="index.php?option=com_adminintegradora&view=txsform&task=save">
    <input type="hidden" name="idtx" value="" />
    <input type="hidden" name="integradoId" value="" />

    <input type="submit" class="btn btn-primary" value="Confirmar" />
    <input type="button" class="btn btn-danger" id="cancelar" value="Cancelar" />
</form>
