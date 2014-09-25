jQuery(function($) {
    $('#authorize').click(function(e){
        var $authBoton = $('#authorize-btn');
        console.log($authBoton);
        if($(e.target).prop("checked") == true){
            $authBoton.show();
        }else{
            $authBoton.hide();
        }
    });
});