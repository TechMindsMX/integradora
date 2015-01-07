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

//var_dump($datos);
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

//        jQuery("#deudor").tablesorter({
//            sortList: [[0,0]],
//            headers: {
//                0:{ sorter: false },
//                1:{ sorter: false },
//                2:{ sorter: false },
//                4:{ sorter: false },
//                5:{ sorter: false }
//            }
//        });
    });
</script>
<div>
    <h1>Listado de Mutuos</h1>
</div>
<div class="clearfix">
    <a class="btn btn-primary" href="index.php?option=com_mandatos&view=mutuosform&integradoId=<?php echo $this->data->integradoId; ?>">Crear Mutuo</a>
</div>

<h3>Mutuos como Acreedor</h3>
<table id="tabla" class="table table-bordered tablesorter" style="width: 100%; text-align: center;">
    <thead>
    <tr class="row">
        <th>Deudor</th>
        <th>Monto</th>
        <th>Tipo de pago</th>
        <th>Cantidad de Pagos</th>
        <th>Duración</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($datosAcreedor as $value) {
        $url = 'index.php?option=com_mandatos&view=mutuosform&integradoId='.$this->data->integradoId.'&idMutuo='.$value->id;
        if($value->status == 1){
            $edit = '<a class="btn btn-primary disabled" href="#">Editar</a>';
            $odp = '<td><a class="btn btn-primary disabled" href="#">'.JText::_('LBL_VER_ODPS').'</a></td>';
        }else{
            $edit = '<a class="btn btn-primary" href="index.php?option=com_mandatos&view=mutuosform&integradoId='.$this->data->integradoId.'&id='.$value->id.'">Editar</a>';
            $odp = '<td><a class="btn btn-primary" href="index.php?option=com_mandatos&view=mutuosform&integradoId='.$this->data->integradoId.'&id='.$value->id.'">Generar ODP</a></td>';
        }
    ?>
        <tr class="row">
            <td><?php echo $value->integradoDeudor->nombre; ?></td>
            <td>$<?php echo number_format($value->totalAmount,2); ?></td>
            <td><?php echo $value->tipoPeriodo; ?></td>
            <td><?php echo $value->quantityPayments; ?></td>
            <td><?php echo $value->duracion; ?> años</td>
            <td><?php echo $edit; ?></td>
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
        <th>Monto</th>
        <th>Tipo de pago</th>
        <th>Cantidad de Pagos</th>
        <th>Duración</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($datosDeudor as $value) {
        $url = 'index.php?option=com_mandatos&view=mutuosform&integradoId='.$this->data->integradoId.'&idMutuo='.$value->id;
        if($value->status == 1){
            $odp = '<td><a class="btn btn-primary" href="index.php?option=com_mandatos&view=mutuosform&integradoId='.$this->data->integradoId.'&idMutuo='.$value->id.'">'.JText::_('LBL_VER_ODPS').'</a></td>';
        }else{
            $odp = '<td><a class="btn btn-primary disabled" href="#">Generar ODP</a></td>';
        }
        ?>
        <tr class="row">
            <td><?php echo $value->integradoAcredor->nombre; ?></td>
            <td>$<?php echo number_format($value->totalAmount,2); ?></td>
            <td><?php echo $value->tipoPeriodo; ?></td>
            <td><?php echo $value->quantityPayments; ?></td>
            <td><?php echo $value->duracion; ?> años</td>
        </tr>
    <?php }?>

    </tbody>
</table>
<div class="clearfix">&nbsp;</div>

<div>
    <a class="btn btn-primary" href="index.php?option=com_mandatos">Mandatos</a>
</div>
