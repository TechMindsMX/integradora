<?php
// no direct access
defined( '_JEXEC' ) or die;
$integrados = $this->integrados;
$bancos = $this->bancos;
?>
<form id="conciliacionbanco">
    <div>
        <label for="cuenta"><?php echo JText::_('COM_CONCILIACIONBANCO_SELECT_BANCO'); ?></label>
        <select name="cuenta" id="cuenta">
            <option value="0"><?php echo JText::_('LBL_SELECCIONE_OPCION'); ?></option>
            <?php foreach ($integrados as $key => $value) {
                $nombreBanco = $bancos[$value->datos_bancarios->banco_codigo];
                $cuenta = substr($value->datos_bancarios->banco_cuenta, -4,4);
                ?>
                <option value="<?php echo $value->datos_bancarios->datosBan_id; ?>"><?php echo $nombreBanco.' - '.$cuenta; ?></option>
            <?php } ?>
        </select>
    </div>

    <div>
        <label><?php echo JText::_('COM_CONCILIACIONBANCO_REFERENCIA'); ?></label>
        <input type="text" name="referencia" id="referencia" maxlength="21" />
    </div>

    <div>
        <label><?php echo JText::_('COM_CONCILIACIONBANCO_FECHA'); ?></label>
        <input type="text" name="referencia" id="referencia" maxlength="21" />
    </div>

    <div>
        <label><?php echo JText::_('COM_CONCILIACIONBANCO_MONTO'); ?></label>
        <input type="text" name="referencia" id="referencia" maxlength="21" />
    </div>

    <div>
        <label for="integradoId"><?php echo JText::_('COM_CONCILIACIONBANCO_SELECT_INTEGRADO') ?></label>
        <select name="integradoId" id="integradoId">
            <option value="0"><?php echo JText::_('LBL_SELECCIONE_OPCION'); ?></option>
            <?php foreach ($integrados as $key => $value) {?>
                <option value="<?php echo $value->integrado->integrado_id; ?>"><?php echo $value->datos_personales->nom_comercial; ?></option>
            <?php } ?>
        </select>
    </div>

</form>