function mensajes($msg, $tipo, $campo){
    var spanError   = jQuery('#'+$campo);
    var divMsg      = jQuery('.msgs_plataforma');
    switch($tipo){
        case 'error':
            spanError.css('border-color', '#FF0000');
            var $errMsg = '<span class="error">'+$msg+'</span>';
            spanError.after($errMsg);

            spanError.focus(function(){
                spanError.css('border-color', '');
                jQuery('.error').remove();
            });
            break;
        case 'msg':
            divMsg.fadeIn();
            divMsg.prop('class', 'msgs_plataforma alert alert-warning');
            divMsg.html('Datos Almacenados');
            divMsg.delay( 3500 ).fadeOut( 400).prop('class', 'msgs_plataforma alert alert-warning');
            break;
        default :
            break;
    }

}
