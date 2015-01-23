<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$document = JFactory::getDocument();

$document->addScript('libraries/integradora/js/jquery.metadata.js');
$document->addScript('libraries/integradora/js/jquery.tablesorter.min.js');
$document->addScript('libraries/integradora/js/multifilter.js');
$productos = $this->data;
?>
<script>
    jQuery(document).ready(function($){
        $('.status0 input:button').prop('disabled', true);

        $("#myTable").tablesorter({
            sortList: [[0,0]],
            headers: {
                2:{ sorter: false },
                3:{ sorter: false },
                4:{ sorter: false },
                5:{ sorter: false },
                6:{ sorter: false },
                7:{ sorter: false },
                8:{ sorter: false }
            }
        });

        $('#input_filtro_nombre').multifilter({
            'target' : jQuery('#myTable')
        });


        $("input[name*='baja']").click(function () {
            var $this = $(this);
            var itemId = $this.data('id');
            var valor = $this.is(':checked') ? 1 : 0;

            var request = $.ajax({
                    url: 'index.php?option=com_mandatos&task=productoslist.changeStatus&format=raw',
                    data: {id_producto: itemId,
                    status: valor},
                    type: 'POST',
                    async: false
                });

            request.done(function (result) {
                var $row = $this.parentsUntil('table', 'tr');
                $row.removeClass('status0');
                if(result.success.status == 0) {
                    $row.addClass('status0')
                }

                $('#system-message-container').remove();

                var $html = '<div id="system-message-container"><div id="system-message"><div class="alert alert-message"><a data-dismiss="alert" class="close">×</a><h4 class="alert-heading">Mensaje</h4><div>';
                $html += '<p>' + result.msg + '</p></div></div></div></div>';

                $('header').prepend($html);
            });

        });
    });

</script>

<h1><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_TITULO'); ?></h1>

<div class="agregarProducto control-group">
    <?php
    $ruta = JRoute::_('index.php?option=com_mandatos&view=productosform');
    echo '<a class="btn btn-primary" href="'.$ruta.'">'.JText::_('COM_MANDATOS_PRODUCTOS_LBL_AGREGAR').'</a>';
    ?>
</div>

<div class="form-search control-group" id="filtro_nombre">
    <label class="form-control" for="filtro_nombre"><?php echo JText::_('LBL_FILTRO_NOMBRE'); ?></label>
    <input class="form-control" name="filtro_nombre" id="input_filtro_nombre" value="" placeholder="<?php echo JText::_('LBL_FILTRO_NOMBRE'); ?>" data-col="nombre" />
</div>

<div class="table-responsive">
    <table id="myTable" class="table table-bordered tablesorter">
        <thead>
        <tr>
            <th class="header nombre" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_NAME'); ?></span> </th>
            <th class="header" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_MEDIDAS'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_PRECIO'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_MONEDA'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ></th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('LBL_PUBLISHED'); ?></span></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if( !is_null($productos) ){
            foreach ($productos as $key => $value) {
                $selected = $value->status == 0 ? '' : 'checked';
                $class = $value->status == 0 ? 'status0' : '';
                $editUrl = 'index.php?option=com_mandatos&view=productosform&id_producto='.$value->id_producto;

                echo '<tr class="'.$class.'" >';
                echo '	<td style="text-align: center; vertical-align: middle;">'.$value->productName.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;">'.$value->description.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;">'.$value->measure.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;">$'.number_format($value->price,2).'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;">'.$this->catalogo[$value->iva]->leyenda.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;">'.$value->ieps.'%</td>';
                echo '	<td style="text-align: center; vertical-align: middle;">'.$value->currency.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;"><a class="btn btn-primary" href="'.$editUrl.'">'.JText::_('COM_MANDATOS_PROYECTOS_LISTADO_EDITAR_PROYECTO').'</a></td>';
                echo '	<td style="text-align: center; vertical-align: middle;"><input type="checkbox" data-id="'.$value->id_producto.'" name="baja_'.$value->id_producto.'" '.$selected.' /></td>';
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
    <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos'); ?>" >
    <?php echo JText::_('COM_MANDATOS_TITULO'); ?>
    </a>
</div>
