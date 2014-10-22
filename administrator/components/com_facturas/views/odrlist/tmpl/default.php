<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$vName = 'listadoODR';

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
            <div id="columna1" ><span>Seleciona el Integrado:</span>

                <select id='integrado' name="integrado" class="integrado">
                    <option value="0"></option>
                    <?php
                    foreach ($this->usuarios as $key => $value) {
                        echo '<option value="'.$value->integrado_id.'">'.$value->name.'</option>';
                    }
                    ?>

                </select>
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
                <tr class="integrado_<?php echo $value->integradoId; ?>">
                    <td><?php echo $value->id; ?></td>
                    <td><?php echo $value->created; ?></td>
                    <td><?php echo $value->integradoName; ?></td>
                    <td>$<?php echo number_format($value->totalAmount,2); ?></td>
                    <td><input type="button" class="btn btn-primary" value="Conciliar"> </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</form>