<?php
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
JHTML::_('behavior.calendar');

$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19', 'disabled'=>'1');

?>

<form id="form_admin_odd" class="form">
    <div class="form-group">
        <label for="ordenPagada">
            <input type="checkbox" id="ordenPagada" name="ordenPagada" value="1" >
            Orden Pagada
        </label>
    </div>
    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <label for="banco_cuenta">Cuenta y Banco</label>
        <select name="banco_cuenta" id="banco_cuenta">
            <option value="0">Seleccione su opción</option>
        </select>
    </div>
    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <label for="reference">Referencia</label>
        <input type="text" maxlength="21" name="reference" id="reference">
    </div>
    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <label for="paymentDate">Fecha de pago</label>
        <?php
        $default = date('Y-m-d');
        echo JHTML::_('calendar',$default,'paymentDate', 'paymentDay', $format = '%Y-%m-%d', $attsCal);
        ?>
    </div>
    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <label for="reference">Monto</label>
        <input type="text" name="reference" id="reference">
    </div>
    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <input type="button" class="btn btn-primary" value="Enviar" />
    </div>
</form>