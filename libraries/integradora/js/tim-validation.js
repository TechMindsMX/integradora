function mensajes($msg, $tipo, $campo){
    var spanError = jQuery('#'+$campo);

    switch($tipo){
        case 'error':
            spanError.css('border-color', '#FF0000')
            var $errMsg = '<span class="error">'+$msg+'</span>';
            spanError.after($errMsg);

            spanError.focus(function(){
                spanError.css('border-color', '');
                jQuery('.error').remove();
            });
            break;
        default :
            break;
    }

}
