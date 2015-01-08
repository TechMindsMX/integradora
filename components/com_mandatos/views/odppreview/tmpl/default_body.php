<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');
jimport('integradora.numberToWord');
$number2word = new AifLibNumber;
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

    <div>
        <div class="span6">Tasa: <strong><?php echo $orden->tasa ?></strong></div>
        <div class="span6">Tipo Movimiento: <strong><?php echo $orden->tipo_movimiento; ?></strong></div>
    </div>

    <div class="clearfix">&nbsp;</div>

    <div>
        <div class="span6">Acreedor: <strong><?php echo $orden->acreedor ?></strong></div>
        <div class="span6">Capital: <strong>-$<?php echo number_format($orden->capital,2); ?></strong></div>
    </div>

    <div>
        <div class="span6">RFC: <strong><?php echo $orden->a_rfc; ?></strong></div>
        <div class="span6">Intereses: <strong>-$<?php echo number_format($orden->intereses,2); ?></strong></div>
        <div class="clearfix"></div>
        <div class="span7" style="text-align: right;">IVA: <strong>$<?php echo number_format($orden->iva_intereses,2); ?></strong></div>
    </div>

    <div class="clearfix">&nbsp;</div>

    <div>
        <div class="span6">Deudor: <strong><?php echo $orden->deudor; ?></strong></div>
        <div class="span6">Capital: <strong>$<?php echo number_format($orden->capital,2); ?></strong></div>
    </div>

    <div>
        <div class="span6">RFC: <strong><?php echo $orden->d_rfc; ?></strong></div>
        <div class="span6">interese: <strong>$<?php echo number_format($orden->intereses,2); ?></strong></div>
        <div class="clearfix"></div>
        <div class="span7" style="text-align: right;">IVA: <strong>$<?php echo number_format($orden->iva_intereses,2); ?></strong></div>
    </div>

    <div>
        <div class="span6"><h3>Importe de la cantidad:</h3> <h4>$<?php echo number_format($orden->capital,2); ?></h4></div>
        <div class="span6"><h3>Importe en letra:</h3><h4><?php echo $number2word->toCurrency('$'.number_format($orden->capital,2)); ?></h4></div>
    </div>

    <div class="clearfix">&nbsp;</div>

    <div class="clearfix" style="margin-top: 40px;">
        AUTORIZO EXPRESAMENTE A INTEGRADORA DE EMPRENDIMIENTOS CULTURALES, S.A. DE C.V.,CONFORME A LOS ESTATUTOS, POLITICAS,
        REGLAS Y PROCEDIMIENTOS, A EFECTUAR LA OPERACIÓN DE PRÉSTAMO DESCRITA Y A NOMBRE Y CUENTA DE MI REPRESENTADA

        <table class="table-condensed table-mutuo">

            <tr>
                <td>_____________________________</td>
                <td>&nbsp;</td>
                <td>____________________________</td>
            </tr>
            <tr>
                <td>(Nombre y Firma)<br />
                    Apoderado Legal<br />
                    Acreedor</td>
                <td>&nbsp;</td>
                <td>(Nombre y Firma)<br />
                    Apoderado Legal<br />
                    Deudor</td>
            </tr>
        </table>
    </div>

</div>
