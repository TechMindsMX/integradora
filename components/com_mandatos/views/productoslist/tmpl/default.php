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
$productos = $this->data;
?>
<script>
    jQuery(document).ready(function(){
        jQuery('.btn').on('click',editarProd);
        jQuery('.status1 input:button').prop('disabled', true);
        jQuery("#myTable").tablesorter({
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
    });

    function editarProd(){
        var clickedBtn	= jQuery(this).prop('id');
        var productId	= clickedBtn.split('_')[1];

        window.location = 'index.php?option=com_mandatos&task=editarproducto&id_producto='+productId;
    }
</script>

<h1><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_TITULO'); ?></h1>

<div class="agregarProducto">
    <?php
    $ruta = JRoute::_('index.php?option=com_mandatos&view=productosform');
    echo '<a class="btn btn-primary" href="'.$ruta.'">'.JText::_('COM_MANDATOS_PRODUCTOS_LBL_AGREGAR').'</a>';
    ?>
</div>

<div class="table-responsive">
    <table id="myTable" class="table table-bordered tablesorter">
        <thead>
        <tr>
            <th class="header" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_NAME'); ?></span> </th>
            <th class="header" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_MEDIDAS'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_PRECIO'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_IEPS'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_MONEDA'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ></th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_DESHABILITA_PROYECTO'); ?></span></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if( !is_null($productos) ){
            foreach ($productos as $key => $value) {
                $selected = $value->status == 0?'':'checked';
                $class = $value->status == 0?'':'status1';

                echo '<tr>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$value->productName.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$value->description.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$value->measure.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >$'.number_format($value->price,2).'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$this->catalogo[$value->iva]->leyenda.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$value->ieps.'%</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$value->currency.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" ><input type="button" class="btn btn-primary" id="editar_'.$value->id_producto.'" value="'.JText::_('COM_MANDATOS_PROYECTOS_LISTADO_EDITAR_PROYECTO').'" /></td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" ><input type="checkbox" id=baja_"'.$value->id_producto.'" name="baja_'.$value->id_producto.'" '.$selected.' /></td>';
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