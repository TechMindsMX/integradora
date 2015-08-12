<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');
JHtml::_('behavior.keepalive');

$document	= JFactory::getDocument();
$app 		= JFactory::getApplication();
$attsCal    = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');

$default    = isset($this->odr->paymentDate) ? $this->odr->paymentDate : date('Y-m-d');
$amount     = isset($this->odr->totalAmount) ? $this->odr->totalAmount : '';
?>
	<script src="libraries/integradora/js/tim-validation.js"> </script>
	<script>
		jQuery(document).ready(function(){
			jQuery('#amount').on('change', validaSaldo);
			jQuery('input:button').on('click',envio);
		});

		function validaSaldo(){
			var monto           = parseFloat(jQuery(this).val());
			var saldo           = parseFloat(jQuery('#balance').val());
			var errormsg        = jQuery('#errormsg');
			var fielderrormsg   = jQuery('#amount');
			var boton           = jQuery('#btn_submit');

			if(saldo < monto){
				errormsg.text('<?php echo JText::_('COM_MANDATOS_ORDEN_RETIRO_AMOUNT_ERROR'); ?>');
				errormsg.fadeIn();
				fielderrormsg.css('border-color', '#FF0000');
				boton.prop('disabled', true);

				errormsg.delay(800).fadeOut(4000, function(){
					fielderrormsg.css('border-color', '');
					errormsg.text('');
				});
			}else{
				boton.prop('disabled', false);
				fielderrormsg.css('border-color', '');
				errormsg.hide();
				errormsg.text('');
			}
		}

		function envio (){
			var form     = jQuery('#generaODR');
			var datos    = form.serialize();
			var task     = '';
			var buttonId = jQuery(this).prop('id');

			switch(buttonId){
				case 'confirmarodr':
					task = 'odrform.valida';
					break;
				case 'enviar':
					task = 'odrform.saveODR';
					break;
				default :
					break;
			}

			var request = jQuery.ajax({
				url: 'index.php?option=com_mandatos&task='+task+'&format=raw',
				data: datos,
				type: 'post',
				async: false
			});

			request.done(function(result){
				mensajesValidaciones(result);

				var enviar = true;

				jQuery.each(result, function(k, v){
					if(v != true){
						enviar = false;
					}
				});

				if(enviar === true && buttonId === 'confirmarodr'){
					form.submit();
				}

				if(buttonId === 'enviar'){
					if(result.redireccion){
						window.location = result.urlRedireccion;
					}
				}
			});
		}

	</script>


<?php
if(!$this->confirmacion){
?>
<h1><?php echo JText::_('COM_MANDATOS_ORDENES_RETIRO_AGREGAR'); ?></h1>
<div style="margin-bottom: 10px;">
	<h4>
		<span class="label-default"><?php echo JText::_('LBL_BALANCE_AVAILABLE'); ?></span>
		<span>$<?php echo number_format($this->integrado->balance,2); ?></span>
	</h4>
</div>

<form id="generaODR" action="<?php echo $this->actionUrl; ?>" method="post" enctype="multipart/form-data" autocomplete="off">
	<div class="form-group">
		<label for="paymentDate"><?php echo JText::_('LBL_PAYMENT_DATE'); ?></label>
		<?php
		echo JHTML::_('calendar',$default,'paymentDate', 'paymentDate', $format = '%Y-%m-%d', $attsCal);
		?>
	</div>
	<input type="hidden" id="balance" value="<?php echo $this->integrado->balance; ?>">
    <input type="hidden" id="integradoId" name="integradoId" value="<?php echo $this->integradoId; ?>">
	<input type="hidden" value="<?php echo isset($this->odr->id) ? $this->odr->id : ''; ?>" name="idOrden" />


	<div class="form-group">
        <label for="paymentMethod"><?php echo JText::_('LBL_FORMA_PAGO'); ?></label>
        <select id="paymentMethod" name="paymentMethod">
	        <?php
	        $cat = new Catalogos();
	        foreach ( $cat->getPaymentMethods() as $key => $val ) {
		        if ( isset( $this->odr->paymentMethod ) ) {
			        $selected = ($this->odr->paymentMethod->id == $key) ? ' selected ' : '';
		        }
		        echo '<option value="'.$key.'"'.$selected.'>'.JText::_($val->tag).'</option>';
	        }
	        ?>
        </select>
    </div>

	<div class="form-group">
        <label for="amount"><?php echo JText::_('LBL_AMOUNT_REQUESTED'); ?></label>
        <input type="text" name="totalAmount" id="totalAmount" value="<?php echo $amount; ?>" /> <span id="errormsg" style="display: none;"></span>
    </div>

    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <input type="button" class="btn btn-primary" id="confirmarodr" value="<?php echo JText::_('LBL_ENVIAR'); ?>">
        <input type="button" class="btn btn-danger"  onclick="window.history.back()" value="<?php echo jText::_('LBL_CANCELAR'); ?>" />
    </div>
</form>
<?php
	// confirmación de orden
} else {
    $datos = $this->datos;
    $formadepago = array( JText::_('LBL_SPEI'), JText::_('LBL_CHEQUE') );
?>
	<h1><?php echo JText::_('COM_MANDATOS_ORDENES_RETIRO_LBL_CONFIMACION'); ?></h1>

	<div class="form-group">
		<span class="label-default"><?php echo JText::_('LBL_FORMA_PAGO'); ?>: </span>
    <span>
        <?php echo $formadepago[$datos['paymentMethod']]; ?>
    </span>
	</div>
	<div class="form-group">
		<span class="label-default"><?php echo JText::_('LBL_AMOUNT_REQUESTED'); ?>: </span>
    <span>
        $<?php echo number_format($datos['totalAmount'],2 ); ?>
    </span>
	</div>
	<div class="clearfix">&nbsp;</div>

	<form id="generaODR" autocomplete="off">
		<div class="form-group">
			<input type="hidden" value="<?php echo $this->integradoId; ?>" name="integradoId" />
			<input type="hidden" value="<?php echo $datos['paymentMethod']; ?>" name="paymentMethod" />
			<input type="hidden" value="<?php echo $datos['paymentDate']; ?>" name="paymentDate" />
			<input type="hidden" value="<?php echo $datos['totalAmount']; ?>" name="totalAmount" />
			<input type="hidden" value="<?php echo isset($datos['idOrden'])?$datos['idOrden']:''; ?>" name="idOrden" />
			<?php echo JHtml::_( 'form.token' ); ?>

			<input type="button" id="enviar" class="btn btn-primary" value="<?php echo JText::_('LBL_ENVIAR'); ?>">
			<input type="button" class="btn btn-danger"  onclick="window.history.back()" value="<?php echo jText::_('LBL_CANCELAR'); ?>" />
		</div>
	</form>
<?php

}
?>