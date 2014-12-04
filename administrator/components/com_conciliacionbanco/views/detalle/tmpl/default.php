<?php
// no direct access
defined( '_JEXEC' ) or die;


JHTML::_('behavior.calendar');

$integrados = $this->integrados;
$bancos = $this->bancos;
$data = $this->data;
$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');
var_dump($data);
?>
    <script>
        var integradosArray   = new Array();
        var integradoNon_id   = new Array();
        <?php
                foreach ($integrados as $key => $value) {
                    echo 'integradosArray['.$key.'] = "'.$value->datos_personales->nom_comercial.'";'."\n";
                    echo 'integradoNon_id["'.$value->datos_personales->nom_comercial.'"] = '.$value->integrado->integrado_id.';'."\n";
                }
            ?>
        var typeaheadSettings = {
            source: function () {
                return integradosArray;
            },
            minLength:3
        };

        function enviar() {
            var integradoName = jQuery('#integradoName').val();
            var formulario = jQuery('#conciliacionbanco');
            if(integradoName !== ''){
                jQuery('#integradoId').val(integradoNon_id[integradoName]);
            }

            formulario.submit();
        }

        function cancelar() {
            window.location = 'index.php?option=com_conciliacionbanco';
        }

        jQuery(document).ready(function(){
            jQuery('.typeahead').typeahead(typeaheadSettings);
            jQuery('.enviar').on('click', enviar);
            jQuery('.cancel').on('click', cancelar);
        });
    </script>
<?php if(is_null($data->confirmacion)) { ?>
    <form id="conciliacionbanco" action="index.php?option=com_conciliacionbanco&view=detalle&confirmacion=1" method="post">
        <input type="hidden" name="id" id="id" value="<?php $data->id; ?>" />

        <div>
            <label for="cuenta"><?php echo JText::_('COM_CONCILIACIONBANCO_SELECT_BANCO'); ?></label>
            <select name="cuenta" id="cuenta">
                <option value="0"><?php echo JText::_('LBL_SELECCIONE_OPCION'); ?></option>
                <?php foreach ($integrados as $key => $value) {
                    $nombreBanco = $bancos[$value->datos_bancarios->banco_codigo];
                    $cuenta = substr($value->datos_bancarios->banco_cuenta, -4, 4);
                    ?>
                    <option
                        value="<?php echo $value->datos_bancarios->datosBan_id; ?>"><?php echo $nombreBanco . ' - ' . $cuenta; ?></option>
                <?php } ?>
            </select>
        </div>

        <div>
            <label for="referencia"><?php echo JText::_('COM_CONCILIACIONBANCO_REFERENCIA'); ?></label>
            <input type="text" name="referencia" id="referencia" maxlength="21"/>
        </div>

        <div>
            <label><?php echo JText::_('COM_CONCILIACIONBANCO_FECHA'); ?></label>
            <?php
            $default = date('Y-m-d');
            echo JHTML::_('calendar',$default,'date', 'date', $format = '%Y-%m-%d', $attsCal);
            ?>
        </div>

        <div>
            <label for="amount"><?php echo JText::_('COM_CONCILIACIONBANCO_MONTO'); ?></label>
            <input type="text" name="amount" id="amount" maxlength="21"/>
        </div>

        <div>
            <label for="integradoName">
                <?php echo JText::_('COM_CONCILIACIONBANCO_SELECT_INTEGRADO') ?>
                <span style="font-size: 9px;">
                <?php echo JText::_('COM_CONCILIACIONBANCO_LEYEDA'); ?>
            </span>
            </label>
            <input type="text" id="integradoName" class="typeahead"/>
            <input type="hidden" name="integradoId" id="integradoId" value="0"/>
        </div>

        <div>
            <input type="button" class="btn btn-primary enviar" value="<?php echo JText::_('LBL_ENVIAR') ?>"/>
            <input type="button" class="btn btn-danger cancel" value="<?php echo JText::_('LBL_CANCELAR') ?>"/>
        </div>

    </form>
<?php }else { ?>
    <form id="conciliacionbanco" action="index.php?option=com_conciliacionbanco&task=detalle.save" method="post">
        <input type="hidden" name="integradoId" value="<?php echo $data->integradoId; ?>" />
        <input type="hidden" name="cuenta" value="<?php echo $data->cuenta; ?>" />
        <input type="hidden" name="referencia" value="<?php echo $data->referencia; ?>" />
        <input type="hidden" name="date" value="<?php echo $data->date; ?>" />
        <input type="hidden" name="amount" value="<?php echo $data->amount; ?>" />

        <h3><?php echo JText::_('CON_CONCILIACIONBANCO_CONFIRM_MSG'); ?></h3>

        <div>Banco - Cuenta : <?php echo $data->cuenta ?></div>
        <div>Referencia : <?php echo $data->referencia ?></div>
        <div>Fecha : <?php echo $data->date ?></div>
        <div>Monto: $<?php echo number_format($data->amount,2); ?></div>
        <?php if($data->integradoId != 0){ ?>
            <div>Integrado:</div>
        <?php }else{ ?>
            <div>Transaccion no Identificada.</div>
        <?php } ?>

        <div>
            <input type="submit" class="btn btn-primary" id="send" value="Guardar" />
            <input type="submit" class="btn btn-danger" id="cancel" value="<?php echo JText::_('LBL_CANCELAR'); ?>" />
        </div>
    </form>
<?php } ?>