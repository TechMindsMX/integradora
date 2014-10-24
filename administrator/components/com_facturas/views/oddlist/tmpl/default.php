<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$vName = 'listadoODD';

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

$odds = $this->ordenes;
?>
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
    jQuery(document).ready(function(){
        jQuery('#integrado').on('change',filtrointegrado);
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
            foreach ($odds as $key => $value) {
            ?>
                <tr class="integrado_<?php echo $value->integradoId; ?>">
                    <td><?php echo $value->numOrden; ?></td>
                    <td><?php echo $value->created; ?></td>
                    <td><?php echo $value->integradoName; ?></td>
                    <td>$<?php echo number_format($value->totalAmount,2); ?></td>
                    <td><a href="index.php?option=com_facturas&view=oddform&oddNum=<?php echo $value->numOrden; ?>" class="btn btn-primary">Conciliar</a> </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</form>