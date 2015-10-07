<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');

JHtml::_('behavior.calendar');
$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');
?>
<link rel="stylesheet" href="templates/isis/css/override.css" type="text/css">
<script language="javascript" type="text/javascript">

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
        jQuery('#filtrofecha').on('click', filtro_fechas);
        jQuery('#llenatabla').on('click', limpiaFiltro);
    });
</script>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <form action="" method="post" name="adminForm" id="adminForm" autocomplete="off">
        <div  class="integrado-id" id="odv">
            <div class="head2" id="head" >

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
                    <th><?php echo JText::_('COM_MANDATOS_ORDENES_FECHA_ORDEN'); ?></th>
                    <th><?php echo JText::_('COM_MANDATOS_ORDENES_MONTO_ORDEN'); ?></th>
                    <th>Conciliación</th>
                </tr>
                </thead>
                <tbody class="tbody">
                <?php
                foreach ($this->txs as $key => $value) {
                    ?>
                    <tr class="row1">
                        <td> <?php echo date('d-m-Y', $value->date); ?> </td>
                        <td> $<?php echo number_format($value->amount,2); ?> </td>
                        <td><a href="index.php?option=com_adminintegradora&view=txsform&idtx=<?php echo $value->id ?>" class="btn btn-primary"><?php echo JText::_('COM_TXS_CONCILIACION')?></a> </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </form>
</div>