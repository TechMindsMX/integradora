<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$document	= JFactory::getDocument();
$ordenes	= $this->data;
$type		= array('Cliente', 'Proveedor');
$status		= array('Activo', 'Inactivo');

$document->addScript('libraries/integradora/js/jquery.number.min.js');
$document->addScript('libraries/integradora/js/jquery.metadata.js');
$document->addScript('libraries/integradora/js/jquery.tablesorter.min.js');
$document->addScript('libraries/integradora/js/tim-filtros.js');

if(is_null($ordenes) || empty($ordenes)){
    JFactory::getApplication()->enqueueMessage(JText::_('MSG_NO_ORDERS'), 'Message');
}
?>
<script>
    jQuery(document).ready(function(){
        jQuery("span.number").number( true, 2 );

        jQuery('.filtro').on('click', filtro_autorizadas);

        jQuery('.status1 input:button').prop('disabled', true);

        jQuery("#myTable").tablesorter({
            sortList: [[0,0]],
            headers: {
                1:{ sorter: false },
                2:{ sorter: false },
                3:{ sorter: false },
                4:{ sorter: false },
                5:{ sorter: false }
            }
        });
    });


</script>
<h1><?php echo JText::_('COM_MANDATOS_ODV_LIST'); ?></h1>

<div>
    <div class="col-md-4">
        <?php $newOdvUrl = jRoute::_('index.php?option=com_mandatos&view=odvform'); ?>
        <a class="btn btn-primary" href="<?php echo $newOdvUrl; ?>" ><?php echo JText::_('COM_MANDATOS_ORV_LBL_AGREGAR'); ?></a>
    </div>

    <div class="col-md-4">
        <div><?php echo JText::_('COM_MANDATOS_ORDENES_FILTRO'); ?>:</div>
        <div class="radio">
            <label for="filtro"><input type="radio" name="filtro" class="filtro" value="0"><?php echo JText::_('LBL_STATUS_PENDING_AUTH'); ?></label>
            <label for="filtro"><input type="radio" name="filtro" class="filtro" value="1"><?php echo JText::_('LBL_STATUS_ACTIVO'); ?></label>
            <label for="filtro"><input type="radio" name="filtro" class="filtro" value="3" id="showall" checked="checked">Todos</label>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table id="myTable" class="table table-bordered tablesorter">
        <thead>
        <tr>
            <th class="header" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ORDENES_NUM_ORDEN'); ?></span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ORDENES_FECHA_ORDEN'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('LBL_BENEFICIARIO'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ORDENES_MONTO'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ORDENES_ACEPTAR_ORDEN'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta">Edición</span> </th>
        </tr>
        </thead>
        <tbody>
        <?php
        if( !is_null($ordenes) ){
            foreach ($ordenes as $key => $value) {
                $url_preview = JRoute::_('index.php?option=com_mandatos&view=odvpreview&idOrden='.$value->id);
                $preview_button = '<a href="'.$url_preview.'"><i class="icon-search"></i></a>';

                if ($value->status->id == 1 && $this->permisos['canAuth']){
                    $url_auth = JRoute::_('index.php?option=com_mandatos&view=odvpreview&layout=confirmauth&idOrden='.$value->id);
                    $auth_button = '<a class="btn btn-primary" id=baja_"'.$value->id.'" name="baja" href="'.$url_auth.'">'.JText::_("LBL_AUTORIZE") .'</a>';
                    $edit_button = '<a class="btn btn-primary" href="index.php?option=com_mandatos&view=odvform&idOrden='.$value->id.'">'.JText::_('COM_MANDATOS_PROYECTOS_LISTADO_EDITAR_PROYECTO').'</a>';
                } elseif ($value->status->id == 1 && !$this->permisos['canAuth'] && $this->permisos['canEdit']){
                    $auth_button = JText::_("LBL_CANT_AUTHORIZE") ;
                    $edit_button = '<a class="btn btn-primary" href="index.php?option=com_mandatos&view=odvform&idOrden='.$value->id.'">'.JText::_('COM_MANDATOS_PROYECTOS_LISTADO_EDITAR_PROYECTO').'</a>';
                } elseif ($value->status->id == 5) {
                    $auth_button = JText::_('LBL_AUTHORIZED');
                    $edit_button = JText::_('LBL_NOT_EDITABLE');
                }elseif ($value->status->id == 13) {
                    $auth_button = JText::_('LBL_PAID');
                    $edit_button = JText::_('LBL_NOT_EDITABLE');
                } else {
                    $auth_button = getFromTimOne::getOrderStatusName($value->status->id)->name;
                    $edit_button = JText::_('LBL_NOT_EDITABLE');
                }
                $class = $value->status->id == 1?'':'status1';

                echo '<tr class="type_'.$value->status->id.'" data-filtro="'.$value->status->id.'">';
                echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$preview_button.' '.$value->numOrden.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="rfc '.$class.'" >'.$value->createdDate.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="rfc '.$class.'" >'.@$value->proveedor->tradeName.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >$'.number_format($value->totalAmount,2).'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$auth_button.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$edit_button.'</td>';
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