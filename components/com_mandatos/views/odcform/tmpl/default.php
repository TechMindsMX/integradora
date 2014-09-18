<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');
if(!isset($this->datos['confirmacion'])){
?>
<script>
	jQuery(document).ready(function(){
		jQuery('#proveedor').on('change', muestraboton);
		jQuery('#agregarProveedor').on('click', agregaProveedor);
	});
	
	function muestraboton(){
		var valorCampo = jQuery(this).val();
		
		if(valorCampo == 'other'){
			jQuery('#agregarProveedor').show();
		}else{
			jQuery('#agregarProveedor').hide();
		}
	}
	
	function agregaProveedor(){
		console.log(this);
	}
</script>

<h1><?php echo JText::_('COM_MANDATOS_ODC_FORM_TITULO'); ?></h1>

<form id="generaODC" method="post" action="<?php echo JRoute::_('index.php?option=com_mandatos&view=odcform&integradoId=1&confirmacion=1') ?>" role="form" enctype="multipart/form-data">
	<div class="form-group">
		<label for="proyecto"><?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_TH_NAME_PROYECTO') ?></label>
		<select id="proyecto" name="project">
			<?php
			foreach ($this->proyectos as $key => $value) {
				echo '<option value="'.$value->id.'">'.$value->name.'</option>';
			} 
			?>
		</select>
	</div>
	
	<div class="form-group">
		<label for="proveedor"><?php echo JText::_('LBL_PROVEEDOR') ?></label>
		<select id="proveedor" name="provider">
			<?php
			foreach ($this->proveedores as $key => $value) {
				echo '<option value="'.$value->id.'">'.$value->tradeName.'</option>';
			} 
			?>
			<option value="other"><?php echo JText::_('LBL_OTHER'); ?></option>
		</select>
		
		<div class="form-group" id="agregarProveedor" style="display: none;">
			<input type="button" class="btn btn-primary" value="<?php echo JText::_('LBL_CARGAR') ?>" />
		</div>
	</div>
	
	<div class="form-group">
		<label for="created"><?php echo JText::_('LBL_CREATED'); ?></label>
		<?php 
		$default = date('Y-m-d');
		echo JHTML::_('calendar',$default, 'created', 'created', $format = '%Y-%m-%d', $attsCal);
		?>
	</div>
	
	<div class="form-group">
		<label for="paymentDate"><?php echo JText::_('LBL_PAYMENT_DATE'); ?></label>
		<?php 
		$default = date('Y-m-d');
		echo JHTML::_('calendar',$default, 'paymentDate', 'paymentDate', $format = '%Y-%m-%d', $attsCal);
		?>
	</div>
	
	<div class="form-group">
		<label for="paymentform"><?php echo JText::_('COM_MANDATOS_ODC_PAYMENTFORM'); ?></label>
		<select id="paymentform" name="paymentMethod">
			<option value="0"><?php echo JText::_('LBL_SPEI'); ?></option>
			<option value="1"><?php echo JText::_('LBL_CHEQUE'); ?></option>
		</select>
	</div>
	
	<div class="form-group">
		<label for="factura"><?php echo JText::_('LBL_FACTURA'); ?></label>
		<input type="file" name="factura" id="factura" />
	</div>
	
	<div class="form-group">
		<label for="observaciones"><?php echo JText::_('LBL_OBSERVACIONES'); ?></label>
		<textarea name="observaciones" id="observaciones"></textarea>
	</div>

    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="<?php echo jText::_('LBL_ENVIAR'); ?>" />
        <input type="button" class="btn btn-primary"  onclick="window.history.back()" value="<?php echo jText::_('LBL_CANCELAR'); ?>" />
    </div>
</form>
<?php
}else{
	$created = new DateTime($this->datos['created']);
	$datePayment = new DateTime($this->datos['paymentDate']);

    $comprobante    = $this->dataXML->comprobante;
	$impuestos      = $this->dataXML->impuestos;
	$conceptos      = $this->dataXML->conceptos;
?>
<div id="odc_preview">
	<h1><?php echo JText::_('MANDATOS_ODC_CONFIRMACION'); ?></h1>
	
	<div class="clearfix" id="cabecera">
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_TH_NAME_PROYECTO') ?>
			</div>
			<div class="span4">
				<?php
				foreach ($this->proyectos as $key => $value) {
					if($value->id == $this->datos['project']){
						echo $value->name;
					}
				} 
				?>
			</div>
			
			<div class="span2 text-right">
				<?php echo JText::_('LBL_PROVEEDOR') ?>:
			</div>
			<div class="span4">
				<?php
				foreach ($this->proveedores as $key => $value) {
					if($value->id == $this->datos['provider']){
						echo $value->tradeName;
					}
				} 
				?>
			</div>
		</div>
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('COM_MANDATOS_ODC_PAYMENTFORM'); ?>:
			</div>
			<div class="span4">
				<?php echo $this->datos['paymentMethod']==0?'SPEI':'Cheque'; ?>
			</div>
			
			<div class="span2 text-right">
				<?php echo JText::_('LBL_CREATED'); ?>:
			</div>
			<div class="span4">
				<?php echo $created->format('d-m-Y');?>
			</div>
		</div>
		<div>
			<div class="span2 text-right">
				<?php echo JText::_('LBL_PAYMENT_DATE'); ?>:
			</div>
			<div class="span4">
				<?php echo $datePayment->format('d-m-Y'); ?>
			</div>
		</div>
	</div>	
</div>
<h3><?php echo JText::_('LBL_DESCRIP_PRODUCTOS'); ?></h3>
<table class="table table-bordered">
	<thead>
		<tr>
			<th class="span1">#</th>
			<th class="span2"><?php echo JText::_('LBL_CANTIDAD'); ?></th>
			<th class="span4"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION'); ?></th>
			<th class="span1"><?php echo JText::_('LBL_UNIDAD'); ?></th>
			<th class="span2"><?php echo JText::_('LBL_P_UNITARIO'); ?></th>
			<th class="span2"><?php echo JText::_('LBL_IMPORTE'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($conceptos as $key => $value) {
		?>
				<tr>
					<td><?php echo $key+1; ?></td>
					<td style="text-align: center;"><?php echo number_format($value['CANTIDAD']); ?></td>
					<td><?php echo $value['DESCRIPCION']; ?></td>
					<td><?php echo $value['UNIDAD']; ?></td>
					<td>
						<div class="text-right">
							$<?php echo number_format($value['VALORUNITARIO'], 2); ?>
						</div>
					</td>
					<td>
						<div class="text-right">
							$<?php echo number_format($value['IMPORTE'], 2); ?>
						</div>
					</td>
				</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="4" rowspan="3">
				<?php echo JText::_('LBL_MONTO_LETRAS'); ?>
			</td>
			<td class="span2">
				<?php echo JText::_('LBL_SUBTOTAL'); ?>
			</td>
			<td>
				<div class="text-right">$<?php echo number_format($comprobante['SUBTOTAL'], 2); ?></div>
			</td>
		</tr>
		<tr>
			<td class="span2">
				<?php echo number_format($impuestos->iva->tasa).'%'.JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?>
			</td>
			<td>
				<div class="text-right">$<?php echo number_format($impuestos->iva->importe, 2); ?></div>
			</td>
		</tr>
		<tr>
			<td class="span2">
				<?php echo JText::_('LBL_TOTAL'); ?>
			</td>
			<td>
				<div class="text-right">$<?php echo number_format($comprobante['TOTAL'], 2); ?></div>
			</td>
		</tr>
	</tbody>
</table>
<div class="control-group" id="tabla-bottom">
	<div>
		<?php echo JText::_('LBL_OBSERVACIONES'); ?>:
	</div>
	<div>
		<?php echo $this->datos['observaciones']; ?>
	</div>
</div>
<?php
}
?>