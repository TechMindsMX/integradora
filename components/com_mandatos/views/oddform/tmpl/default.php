<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.validation');
jimport('joomla.html.html.bootstrap');
jimport('integradora.numberToWord');
JHtml::_('behavior.keepalive');
$document	= JFactory::getDocument();

$document->addScript('//code.jquery.com/ui/1.11.3/jquery-ui.js');
$document->addScript('libraries/integradora/js/tim-datepicker-defaults.js');
$document->addStyleSheet('//code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css');

$app 		= JFactory::getApplication();
$attsCal    = array('class'=>'inputbox forceinline', 'size'=>'25', 'maxlength'=>'19');
?>
    <script src="libraries/integradora/js/tim-validation.js"> </script>
    <script>
        jQuery(document).ready(function(){
	        jQuery('.datepicker').datepicker();

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
                mensajesValidaciones(result);

                var enviar = true;

                jQuery.each(result, function(k, v){
                    if(v != true){
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
$datos = $this->datos;

if(!$this->confirmacion){
    ?>
    <h1><?php echo JText::_('COM_MANDATOS_ODDFORM_TITLE'); ?></h1>
    <form id="oddform" action="<?php echo $this->actionUrl; ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" id="id" value="<?php echo $datos->id; ?>" />
        <input type="hidden" name="numOrden" id="numOrden" value="<?php echo $datos->numOrden; ?>" />

        <div class="form-group">
            <label for="paymentMethod"><?php echo JText::_('COM_MANDATOS_ODC_PAYMENTFORM'); ?></label>
            <select id="paymentMethod" name="paymentMethod">
	            <?php
	            $cat = new Catalogos();
	            foreach ( $cat->getPaymentMethods() as $key => $val ) {
		            if ( isset( $datos->paymentMethod ) ) {
			            $selected = ($datos->paymentMethod->id == $key) ? ' selected ' : '';
		            }
		            echo '<option value="'.$key.'"'.$selected.'>'.JText::_($val->tag).'</option>';
	            }
	            ?>
            </select>
        </div>

        <div class="form-group">
            <label for="paymentDate"><?php echo JText::_('LBL_DEPOSIT_DATE'); ?></label>
	        <input type="text" name="paymentDate" id="paymentDate" class="datepicker" readonly />
        </div>

        <div class="form-group">
            <label for="totalAmount"><?php echo JText::_('LBL_AMOUNT_DEPOSITED'); ?></label>
            <input maxlength="10" type="text" name="totalAmount" id="totalAmount" value="<?php echo $datos->totalAmount; ?>"/>
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
    $archivo = $this->file;
    $datos['attachment'] = $archivo['name'];
    $formadepago = CatalogoFactory::create()->getPaymentMethods(true);
    ?>

    <h1><?php echo JText::_('COM_MANDATOS_ORDENES_DEPOSITO_LBL_CONFIMACION'); ?></h1>

    <div class="form-group">
        <span class="label-default"><?php echo JText::_('LBL_FORMA_PAGO'); ?>: </span>
        <span>
            <?php echo JText::_($formadepago[$datos['paymentMethod']]->tag); ?>
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
            <a target="_blank" href="<?php echo JRoute::_($archivo['ruta']); ?>"><?php echo $archivo['name']; ?></a>
        </span>
    </div>

    <div class="clearfix">&nbsp;</div>

    <div class="form-group">
        <form id="oddform" method="post" action="index.php?option=com_mandatos&view=oddform&task=oddform.saveODD">

            <input type="hidden" value='<?php echo json_encode($datos); ?>' name="datos" />
            <input type="button" class="btn btn-primary" id="envio" value="<?php echo JText::_('LBL_ENVIAR'); ?>">
            <input type="button" class="btn btn-danger"  onclick="window.history.back()" value="<?php echo jText::_('LBL_CANCELAR'); ?>" />
        </form>
    </div>
<?php
}
?>