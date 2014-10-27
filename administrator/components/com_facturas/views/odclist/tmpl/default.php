<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');

JHtml::_('behavior.calendar');
$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');

$vName = 'listadoODC';

JSubMenuHelper::addEntry(
    JText::_('COM_FACTURAS_FACTURAS'),
    'index.php?option=com_facturas',
    $vName == 'facturas');
JSubMenuHelper::addEntry(
    JText::_('COM_FACTURAS_LISTADO_ODD'),
    'index.php?option=com_facturas&view=oddlist',
    $vName == 'listadoODD');
JSubMenuHelper::addEntry(
    JText::_('COM_FACTURAS_LISTADO_ODC'),
    'index.php?option=com_facturas&view=odclist',
    $vName == 'listadoODC');
JSubMenuHelper::addEntry(
    JText::_('COM_FACTURAS_LISTADO_ODR'),
    'index.php?option=com_facturas&view=odrlist',
    $vName == 'listadoODR');

$odcs = $this->ordenes;
?>
<link rel="stylesheet" href="templates/isis/css/override.css" type="text/css">
<script language="javascript" type="text/javascript">
    function filtrointegrado() {
        $idIntegrado = jQuery(this).val();

        if($idIntegrado != 0){
            jQuery('[class*="integrado_"]').hide();
            jQuery('.integrado_'+$idIntegrado).show();
        }else{
            jQuery('[class*="integrado_"]').show();
        }
    }
    function filtro_fechas(){
        var fechaInicio = new Date(Date.parse(jQuery('#fechaInicio').val()));
        var fechafin    = new Date(Date.parse(jQuery('#fechaFin').val()));

        fechaInicioTS = fechafin.getTime()/1000;
        fechaFinTS = fechaInicio.getTime()/1000;

        var filas = jQuery('.row1');
        jQuery.each(filas, function(key, value){
            var fila = jQuery(value);
            var campo = fila.find('input[id*="fecha"]');
            if( (fechaInicioTS >= campo.val()) && (fechaFinTS <= campo.val()) ){
                fila.show();
            }else{
                fila.hide();
            }
        });
    }

    function limpiaFiltro() {
        jQuery('.row1').show();
    }
    jQuery(document).ready(function(){
        jQuery('#integrado').on('change',filtrointegrado);
        jQuery('#filtrofecha').on('click', filtro_fechas);
        jQuery('#llenatabla').on('click', limpiaFiltro);
    });
</script>
<form action="" method="post" name="adminForm" id="adminForm">
    <div  class="integrado-id" id="odv">
        <div class="head2" id="head" >
            <div class="filtros" id="columna1" >
                <label for="integrado">Seleciona el Integrado:</label>
                <select id='integrado' name="integrado" class="integrado">
                    <option value="0" selected="selected">Seleccione el filtro</option>
                    <?php
                    foreach ($this->usuarios as $key => $value) {
                        echo '<option value="'.$value->integrado_id.'">'.$value->name.'</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="filtros">
                <div class="columna1">
                    <label for="fechaFin">Fecha Inicio</label>
                    <?php
                    $d = new DateTime();
                    $d->modify('first day of this month');
                    $default = $d->format('Y-m-d');
                    echo JHTML::_('calendar',$default,'fechaInicio', 'fechaInicio', $format = '%Y-%m-%d', $attsCal);
                    ?>
                </div>
                <div class="columna1">
                    <label for="fechaFin">Fecha Fin</label>
                    <?php
                    $default = date('Y-m-d');
                    echo JHTML::_('calendar',$default,'fechaFin', 'fechaFin', $format = '%Y-%m-%d', $attsCal);
                    ?>
                </div>
                <div>
                    <input type="button" class="btn btn-primary" value="Buscar" id="filtrofecha">
                    <input type="button" class="btn btn-primary" value="Limpiar" id="llenatabla">
                </div>
            </div>
        </div>
    </div>
    <div id="table_content">
        <table class="adminlist table" id="table_list" cellspacing="0" cellpadding="0" id="odv">
            <thead class="thead">
            <tr>
                <th><?php echo JText::_('COM_MANDATOS_ORDENES_NUM_ORDEN'); ?></th>
                <th><?php echo JText::_('COM_MANDATOS_ORDENES_FECHA_ORDEN'); ?></th>
                <th><?php echo JText::_('COM_MANDATOS_ODD_INTEGRADO'); ?></th>
                <th><?php echo JText::_('COM_MANDATOS_ORDENES_MONTO_ORDEN'); ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody class="tbody">
            <?php
            foreach ($odcs as $key => $value) {
            ?>
                <tr class="row1 integrado_<?php echo $value->integradoId; ?>">
                    <td><?php echo $value->folio; ?></td>
                    <td><?php echo $value->created; ?><input type="hidden" id="fecha" value="<?php echo strtotime($value->created); ?>" /> </td>
                    <td><?php echo $value->integradoName; ?></td>
                    <td>$<?php echo number_format($value->totalAmount,2); ?></td>
                    <td><a href="index.php?option=com_facturas&view=odcform&odcNum=<?php echo $value->folio ?>" class="btn btn-primary">Conciliar</a> </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</form>