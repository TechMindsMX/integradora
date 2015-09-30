<?php
defined('_JEXEC') or die('Restricted access');
?>
<script>
 jQuery(document).ready(function(){
     jQuery(document).ready(function(){
        jQuery('#cancelar').click(function(){
            window.history.back();
        });
     });
 });
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

<form action="index.php?option=com_adminintegradora&view=txsform" method="post">
    <input type="hidden" name="task" value="txsform.save" />
    <input type="hidden" name="idtx" value="<?php echo $this->datosConfirmacion->txInfo->id; ?>" />
    <input type="hidden" name="integradoId" value="<?php echo $this->datosConfirmacion->integrado->getId(); ?>" />
    <input type="hidden" name="integradoIdPagador" value="<?php echo $this->datosConfirmacion->txInfo->integradoId; ?>" />
    <input type="hidden" name="monto" value="<?php echo $this->datosConfirmacion->txInfo->amount; ?>" />
    <input type="hidden" name="cuenta" value="<?php echo $this->datosConfirmacion->txInfo->cuenta->datosBan_id; ?>" />

    <input type="submit" class="btn btn-primary" value="Confirmar" />
    <input type="button" class="btn btn-danger cancelar" id="cancelar" value="Cancelar" />
</form>
