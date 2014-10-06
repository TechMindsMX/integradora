function mensajes($msg, $tipo, $campo){
    var spanError = jQuery($campo);

    spanError.text($msg);
    spanError.fadeIn();

    switch($tipo){
        case 'msg':
            spanError.delay(800).fadeOut(4000);
            break;
        case 'error':
            jQuery('#bu_rfc').css('border-color', '#FF0000');
            spanError.delay(800).fadeOut(4000, function(){
                jQuery('#bu_rfc').css('border-color', '');
            });
            break;
    }

}
