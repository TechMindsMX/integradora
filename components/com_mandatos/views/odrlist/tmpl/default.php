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

if(is_null($ordenes) || empty($ordenes)){
    JFactory::getApplication()->enqueueMessage(JText::_('MSG_NO_ORDERS'), 'Message');
}
?>
<script>
    jQuery(document).ready(function(){
        jQuery("span.number").number( true, 2 );

        jQuery('.filtro').on('click', filtro);

        jQuery('.status1 input:button').prop('disabled', true);

        jQuery("#myTable").tablesorter({
            sortList: [[0,0]],
            headers: {
                1:{ sorter: false },
                2:{ sorter: false },
                3:{ sorter: false },
                4:{ sorter: false }
            }
        });
    });

    function filtro(){
        var valor	= parseInt( jQuery(this).val() );
        var type0   = jQuery('.type_0');
        var type1   = jQuery('.type_1');

        switch(valor){
            case 0:
                type0.show();
                type1.hide();
                break;
            case 1:
                type1.show();
                type0.hide();
                break;
            case 3:
                type0.show();
                type1.show();
                break;
        }
    }
</script>
<h1><?php echo JText::_('COM_MANDATOS_ORDENES_RETIRO_LBL_TITULO'); ?></h1>

<div>
    <div class="col-md-4">
        <?php $newOdcUrl = jRoute::_('index.php?option=com_mandatos&view=odcform&integradoId='.$this->integradoId); ?>
        <a class="btn btn-primary" href="<?php echo $newOdcUrl; ?>" /><?php echo JText::_('COM_MANDATOS_ORDENES_LBL_AGREGAR'); ?></a>
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
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('LBL_PROVEEDOR'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ORDENES_MONTO'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ORDENES_ACEPTAR_ORDEN'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" >&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if( !is_null($ordenes) ){
            foreach ($ordenes as $key => $value) {
                $url_preview = JRoute::_('index.php?option=com_mandatos&view=odcpreview&integradoId='.$this->integradoId.'&odcnum='.$value->id);
                $preview_button = '<a href="'.$url_preview.'"><i class="icon-search"></i></a>';

                if ($value->status == 0 && $this->permisos['canAuth']){
                    $url_auth = JRoute::_('index.php?option=com_mandatos&view=odcpreview&integradoId='.$this->integradoId.'&odcnum='.$value->id);
                    $auth_button = '<a class="btn btn-primary" id=baja_"'.$value->id.'" name="baja" href="'.$url_auth.'">'.JText::_("LBL_AUTORIZE") .'</a>';
                    $edit_button = '<a class="btn btn-primary" href="#">'.JText::_('COM_MANDATOS_PROYECTOS_LISTADO_EDITAR_PROYECTO').'</a>';
                } elseif ($value->status == 0 && !$this->permisos['canAuth'] && $this->permisos['canEdit']){
                    $auth_button = JText::_("LBL_CANT_AUTHORIZE") ;
                    $edit_button = '<a class="btn btn-primary" href="#">'.JText::_('COM_MANDATOS_PROYECTOS_LISTADO_EDITAR_PROYECTO').'</a>';
                } elseif ($value->status == 1) {
                    $auth_button = JText::_('LBL_AUTHORIZED');
                    $edit_button = JText::_('LBL_NOT_EDITABLE');
                } else {
                    $auth_button = JText::_("LBL_CANT_AUTHORIZE") ;
                    $edit_button = JText::_('LBL_NOT_EDITABLE');
                }
                $class = $value->status == 0?'':'status1';

                echo '<tr class="type_'.$value->status.'">';
                echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$preview_button.$value->folio.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="rfc '.$class.'" >'.$value->created.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="rfc '.$class.'" >'.$value->proveedor->tradeName.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >$'.number_format($value->amount,2).'</td>';
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
    <a class="btn btn-primary" href="<?php echo JRoute::_('index.php?option=com_mandatos&integradoId='.$this->integradoId); ?>" />
    <?php echo JText::_('COM_MANDATOS_TITULO'); ?>
    </a>
</div>