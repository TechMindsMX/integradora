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

?>
<script language="javascript" type="text/javascript">
</script>
<form action="" method="post" name="adminForm" id="adminForm">
    <div  class="integrado-id" id="odv">
        <div class="head2" id="head" >
            <div id="columna1" ><span>Seleciona el Integrado:</span>

                <select id='integrado' name="integrado" onchange="llenatabla()" class="integrado">
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
                <th><?php echo JText::_('COM_MANDATOS_ORDENES_FECHA_ORDEN'); ?></th>
                <th><?php echo JText::_('COM_MANDATOS_ORDENES_FECHA_ORDEN'); ?></th>
                <th><?php echo JText::_('COM_MANDATOS_ORDENES_MONTO_ORDEN'); ?></th>
            </tr>
            </thead>
            <tbody class="tbody"></tbody>
            <tfoot>
            <tr>
                <td colspan="10">
                    <div class="pagination pagination-toolbar">
                        <input type="hidden" value="0" name="limitstart">
                    </div>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</form>