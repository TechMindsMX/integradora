<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$document = JFactory::getDocument();
$document->addScript('libraries/integradora/js/jquery.number.min.js');
$document->addScript('libraries/integradora/js/jquery.metadata.js');
$document->addScript('libraries/integradora/js/jquery.tablesorter.min.js');

$ordenes = $this->data;

if(is_null($ordenes) || empty($ordenes)){
    JFactory::getApplication()->enqueueMessage(JText::_('MSG_NO_ORDERS'), 'Message');
}
?>
<script>
    jQuery(document).ready(function(){
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
</script>
<h1><?php echo JText::_('COM_MANDATOS_ORDENES_DEPOSITO_LBL_TITULO'); ?></h1>

<div>
    <div class="col-md-4">
        <?php $newOddUrl = jRoute::_('index.php?option=com_mandatos&view=oddform&integradoId='.$this->integradoId); ?>
        <a class="btn btn-primary" href="<?php echo $newOddUrl; ?>" />
            <?php echo JText::_('COM_MANDATOS_ORDENES_DEPOSITO_LBL_AGREGAR'); ?>
        </a>
    </div>

    <div class="col-md-4">
        <div><?php echo JText::_('COM_MANDATOS_ORDENES_FILTRO'); ?>:</div>
        <div class="radio">
            <label for="filtro"><input type="radio" name="filtro" class="filtro" value="1"><?php echo JText::_('LBL_STATUS_BAJA'); ?></label>
            <label for="filtro"><input type="radio" name="filtro" class="filtro" value="0"><?php echo JText::_('LBL_STATUS_ACTIVO'); ?></label>
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
            <th style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('COM_MANDATOS_ORDENES_MONTO'); ?> </span> </th>
            <th style="text-align: center; vertical-align: middle;" >&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if( !is_null($ordenes) ){
            foreach ($ordenes as $key => $value) {
                $url_preview = JRoute::_('index.php?option=com_mandatos&view=oddpreview&integradoId='.$this->integradoId.'&oddnum='.$value->id);
                $preview_button = '<a href="'.$url_preview.'"><i class="icon-search"></i></a>';
                if ($value->status == 0 && $this->permisos['canAuth']){
                    $url_auth = JRoute::_('index.php?option=com_mandatos&view=oddpreview&integradoId='.$this->integradoId.'&oddnum='.$value->id.'&task=authorize');
                    $auth_button = '<a class="btn btn-primary" id=baja_"'.$value->id.'" name="baja" href="'.$url_auth.'">'.JText::_("LBL_ÄUTORIZE") .'</a>';
                    $edit_button = '<a class="btn btn-primary" href="#">'.JText::_('COM_MANDATOS_PROYECTOS_LISTADO_EDITAR_PROYECTO').'</a>';
                } elseif ($value->status == 0 && !$this->permisos['canAuth'] && $this->permisos['canEdit']){
                    $auth_button = JText::_("LBL_CANT_ÄUTHORIZE") ;
                    $edit_button = '<a class="btn btn-primary" href="#">'.JText::_('COM_MANDATOS_PROYECTOS_LISTADO_EDITAR_PROYECTO').'</a>';
                } else {
                    $auth_button = JText::_('LBL_AUTHORIZED');
                    $edit_button = JText::_('LBL_NOT_EDITABLE');
                }
                $class = $value->status == 0?'':'status1';

                echo '<tr class="type_'.$value->status.'">';
                echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$preview_button.$value->numOrden.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >'.$value->created.'</td>';
                echo '	<td style="text-align: center; vertical-align: middle;" class="'.$class.'" >$'.number_format($value->totalmount,2).'</td>';
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