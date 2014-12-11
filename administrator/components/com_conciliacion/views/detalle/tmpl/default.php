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


<?php
class Tabla {
	public $id;
	public $valor;
}

$params = new Tabla;
$params->id = 'link';
$params->valor = null;

function pintaTabla($tabla, $params) {
	
	$html = array();
	
	foreach ( $tabla as $row ) {
		$html[] = '<div class="row1 clearfix">';
		foreach ($row as $value) {
			$span = 100/count($value);
			$html[] = '<div class="" style="width: '.$span.'%;">';
			$html[] = $value;
			$html[] = '</div>';
		}
		$html[] = '</div>';
	}
}

foreach ( $this->odv as $odv ) {
	$html[] = '<div class="row1 clearfix">';
	$html[] = '<div class="span1">'.$odv->id.'</div>';
	$html[] = '<div class="span1">'.$odv->statusName.'</div>';
	$html[] = '<div class="span2">'.$odv->integradoName.'</div>';
	$html[] = '<div class="span2">'.$odv->proveedor->corporateName.'</div>';
	$html[] = '<div class="span2">'.$odv->createdDate.'</div>';
	$html[] = '<div class="span2">'.$odv->paymentDate.'</div>';
	$html[] = '<div class="span2">'.$odv->totalAmount.'</div>';
	$html[] = '</div>';
}
echo implode('',$html);



var_dump($this->odv);
