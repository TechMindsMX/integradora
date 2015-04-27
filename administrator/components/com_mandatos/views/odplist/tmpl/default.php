<?php
use Integralib\OrdenFn;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$ordenes = $this->ordenes;
$mutuo = $this->mutuo;

if(is_null($ordenes) || empty($ordenes)){
    JFactory::getApplication()->enqueueMessage(JText::_('MSG_NO_ORDERS'), 'Message');
}
?>
<h1><?php echo JText::_('TITULO_LISTADO_ODP').$ordenes[0]->idMutuo; ?></h1>

<div class="clearfix">&nbsp;</div>

<div class="span6"><strong>Acreedor:</strong> <?php echo $mutuo->integradoAcredor->nombre; ?></div>
<div class="span6"><strong>Deudor:</strong> <?php echo $mutuo->integradoDeudor->nombre; ?></div>

<div class="clearfix">&nbsp;</div>

<div class="span3"><strong>Monto:</strong> $<?php echo number_format($mutuo->totalAmount, 2); ?></div>
<div class="span3"><strong>Interes:</strong> $<?php echo number_format($mutuo->totalInteres, 2); ?></div>
<div class="span3"><strong>Tasa Interes:</strong> <?php echo $mutuo->interes; ?>%</div>
<div class="span3"><strong>IVA:</strong> $<?php echo number_format($mutuo->totalIva, 2); ?></div>
<div class="span3"><strong>Monto Total del prestamo:</strong> $<?php echo number_format($mutuo->realTotalAmount, 2); ?></div>
<div class="span3"><strong>Saldo:</strong> <?php echo number_format($mutuo->saldo,2); ?></div>

<div class="clearfix">&nbsp;</div>

<div style="margin-top: 20px;">
    <table class="table table-bordered">
        <thead>
        <tr class="row">
            <th class="" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('ODPLIST_LBL_NUM'); ?></span> </th>
            <th class="" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('ODPLIST_LBL_DEPOSITO'); ?></span> </th>
            <th class="" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('ODPLIST_LBL_CAPITAL'); ?></span> </th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if( !is_null($ordenes) ){
            foreach($ordenes as $key => $value){
                if( $value->status == OrdenFn::getStatusIdByName('pagada') ) {
                    $classStatus = 'color:#FF0000;';
                    $btnOrden = JText::_('LBL_PAID');
                }else{
                    $classStatus = '';
                    $btnOrden = '<a class="btn btn-primary" href="index.php?option=com_mandatos&view=odppreview&id=' . $value->id . '">' . JText::_('ODPS_LBL_IR_ORDEN') . '</a> ';
                }
                ?>
                <tr class="row">
                    <td style="text-align: center; vertical-align: middle; <?php echo $classStatus; ?>"><?php echo $value->numOrden; ?></td>
                    <td style="text-align: center; vertical-align: middle; <?php echo $classStatus; ?>"><?php echo date('d-m-Y',$value->fecha_deposito); ?></td>
                    <td style="text-align: right; vertical-align: middle; <?php echo $classStatus; ?>">$<?php echo number_format($value->capital,2); ?></td>
                    <td style="text-align: center; vertical-align: middle; <?php echo $classStatus; ?>"><?php echo $btnOrden; ?></td>
                </tr>
            <?php
            }
        }
        ?>
        </tbody>
    </table>
</div>

<a class="btn btn-primary" href="index.php?option=com_mandatos&view=mutuoslist">Listado de Mutuos</a>