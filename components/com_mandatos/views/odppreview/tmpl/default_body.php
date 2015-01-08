<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$orden = $this->orden;
?>

<div id="odr_preview">
    <div class="clearfix" id="logo">
        <div class="span5"><img width="200" src="<?php echo JUri::base().'images/logo_iecce.png'; ?>" /></div>
        <div class="span5">
            <div class="row"><h3 class="span2 text-right" style="width: 40%;">No. Orden</h3><h3 class="span2 bordes-box text-center"><?php echo $orden->numOrden; ?></h3></div>
            <div class="row"><h3 class="span2 text-right" style="width: 40%;">No. Mutuo</h3><h3 class="span2 bordes-box text-center"><?php echo $orden->idMutuo; ?></h3></div>
        </div>
    </div>

    <h1 style="margin-bottom: 40px;"><?php echo strtoupper(JText::_('LBL_ORDEN_DE_PRESTAMO')); ?></h1>

    <div>
        <div class="span6">Fecha de Elaboración: <strong><?php echo date('d-m-Y', $orden->fecha_elaboracion) ?></strong></div>
        <div class="span6">Fecha de depósito: <strong><?php echo $orden->fecha_deposito; ?></strong></div>
    </div>
    <div class="clearfix">&nbsp;</div>
    <div>
        <div class="span6">Tasa: <strong><?php echo $orden->tasa ?></strong></div>
        <div class="span6">Tipo Movimiento: <strong><?php echo $orden->tipo_movimiento; ?></strong></div>
    </div>
    <div class="clearfix">&nbsp;</div>
    <div>
        <div class="span6">Acreedor: <strong><?php echo $orden->acreedor ?></strong></div>
        <div class="span6">Capital: <strong>-$<?php echo number_format($orden->capital,2); ?></strong></div>
    </div>
    <div class="clearfix">&nbsp;</div>
    <div>
        <div class="span6">RFC: <strong><?php echo $orden->a_rfc; ?></strong></div>
        <div class="span6">Intereses: <strong>-</strong></div>
        <div class="clearfix"></div>
        <div class="span7" style="text-align: right;">IVA: <strong>$<?php echo number_format($orden->iva_intereses,2); ?></strong></div>
    </div>
    <div class="clearfix">&nbsp;</div>
    <div>
        <div class="span6">Deudor: <strong><?php echo $orden->deudor; ?></strong></div>
        <div class="span6">Capital: <strong><?php echo number_format($orden->capital,2); ?></strong></div>
    </div>
    <div class="clearfix">&nbsp;</div>
    <div>
        <div class="span6">RFC: <strong><?php echo $orden->d_rfc; ?></strong></div>
        <div class="span6">interese: <strong>-</strong></div>
        <div class="clearfix"></div>
        <div class="span7" style="text-align: right;">IVA: <strong>$<?php echo number_format($orden->iva_intereses,2); ?></strong></div>
    </div>
    <div class="clearfix">&nbsp;</div>

    <div></div>

</div>
