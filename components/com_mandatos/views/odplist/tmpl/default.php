<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$ordenes = $this->ordenes;

if(is_null($ordenes) || empty($ordenes)){
    JFactory::getApplication()->enqueueMessage(JText::_('MSG_NO_ORDERS'), 'Message');
}
?>
<h1><?php echo JText::_('TITULO_LISTADO_ODP').$ordenes[0]->idMutuo; ?></h1>

<div>
    <table class="table table-bordered">
        <thead>
        <tr class="row">
            <th class="header" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('ODPLIST_LBL_NUM'); ?></span> </th>
            <th class="header" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('ODPLIST_LBL_DEPOSITO'); ?></span> </th>
            <th class="header" style="text-align: center; vertical-align: middle;" ><span class="etiqueta"><?php echo JText::_('ODPLIST_LBL_CAPITAL'); ?></span> </th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if( !is_null($ordenes) ){
            foreach($ordenes as $key => $value){
                $btnOrden = '<a class="btn btn-primary" href="index.php?option=com_mandatos&view=odppreview&id='.$value->id.'">'.JText::_('ODPS_LBL_IR_ORDEN').'</a> ';
                ?>
                <tr class="row">
                    <td style="text-align: center; vertical-align: middle;"><?php echo $value->numOrden; ?></td>
                    <td style="text-align: center; vertical-align: middle;"><?php echo $value->fecha_deposito; ?></td>
                    <td style="text-align: right; vertical-align: middle;">$<?php echo number_format($value->capital,2); ?></td>
                    <td style="text-align: center; vertical-align: middle;"><?php echo $btnOrden; ?></td>
                </tr>
            <?php
            }
        }
        ?>
        </tbody>
    </table>
</div>

<a class="btn btn-primary" href="index.php?option=com_mandatos&view=mutuoslist">Listado de Mutuos</a>