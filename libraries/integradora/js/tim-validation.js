function mensajesError($result){
    var $enviar = true;
    jQuery('span.error').remove();

    if (typeof ($result.redirect) == 'undefined') {
        jQuery.each($result, function(k, v){
            if(v != true){
                mensajes(v.msg,'error',k);
                $enviar = false;
            }
        });
    }
    return $enviar;
}

function mensajes($obj){
    // reset accordions errors
    jQuery('.accordion-toggle').removeClass('alert-danger');

    jQuery.each($obj, function(k,v) {

        function inAccordion(spanError) {
            $accordion = spanError.parents('.accordion-group');

            $accordion.find('.accordion-toggle').addClass('alert-danger');
        }

        if(v != true && k != 'integradoId' && k != 'safeComplete'){
            var spanError = jQuery('#' + k);
            var divMsg = jQuery('.msgs_plataforma');

            spanError.siblings('.error').remove();

            switch (v.success) {
                case false:
                    spanError.css('border-color', '#FF0000');
                    var $errMsg = '<span class="error">' + v.msg + '</span>';
                    spanError.after($errMsg);

                    inAccordion(spanError);

                    spanError.focus(function () {
                        spanError.css('border-color', '');
                        jQuery('.error').remove();
                    });
                    break;
                case 'msg':
                    divMsg.fadeIn();
                    divMsg.prop('class', 'msgs_plataforma alert alert-warning');
                    divMsg.html('Datos Almacenados');
                    divMsg.delay(3500).fadeOut(400).prop('class', 'msgs_plataforma alert alert-warning');
                    break;
                default :
                    break;
            }

        }
    });
}
