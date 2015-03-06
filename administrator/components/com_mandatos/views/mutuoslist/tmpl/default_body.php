<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$document        = JFactory::getDocument();
$datosAcreedor   = $this->mutuosAcreedor;
$datosDeudor     = $this->mutuosDeudor;

$document->addScript('libraries/integradora/js/jquery.tablesorter.min.js');
?>
<script>
    jQuery(document).ready(function(){
        jQuery("#tabla").tablesorter({
            sortList: [[0,0]],
            headers: {
                0:{ sorter: false },
                1:{ sorter: false },
                2:{ sorter: false },
                3:{ sorter: false },
                5:{ sorter: false },
                6:{ sorter: false },
                7:{ sorter: false }
            }
        });
    });
</script>
<div>
    <h1>Listado de Mutuos</h1>
</div>
<div class="clearfix">
    <a class="btn btn-primary" href="index.php?option=com_mandatos&view=mutuosform">Crear Mutuo</a>
</div>

<h3>Mutuos como Acreedor</h3>
<table id="tabla" class="table table-bordered tablesorter" style="width: 100%; text-align: center;">
    <thead>
    <tr class="row">
        <th>Deudor</th>
        <th>Monto <span style="font-size:9px;">(Capital + Interes + IVA)</span></th>
        <th>Saldo</th>
        <th>Tipo de pago</th>
        <th>Cantidad de Pagos</th>
        <th>Duración</th>
        <th>Estatus</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($datosAcreedor as $value) {
        $url_preview = 'index.php?option=com_mandatos&view=mutuospreview&idMutuo='.$value->id;
        $preview_button = '<a href="'.$url_preview.'"><i class="icon-search"></i></a>';
        $authorizeURL = JRoute::_('index.php?option=com_mandatos&view=mutuospreview&layout=confirmauth&idMutuo=' . $value->id);
        $url = 'index.php?option=com_mandatos&view=mutuosform&idMutuo='.$value->id;

        if($value->status->id == 1){
            $style  = '';
            $edit   = '<a class="btn btn-primary" href="index.php?option=com_mandatos&view=mutuosform&id='.$value->id.'">Editar</a>';
            if ($this->permisos['canAuth']){
                $odp = '<td><a class="btn btn-primary" href="'.$authorizeURL.'">'.JText::_('LBL_AUTORIZE').'</a></td>';
            }else{
                $odp = '';
            }
        }elseif($value->status->id == 3){
            $style  = 'style="color: #FFBB00;"';
            $edit   = '<a class="btn btn-primary disabled">Editar</a>';
            if ($this->permisos['canAuth'] && $value->integradoHasAuth){
                $odp = '<td><a class="btn btn-primary disabled">'.JText::_('LBL_AUTORIZE').'</a></td>';
            }else{
                $odp = '<td><a class="btn btn-primary" href="'.$authorizeURL.'">'.JText::_('LBL_AUTORIZE').'</a></td>';
            }
        }elseif($value->status->id == 5){
            $style  = 'style="color: #FF0000;"';
            $edit   = '<a class="btn btn-primary disabled">Editar</a>';
            $odp    = '<td><a class="btn btn-primary" href="index.php?option=com_mandatos&view=odplist&id='.$value->id.'">'.JText::_('LBL_VER_ODPS').'</a></td>';
        }
        ?>
        <tr class="row" <?php echo $style;?> >
            <td><?php echo $preview_button.' '.$value->integradoDeudor->nombre; ?></td>
            <td>$<?php echo number_format($value->totalCapital+$value->totalIva+$value->totalInteres,2); ?></td>
            <td>$<?php echo number_format($value->saldo,2); ?></td>
            <td><?php echo $value->tipoPeriodo; ?></td>
            <td><?php echo $value->quantityPayments; ?></td>
            <td><?php echo round($value->duracion,2); ?> años</td>
            <td><?php echo $value->status->name; ?></td>
            <td><?php echo $edit; ?></td>
            <?php echo $odp; ?>
        </tr>
    <?php }?>
    </tbody>
</table>
<div class="clearfix">&nbsp;</div>

<h3>Mutuos como Deudor</h3>
<table id="deudor" class="table table-bordered" style="width: 100%; text-align: center;">
    <thead>
    <tr class="row">
        <th>Acreedor</th>
        <th>Monto <span style="font-size:9px;">(Capital + Interes + IVA)</span></th>
        <th>Saldo</th>
        <th>Tipo de pago</th>
        <th>Cantidad de Pagos</th>
        <th>Duración</th>
        <th>Estatus</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($datosDeudor as $value) {
        $url_preview    = 'index.php?option=com_mandatos&view=mutuospreview&idMutuo='.$value->id;
        $preview_button = '<a href="'.$url_preview.'"><i class="icon-search"></i></a>';
        $url            = 'index.php?option=com_mandatos&view=mutuosform&idMutuo='.$value->id;
        $odp            = '';
        $style          = '';
        $authorizeURL = JRoute::_('index.php?option=com_mandatos&view=mutuospreview&layout=confirmauth&idMutuo=' . $value->id);

        if($value->status->id == 1){
            $style  = '';
            if ($this->permisos['canAuth']){
                $odp = '<td><a class="btn btn-primary" href="'.$authorizeURL.'">'.JText::_('LBL_AUTORIZE').'</a></td>';
            }else{
                $odp = '';
            }
        }elseif($value->status->id == 3){
            $style  = 'style="color: #FFBB00;"';
            if ($this->permisos['canAuth'] && $value->integradoHasAuth){
                $odp = '<td><a class="btn btn-primary disabled">'.JText::_('LBL_AUTORIZE').'</a></td>';
            }else{
                $odp = '<td><a class="btn btn-primary" href="'.$authorizeURL.'">'.JText::_('LBL_AUTORIZE').'</a></td>';
            }
        }elseif($value->status->id == 5){
            $style  = 'style="color: #FF0000;"';
            $odp    = '<td><a class="btn btn-primary" href="index.php?option=com_mandatos&view=odplist&id='.$value->id.'">'.JText::_('LBL_VER_ODPS').'</a></td>';
        }
        ?>
        <tr class="row" <?php echo $style; ?>>
            <td><?php echo $preview_button.' '.$value->integradoAcredor->nombre; ?></td>
            <td>$<?php echo number_format($value->totalCapital+$value->totalIva+$value->totalInteres,2); ?></td>
            <td>$<?php echo number_format($value->saldo,2); ?></td>
            <td><?php echo $value->tipoPeriodo; ?></td>
            <td><?php echo $value->quantityPayments; ?></td>
            <td><?php echo number_format($value->duracion,2); ?> años</td>
            <td><?php echo $value->status->name; ?></td>
            <?php echo $odp; ?>
        </tr>
    <?php }?>

    </tbody>
</table>
<div class="clearfix">&nbsp;</div>

<div>
    <a class="btn btn-primary" href="index.php?option=com_mandatos">Mandatos</a>
</div>
