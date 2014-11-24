<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');
JHtml::_('behavior.keepalive');

$document	= JFactory::getDocument();
$app 		= JFactory::getApplication();
$attsCal    = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');
?>
    <script src="/integradora/libraries/integradora/js/tim-validation.js"> </script>
    <script>
        jQuery(document).ready(function(){
            jQuery('input:button').on('click', envioAjax);
        });

        function envioAjax() {
            var id= jQuery(this).prop('id');
            var form = jQuery('#oddform');
            var data = form.serialize();

            switch (id){
                case 'validacion':
                    task = 'oddform.valida';
                    break;
                case 'envio':
                    task = 'oddform.saveODD';
                    break;
            }

            var request = jQuery.ajax({
                url: "index.php?option=com_mandatos&task="+task+"&format=raw",
                data: data,
                type: 'post',
                async: false
            });

            request.done(function(result){
                var enviar = true;
                jQuery.each(result, function(k, v){
                    if(v != true){
                        mensajes(v.msg,'error',k);
                        enviar = false;
                    }
                });

                if(enviar === true && id === 'validacion'){
                    form.submit();
                }

                if(id === 'envio'){
                    if(result.redireccion){
                        window.location = result.urlRedireccion;
                    }
                }
            });

            request.fail(function (jqXHR, textStatus) {
                console.log(jqXHR, textStatus);
            });
        }
    </script>
<?php
if(!$this->confirmacion){
    ?>
    <h1><?php echo JText::_('COM_MANDATOS_ODDFORM_TITLE'); ?></h1>
    <form id="oddform" action="<?php echo $this->actionUrl; ?>" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="paymentMethod"><?php echo JText::_('COM_MANDATOS_ODC_PAYMENTFORM'); ?></label>
            <select id="paymentMethod" name="paymentMethod">
                <option value="0"><?php echo JText::_('LBL_SPEI'); ?></option>
                <option value="1"><?php echo JText::_('LBL_CHEQUE'); ?></option>
            </select>
        </div>

        <div class="form-group">
            <label for="paymentDate"><?php echo JText::_('LBL_DEPOSIT_DATE'); ?></label>
            <?php
            $default = date('Y-m-d');
            echo JHTML::_('calendar',$default, 'paymentDate', 'paymentDate', $format = '%Y-%m-%d', $attsCal);
            ?>
        </div>

        <div class="form-group">
            <label for="totalAmount"><?php echo JText::_('LBL_AMOUNT_DEPOSITED'); ?></label>
            <input type="text" name="totalAmount" id="totalAmount" />
        </div>

        <div class="form-group">
            <label for="attachment"><?php echo JText::_('LBL_PROOF'); ?></label>
            <input type="file" name="attachment" id="attachment" />
        </div>

        <div class="clearfix">&nbsp;</div>

        <div class="form-group">
            <input type="button" class="btn btn-primary" id="validacion" value="<?php echo JText::_('LBL_ENVIAR'); ?>">
            <input type="button" class="btn btn-danger"  onclick="window.history.back()" value="<?php echo jText::_('LBL_CANCELAR'); ?>" />
        </div>
    </form>
<?php
}else{
    $datos = $this->datos;
    $archivo = $this->file;
    $datos['attachment'] = $archivo['ruta'];
    $formadepago = array( JText::_('LBL_SPEI'), JText::_('LBL_CHEQUE') );
    ?>

    <h1><?php echo JText::_('COM_MANDATOS_ORDENES_DEPOSITO_LBL_CONFIMACION'); ?></h1>

    <div class="form-group">
        <span class="label-default"><?php echo JText::_('LBL_FORMA_PAGO'); ?>: </span>
        <span>
            <?php echo $formadepago[$datos['paymentMethod']]; ?>
        </span>
    </div>

    <div class="form-group">
        <span class="label-default"><?php echo JText::_('LBL_DEPOSIT_DATE'); ?>: </span>
        <span>
            <?php echo $datos['paymentDate'] ?>
        </span>
    </div>

    <div class="form-group">
        <span class="label-default"><?php echo JText::_('LBL_AMOUNT_DEPOSITED'); ?>: </span>
        <span>
            $<?php echo number_format($datos['totalAmount'],2 ); ?>
        </span>
    </div>

    <div class="form-group">
        <span class="label-default"><?php echo JText::_('LBL_ONLY_PROOF'); ?>: </span>
        <span>
            <a href="<?php echo JRoute::_($archivo['ruta']); ?>"><?php echo $archivo['name']; ?></a>
        </span>
    </div>

    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <form id="oddform" method="post" action="index.php?option=com_mandatos&view=oddform&integradoId=<?php echo $this->integradoId; ?>&task=oddform.saveODD">
            <input type="hidden" value='<?php echo json_encode($datos); ?>' name="datos" />
            <input type="button" class="btn btn-primary" id="envio" value="<?php echo JText::_('LBL_ENVIAR'); ?>">
            <input type="button" class="btn btn-danger"  onclick="window.history.back()" value="<?php echo jText::_('LBL_CANCELAR'); ?>" />
        </form>
    </div>
<?php
}
?>