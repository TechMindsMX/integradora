<?php
// no direct access
defined( '_JEXEC' ) or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHTML::_('behavior.calendar');

$integrados = $this->integrados;
$bancos = $this->bancos;
$data = $this->data;
$attsCal = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');

echo '<script src="../libraries/integradora/js/tim-validation.js"> </script>';
?>

    <script>
        var integradosArray   = new Array();
        var integradoNon_id   = new Array();
        <?php
                foreach ($integrados as $key => $value) {
                    if ($value->integrado->status == 50 ) {
	                    echo 'integradosArray['.$key.'] = "'.$value->displayName.'";'."\n";
	                    echo 'integradoNon_id["'.$value->displayName.'"] = "'.$value->integrado->integradoId.'";'."\n";
                    }
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

            var data = formulario.serialize();

            var parametros = {
                'link': 'index.php?option=com_adminintegradora&task=validacionforms.validatx&format=raw',
                'datos': data
            };

            var request = jQuery.ajax({
                url: parametros.link,
                data: parametros.datos,
                type: 'post'
            });

            request.done(function (response) {
                if (response.success) {
                    formulario.submit();
                } else {
                    mensajesValidaciones(response);
                }
            });
        }

        function cancelar() {
            window.location = 'index.php?option=com_adminintegradora';
        }

        jQuery(document).ready(function(){
            jQuery('.typeahead').typeahead(typeaheadSettings);
            jQuery('.enviar').on('click', enviar);
            jQuery('.cancel').on('click', cancelar);
        });
    </script>
<?php if(is_null($data->confirmacion)) {
    ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
        <div class="form-group">
            <p><?php echo JText::_('LBL_REGISTRO_TX_BANCO_INTRO'); ?></p>
        </div>
        <form id="conciliacionbanco" action="index.php?option=com_adminintegradora&view=conciliacionbancoform&confirmacion=1" method="post" autocomplete="off">
            <input type="hidden" name="id" id="id" value="<?php $data->id; ?>" />

            <div>
                <label for="cuenta"><?php echo JText::_('COM_CONCILIACIONBANCO_SELECT_BANCO'); ?></label>
                <select name="cuenta" id="cuenta">
                    <option value=""><?php echo JText::_('LBL_SELECCIONE_OPCION'); ?></option>
                    <?php foreach ($this->bancosIntegradora as $key => $value) {
                        $nombreBanco = $bancos[$value->banco_codigo];
                        $cuenta = substr($value->banco_clabe, -4, 4);
                        ?>
                        <option
                            value="<?php echo $value->datosBan_id; ?>"><?php echo $nombreBanco . ' - ' . $cuenta; ?></option>
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
                <input type="text" id="integradoName" class="typeahead" autocomplete="off"/>
                <input type="hidden" name="integradoId" id="integradoId" value="0"/>
            </div>

            <div>
                <input type="button" class="btn btn-primary enviar" value="<?php echo JText::_('LBL_ENVIAR') ?>"/>
                <input type="button" class="btn btn-danger cancel" value="<?php echo JText::_('LBL_CANCELAR') ?>"/>
            </div>

        </form>
    </div>
<?php }else { ?>
    <form id="conciliacionbanco" action="index.php?option=com_adminintegradora&task=conciliacionbancoform.save" method="post" autocomplete="off">
        <input type="hidden" name="integradoId" value="<?php echo $data->integradoId; ?>" />
        <input type="hidden" name="cuenta" value="<?php echo $data->cuenta; ?>" />
        <input type="hidden" name="referencia" value="<?php echo $data->referencia; ?>" />
        <input type="hidden" name="date" value="<?php echo $data->date; ?>" />
        <input type="hidden" name="amount" value="<?php echo $data->amount; ?>" />

        <h3><?php echo JText::_('CON_CONCILIACIONBANCO_CONFIRM_MSG'); ?></h3>

	    <?php  ?>

        <div>Banco - Cuenta : <?php echo AdminintegradoraHelper::getBanknameAccount($this->bancosIntegradora, $data->cuenta); ?></div>
        <div>Referencia : <?php echo $data->referencia ?></div>
        <div>Fecha : <?php echo $data->date ?></div>
        <div>Monto: $<?php echo number_format($data->amount,2); ?></div>
        <?php if($data->integradoId !== '0'){ ?>
            <div>Integrado: <?php echo $this->nombreIntegrado; ?></div>
        <?php }else{ ?>
            <div>Transaccion no Identificada.</div>
        <?php } ?>

        <div class="clearfix">&nbsp;</div>
        <div>
            <input type="submit" class="btn btn-primary" id="send" value="Guardar" />
            <input type="submit" class="btn btn-danger" id="cancel" value="<?php echo JText::_('LBL_CANCELAR'); ?>" />
        </div>
    </form>
<?php } ?>