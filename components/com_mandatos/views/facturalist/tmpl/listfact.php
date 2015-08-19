<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$document	= JFactory::getDocument();
$facturas	= $this->dataAllFacturas;
$type		= array('Cliente', 'Proveedor');
$status		= array('Activo', 'Inactivo');

$document->addScript('libraries/integradora/js/jquery.number.min.js');
$document->addScript('libraries/integradora/js/jquery.metadata.js');
$document->addScript('libraries/integradora/js/jquery.tablesorter.min.js');

?>
<script>
    jQuery(document).ready(function(){
        jQuery("span.number").number( true, 2 );

        jQuery('#benef').on('change', filtro);
        jQuery('#clearFiltro').on('click', filtro);

        jQuery('.status1 input:button').prop('disabled', true);

        jQuery("#myTable").tablesorter({
            sortList: [[0,0]],
            headers: {
                0:{ sorter: false },
                2:{ sorter: false},
                3:{ sorter: false },
                4:{ sorter: false }
            }
        });
    });

    function filtro(){
        var campo   = jQuery(this);
        var valor	= campo.prop('id')=='benef'?campo.val():'0';
        var tr      = jQuery('.client_'+valor);
        var table   = jQuery('[class*="client_"]');

        if(valor != 0){
            table.hide();
            tr.show();
        }else{
            jQuery('#benef').val(0);
            table.show();
        }

    }
</script>
<h1><?php echo JText::_('COM_MANDATOS_FACTURAS'); ?></h1>

<div>
    <div class="radio">
        <label for="benef"><?php echo JText::_('COM_MANDATOS_ORDENES_FILTRO'); ?>:</label>
        <select name="beneficiarios" id="benef">
            <option value="0" selected="selected"><?php echo JText::_('LBL_SELECCIONE_OPCION'); ?></option>
            <?php
            foreach ($this->clientes as $value) {
                ?>
                <option value="<?php echo $value->clientId;?>"><?php echo $value->clientName; ?></option>
            <?php
            }
            ?>
        </select>
        <input type="button" class="btn btn-primary" id="clearFiltro" value="<?php echo JText::_('LBL_LIMPIAR'); ?>">
    </div>
</div>

<div class="table-responsive">
    <table id="myTable" class="table table-bordered tablesorter">
        <thead>
        <tr>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ORDENES_NUM_FACT'); ?></span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ORDENES_FECHA_ORDEN'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('LBL_BENEFICIARIO'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ORDENES_MONTO'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('LBL_ORDER_STATUS'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" >&nbsp;</th>
            <th style="text-align: center; vertical-align: middle;" >&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if( !is_null($facturas) ){
            foreach ($facturas as $key => $value) {
                $url_preview = JRoute::_('index.php?option=com_mandatos&view=facturapreview&facturanum='.$value->id);
                $preview_button = '<a href="'.$url_preview.'"><i class="icon-search"></i></a>';
                $fileName = explode('/',$value->urlXML);

                echo '<tr class="client_'.$value->clientId.'">';
                echo '	<td style="text-align: center; vertical-align: middle;" class="margen-fila" >'.$preview_button.$value->id.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="" >'.$value->createdDate.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="" >'.$value->proveedor->frontName.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="" >$'.number_format($value->totalAmount,2).'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="" >'.$value->status->name.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="" ><a download="'.$fileName[2].'" href="'.$value->urlXML.'">Descargar XML</a></td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="" ><a href="index.php?option=com_mandatos&view=facturapreview&layout=pdfview&tmpl=component&facturanum='.$value->numOrden.'" target="_blank">Ver PDF</a></td>';
                echo '</tr>';
            }
        }else{
            JFactory::getApplication()->enqueueMessage(JText::_('MSG_NO_PRODUCTS'));
        }
        ?>
        </tbody>
    </table>
</div>

<div style="margin-top: 20px;">
    <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos'); ?>">
        <?php echo JText::_('COM_MANDATOS_TITULO'); ?>
    </a>
</div>