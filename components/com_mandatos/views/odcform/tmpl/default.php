<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHTML::_('behavior.calendar');

$sesion = JFactory::getSession();

$datos = $sesion->get('datos',null,'misdatos');
$msg    = $sesion->get('msg',null,'misdatos');

JFactory::getApplication()->enqueueMessage($msg,'ERROR');
$sesion->clear('msg','misdatos');
$sesion->clear('datos','misdatos');

$number2word    = new AifLibNumber;
$attsCal        = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');
?>
    <script src="/integradora/libraries/integradora/js/tim-validation.js"> </script>
    <script>
        jQuery(document).ready(function(){
            jQuery('#proveedor').on('change', muestraboton);
            jQuery('#agregarProveedor').on('click', agregaProveedor);
            jQuery('input:button').on('click',envio);
        });

        function envio (){
            var form     = jQuery('#generaODC');
            var datos    = form.serialize();
            var task     = '';
            var buttonId = jQuery(this).prop('id');


            switch(buttonId){
                case 'confirmarodc':
                    task = 'odcform.valida';
                    break;
                case 'enviar':
                    task = 'odcform.saveODC';
                    break;
                default :
                    break;
            }

            var request = jQuery.ajax({
                url: 'index.php?option=com_mandatos&task='+task+'&format=raw',
                data: datos,
                type: 'post'
            });

            request.done(function(result){
                var enviar = true;
                jQuery.each(result, function(k, v){
                    if(v != true){
                        mensajes(v.msg,'error',k);
                        enviar = false;
                    }
                });

                if(enviar === true && buttonId === 'confirmarodc'){
                    form.submit();
                }

                if(buttonId === 'enviar'){
                    if(result.redireccion){
                        window.location = result.urlRedireccion;
                    }
                }
            });
        }

        function muestraboton(){
            var valorCampo = jQuery(this).val();

            if(valorCampo == 'other'){
                jQuery('#agregarProveedor').show();
            }else{
                jQuery('#agregarProveedor').hide();
            }
        }

        function agregaProveedor(){
            window.location = 'index.php?option=com_mandatos&view=clientesform';
        }
    </script>

<?php
if(!isset($this->datos['confirmacion'])){
    $datos = is_null($datos)?$this->orden:$datos;
    $now = new DateTime('now', new DateTimeZone('America/Mexico_City'));
    $createdDate = isset($datos->createdDate) ? $datos->createdDate : $now->format('d-m-Y');
    ?>
    <h1><?php echo JText::_('COM_MANDATOS_ODC_FORM_TITULO'); ?></h1>

    <form id="generaODC" method="post" action="<?php echo JRoute::_('index.php?option=com_mandatos&view=odcform&confirmacion=1') ?>" role="form" enctype="multipart/form-data">
        <input type="hidden" name="integradoId" id="integradiId" value="<?php echo $this->integradoId; ?>" />
        <input type="hidden" name="numOrden" id="numOrden" value="<?php echo $datos->numOrden; ?>" />
        <input type="hidden" name="idOrden" id="idOrden" value="<?php echo $datos->id; ?>" />

        <div class="form-group">
            <label for="createdDate"><?php echo JText::_('COM_MANDATOS_ORDENES_FECHA_ORDEN')?></label>
            <p><?php echo $createdDate; ?></p>
        </div>

        <div class="form-group">
            <label for="proveedor"><?php echo JText::_('LBL_PROVEEDOR') ?></label>
            <select id="proveedor" name="proveedor">
                <?php
                foreach ($this->proveedores as $key => $value) {
                    $selected = $datos->proveedor == $value->id?'selected':'';
                    echo '<option value="'.$value->id.'" '.$selected.'>'.$value->displayName.'</option>';
                }
                ?>
                <option value="other"><?php echo JText::_('LBL_OTHER'); ?></option>
            </select>

            <div class="form-group" id="agregarProveedor" style="display: none;">
                <input type="button" class="btn btn-primary" value="<?php echo JText::_('LBL_CARGAR') ?>" />
            </div>
        </div>

        <div class="form-group">
            <label for="proyecto"><?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_TH_NAME_PROYECTO') ?></label>
            <select id="proyecto" name="proyecto">
                <?php
                foreach ($this->proyectos as $key => $value) {
                    $selected = $datos->proyecto == $value->id_proyecto?'selected':'';
                    echo '<option value="'.$value->id_proyecto.'" '.$selected.'>'.$value->name.'</option>';
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="paymentDate"><?php echo JText::_('LBL_PAYMENT_DATE'); ?></label>
            <?php
            $default = $datos->paymentDate != '' ? $datos->paymentDate : date('Y-m-d');
            echo JHTML::_('calendar',$default, 'paymentDate', 'paymentDate', $format = '%Y-%m-%d', $attsCal);
            ?>
        </div>

        <div class="form-group">
            <label for="paymentMethod"><?php echo JText::_('COM_MANDATOS_ODC_PAYMENTFORM'); ?></label>
            <select id="paymentMethod" name="paymentMethod">
                <option value="0" <?php echo $datos->paymentMethod == 0 ? 'selected' : ''; ?>><?php echo JText::_('LBL_SPEI'); ?></option>
                <option value="1" <?php echo $datos->paymentMethod == 1 ? 'selected' : ''; ?>><?php echo JText::_('LBL_CHEQUE'); ?></option>
            </select>
        </div>

        <div class="form-group">
            <label for="factura"><?php echo JText::_('LBL_FACTURA'); ?></label>
            <input type="file" name="factura" id="factura" />

        </div>

        <div class="form-group">
            <label for="observaciones"><?php echo JText::_('LBL_OBSERVACIONES'); ?></label>
            <textarea name="observaciones" id="observaciones" rows="10" cols="50" style="width: 306px"><?php echo $datos->observaciones; ?></textarea>
        </div>

        <div class="form-group">
            <input type="button" class="btn btn-primary" id="confirmarodc" value="<?php echo jText::_('LBL_ENVIAR'); ?>" />
            <input type="button" class="btn btn-danger"  onclick="window.history.back()" value="<?php echo jText::_('LBL_CANCELAR'); ?>" />
        </div>
    </form>
<?php
}else{
    $datePayment = new DateTime($this->datos['paymentDate']);

    $comprobante    = $this->dataXML->comprobante;
    $impuestos      = $this->dataXML->impuestos;
    $conceptos      = $this->dataXML->conceptos;

    $datos = $this->datos;
    ?>
    <div id="odc_preview">
        <h1><?php echo JText::_('MANDATOS_ODC_CONFIRMACION'); ?></h1>

        <div class="clearfix" id="cabecera">
            <div>
                <div class="span2 text-right">
                    <?php echo JText::_('COM_MANDATOS_PROYECTOS_LISTADO_TH_NAME_PROYECTO') ?>
                </div>
                <div class="span4">
                    <?php
                    foreach ($this->proyectos as $key => $value) {
                        if($value->id_proyecto == $this->datos['proyecto']){
                            echo $value->name;
                        }
                    }
                    ?>
                </div>

                <div class="span2 text-right">
                    <?php echo JText::_('LBL_PROVEEDOR') ?>:
                </div>
                <div class="span4">
                    <?php
                    foreach ($this->proveedores as $key => $value) {
                        if($value->id == $this->datos['proveedor']){
                            echo $value->displayName;
                        }
                    }
                    ?>
                </div>
            </div>
            <div>
                <div class="span2 text-right">
                    <?php echo JText::_('COM_MANDATOS_ODC_PAYMENTFORM'); ?>:
                </div>
                <div class="span4">
                    <?php echo $this->datos['paymentMethod']==0?'SPEI':'Cheque'; ?>
                </div>
            </div>
            <div>
                <div class="span2 text-right">
                    <?php echo JText::_('LBL_PAYMENT_DATE'); ?>:
                </div>
                <div class="span4">
                    <?php echo $datePayment->format('d-m-Y'); ?>
                </div>
            </div>
        </div>
    </div>

    <h3><?php echo JText::_('LBL_DESCRIP_PRODUCTOS'); ?></h3>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th class="span1">#</th>
            <th class="span2"><?php echo JText::_('LBL_CANTIDAD'); ?></th>
            <th class="span4"><?php echo JText::_('COM_MANDATOS_PRODUCTOS_LBL_DESCRIPTION'); ?></th>
            <th class="span1"><?php echo JText::_('LBL_UNIDAD'); ?></th>
            <th class="span2"><?php echo JText::_('LBL_P_UNITARIO'); ?></th>
            <th class="span2"><?php echo JText::_('LBL_IMPORTE'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($conceptos as $key => $value) {
            ?>
            <tr>
                <td><?php echo $key+1; ?></td>
                <td style="text-align: center;"><?php echo number_format($value['CANTIDAD']); ?></td>
                <td><?php echo $value['DESCRIPCION']; ?></td>
                <td><?php echo $value['UNIDAD']; ?></td>
                <td>
                    <div class="text-right">
                        $<?php echo number_format($value['VALORUNITARIO'], 2); ?>
                    </div>
                </td>
                <td>
                    <div class="text-right">
                        $<?php echo number_format($value['IMPORTE'], 2); ?>
                    </div>
                </td>
            </tr>
        <?php
        }
        ?>
        <tr>
            <td colspan="4" rowspan="3">
                <?php echo JText::_('LBL_MONTO_LETRAS'); ?>
                <span id="wordAmount"><?php echo $number2word->toCurrency('$'.number_format($comprobante['TOTAL'], 2)); ?></span>
            </td>
            <td class="span2">
                <?php echo JText::_('LBL_SUBTOTAL'); ?>
            </td>
            <td>
                <div class="text-right">$<?php echo number_format($comprobante['SUBTOTAL'], 2); ?></div>
            </td>
        </tr>
        <tr>
            <td class="span2">
                <?php echo number_format($impuestos->iva->tasa).'%'.JText::_('COM_MANDATOS_PRODUCTOS_LBL_IVA'); ?>
            </td>
            <td>
                <div class="text-right">$<?php echo number_format($impuestos->iva->importe, 2); ?></div>
            </td>
        </tr>
        <tr>
            <td class="span2">
                <?php echo JText::_('LBL_TOTAL'); ?>
            </td>
            <td>
                <div class="text-right">$<?php echo number_format($comprobante['TOTAL'], 2); ?></div>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="control-group" id="tabla-bottom">
        <div>
            <?php echo JText::_('LBL_OBSERVACIONES'); ?>:
        </div>
        <div>
            <?php echo $this->datos['observaciones']; ?>
        </div>
    </div>

    <form id="generaODC" action="#" method="post">
        <input type="hidden" name="integradoId"   value="<?php echo $datos['integradoId']; ?>" />
        <input type="hidden" name="idOrden"       value="<?php echo $datos['idOrden']; ?>" />
        <input type="hidden" name="numOrden"      value="<?php echo $datos['numOrden'] ?>" />
        <input type="hidden" name="proyecto"      value="<?php echo $datos['proyecto']; ?>" />
        <input type="hidden" name="proveedor"     value="<?php echo $datos['proveedor']; ?>" />
        <input type="hidden" name="paymentDate"   value="<?php echo $datos['paymentDate']; ?>" />
        <input type="hidden" name="paymentMethod" value="<?php echo $datos['paymentMethod']; ?>" />
        <input type="hidden" name="totalAmount"   value="<?php echo $comprobante['TOTAL']; ?>" />
        <input type="hidden" name="urlXML"        value="<?php echo $this->dataXML->urlXML; ?>" />
        <input type="hidden" name="observaciones" value="<?php echo $datos['observaciones']; ?>" />

        <div class="form-group">
            <input type="button" id="enviar" class="btn btn-primary" value="<?php echo jText::_('LBL_ENVIAR'); ?>" />
            <a class="btn btn-danger span3" id="cancel" href="<?php echo JRoute::_('index.php?option=com_mandatos&view=odclist'); ?>"><?php echo JText::_('LBL_CANCELAR'); ?></a>
        </div>
    </form>
<?php
}
?>