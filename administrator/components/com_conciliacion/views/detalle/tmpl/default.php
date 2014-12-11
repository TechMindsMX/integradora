<?php
// no direct access
defined( '_JEXEC' ) or die;

?>
	<script type="text/javascript" src="<?php echo JUri::root().'libraries/integradora/js/tim-filtros.js'; ?>"></script>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			var odv = <?php echo json_encode($this->odv); ?>;
			var odc = <?php echo json_encode($this->odc); ?>;
			var odd = <?php echo json_encode($this->odd); ?>;
			var odr = <?php echo json_encode($this->odr); ?>;

			var $integ = $('#integrado').change(function() {
				filtro_fechas($integ);
			});
			$('#filtrofecha').on('click', filtro_fechas );
		});
	</script>

	<div class="filtros" id="columna1" >
		<label for="integrado">Seleciona el Integrado:</label>
		<select id='integrado' name="integrado" class="integrado">
			<option value="0" selected="selected"><?php JText::_('LBL_SELECCIONE'); ?></option>
			<?php
			foreach ($this->integrados as $key => $value) {
				echo '<option value="'.$value->integrado_id.'">'.$value->name.'</option>';
			}
			?>
		</select>
	</div>
	<div class="filtros">
		<div class="columna1">
			<label for="fechaFin"><?php JText::_('LBL_FECHA_INICIO'); ?></label>
			<?php
			$d = new DateTime();
			$d->modify('first day of this month');
			$default = $d->format('Y-m-d');
			$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');
			echo JHTML::_('calendar',$default,'fechaInicio', 'fechaInicio', $format = '%Y-%m-%d', $attsCal);
			?>
		</div>
		<div class="columna1">
			<label for="fechaFin"><?php JText::_('LBL_FECHA_FIN'); ?></label>
			<?php
			$d = new DateTime();
			$d->modify('last day of this month');
			$default = $d->format('Y-m-d');
			echo JHTML::_('calendar',$default,'fechaFin', 'fechaFin', $format = '%Y-%m-%d', $attsCal);
			?>
		</div>
		<div>
			<input type="button" class="btn btn-primary" value="Buscar" id="filtrofecha">
			<input type="button" class="btn btn-primary" value="Limpiar" id="llenatabla">
		</div>
	</div>
	<div class="tipoOrden">
		<div class="form-horizontal form-actions">
			<a class="btn btn-success"><?php echo JText::_('LBL_ODVS'); ?></a>
			<a class="btn btn-success"><?php echo JText::_('LBL_ODCS'); ?></a>
			<a class="btn btn-success"><?php echo JText::_('LBL_ODDS'); ?></a>
		</div>
	</div>


<?php
$url_asociar = 'index.php?option=com_conciliacion&view=detalle&layout=confirm&orderId=';

$html[] = '<div class=""><h3>'.JText::_('LBL_ODVS').'</h3>';
foreach ( $this->odv as $orden) {
	$btn_asociar = '<a class="btn btn-success" href="'.$url_asociar.$orden->id.'&orderType=odv">'.JText::_('LBL_ASOCIAR').'</a>';
	$html[] = '<div class="row1 clearfix">';
	$html[] = '<div class="span1">'.$orden->id.'</div>';
	$html[] = '<div class="span1">'.$orden->status->statusName.'</div>';
	$html[] = '<div class="span2">'.$orden->integradoName.'</div>';
	$html[] = '<div class="span2">'.$orden->proveedor->corporateName.'</div>';
	$html[] = '<div class="span2">'.$orden->createdDate.'<input type="hidden" id="filtro_fecha" value="'.$orden->timestamps->createdDate.'"></div>';
	$html[] = '<div class="span2">'.$orden->paymentDate.'</div>';
	$html[] = '<div class="span2">'.number_format($orden->totalAmount, 2).'</div>';
	$html[] = '<div class="span1">'.$btn_asociar.'</div>';
	$html[] = '</div>';
}
$html[] = '</div>';

$html[] = '<div class=""><h3>'.JText::_('LBL_ODCS').'</h3>';
foreach ( $this->odc as $orden) {
	$btn_asociar = '<a class="btn btn-success" href="'.$url_asociar.$orden->id.'&orderType=odc">'.JText::_('LBL_ASOCIAR').'</a>';
	$html[] = '<div class="row1 clearfix">';
	$html[] = '<div class="span1">'.$orden->id.'</div>';
	$html[] = '<div class="span1">'.$orden->status->statusName.'</div>';
	$html[] = '<div class="span2">'.$orden->integradoName.'</div>';
	$html[] = '<div class="span2">'.$orden->proveedor->corporateName.'</div>';
	$html[] = '<div class="span2">'.$orden->createdDate.'<input type="hidden" id="filtro_fecha" value="'.$orden->timestamps->createdDate.'"></div>';
	$html[] = '<div class="span2">'.$orden->paymentDate.'</div>';
	$html[] = '<div class="span2">'.number_format($orden->totalAmount, 2).'</div>';
	$html[] = '<div class="span1">'.$btn_asociar.'</div>';
	$html[] = '</div>';
}
$html[] = '</div>';

$html[] = '<div class=""><h3>'.JText::_('LBL_ODDS').'</h3>';
foreach ( $this->odd as $orden ) {
	$btn_asociar = '<a class="btn btn-success" href="'.$url_asociar.$orden->id.'&orderType=odd">'.JText::_('LBL_ASOCIAR').'</a>';
	$html[] = '<div class="row1 clearfix">';
	$html[] = '<div class="span1">'.$orden->id.'</div>';
	$html[] = '<div class="span1">'.$orden->status->statusName.'</div>';
	$html[] = '<div class="span2"></div>';
	$html[] = '<div class="span2"></div>';
	$html[] = '<div class="span2">'.$orden->createdDate.'<input type="hidden" id="filtro_fecha" value="'.$orden->timestamps->createdDate.'"></div>';
	$html[] = '<div class="span2">'.$orden->paymentDate.'</div>';
	$html[] = '<div class="span2">'.number_format($orden->totalAmount, 2).'</div>';
	$html[] = '<div class="span1">'.$btn_asociar.'</div>';
	$html[] = '</div>';
}
$html[] = '</div>';


echo implode('',$html);


//var_dump($this->odd, $this->odc, $this->odv);