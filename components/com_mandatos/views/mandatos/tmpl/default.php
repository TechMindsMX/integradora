<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$integrados = $this->data;

echo '<h1>'.JText::_('COM_MANDATOS_TITULO').'</h1>';
?>
<script type="application/javascript">
	function redirect() {
		var campo = jQuery(this);
		var ruta = campo.prop('id');
		var form = jQuery('#form_listados');
		var action = "<?php echo JRoute::_('index.php?option=com_mandatos&task=mandatos.route'); ?>";

		jQuery('#ruta').val(ruta);

		form.prop('action', action);

		form.submit();
	}
	function habilitaBtns() {
		var btns = jQuery('.btn');
		var select = jQuery(this);

		if(select.val() > 0){
			btns.prop('disabled', false);
		}else if(select.val() == 0){
			btns.prop('disabled', true);
		}
	}
	jQuery(document).ready(function(){
		jQuery('#integradoId').on('change',habilitaBtns);
		jQuery('.btn').on('click', redirect);
	});
</script>
<form id="form_listados" method="post">
	<select name="integradoId" id="integradoId">
		<option value="0">Seleccione Integrado</option>
		<?php
		foreach ($integrados as $value) {
			echo '<option value="'.$value->integradoId.'">'.$value->name.'</option>';
		}
		?>
	</select>
	<input type="hidden" name="ruta" id="ruta" value="" />
</form>

<div class="col-xs-6">
	<div class="control-group">
		<div class="margen-fila">
			<input type="button" class="btn btn-primary" id="list_proyectos"  disabled="disabled" value="<?php echo JText::_('COM_MANDATOS_LISTAD_PROYECTOS'); ?>" />
		</div>
		<div class="margen-fila">
			<input type="button" class="btn btn-primary" id="list_productos"  disabled="disabled" value="<?php echo JText::_('COM_MANDATOS_LISTAD_PRODUCTOS'); ?>" />
		</div>
		<div class="margen-fila">
			<input type="button" class="btn btn-primary" id="list_clientes"  disabled="disabled" value="<?php echo JText::_('COM_MANDATOS_LISTAD_CLIENTES'); ?>" />
		</div>
		<div class="margen-fila">
			<input type="button" class="btn btn-primary" id="list_odc"  disabled="disabled" value="<?php echo JText::_('COM_MANDATOS_LISTAD_ORDENES'); ?>" />
		</div>
		<div class="margen-fila">
			<input type="button" class="btn btn-primary" id="list_odd"  disabled="disabled" value="<?php echo JText::_('COM_MANDATOS_LISTAD_ORDENES_DEPOSITO'); ?>" />
		</div>
		<div class="margen-fila">
			<input type="button" class="btn btn-primary" id="list_odr"  disabled="disabled" value="<?php echo JText::_('COM_MANDATOS_ORDENES_RETIRO_LISTADO'); ?>" />
		</div>
		<div class="margen-fila">
			<input type="button" class="btn btn-primary" id="list_odv"  disabled="disabled" value="<?php echo JText::_('COM_MANDATOS_ODV_LIST'); ?>" />
		</div>
		<div class="margen-fila">
			<input type="button" class="btn btn-primary" id="list_fv"  disabled="disabled" value="<?php echo JText::_('COM_MANDATOS_FACTURA_LIST'); ?>" />
		</div>
		<div class="margen-fila">
			<input type="button" class="btn btn-primary" id="list_mutos"  disabled="disabled" value="<?php echo JText::_('COM_MANDATOS_MUTUOS'); ?>" />
		</div>
	</div>
</div>

<div class="col-xs-6">
	<div class="control-group">
		<div class="margen-fila">
			<input type="button" class="btn btn-primary" id="go_liquidacion"  disabled="disabled" value="<?php echo JText::_('COM_MANDATOS_GO_LIQUIDACION'); ?>" />
		</div>
		<div class="margen-fila">
			<input type="button" class="btn btn-primary" id="tx_sin_mandato"  disabled="disabled" value="<?php echo JText::_('COM_MANDATOS_LIST_TX_SIN_MANDATO_TITLE'); ?>" />
		</div>
	</div>
</div>