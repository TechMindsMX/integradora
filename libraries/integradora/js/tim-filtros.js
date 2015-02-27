function filtro_fechas(){
    var fechaInicio = new Date(Date.parse(jQuery('#fechaInicio').val()));
    var fechafin    = new Date(Date.parse(jQuery('#fechaFin').val()));

    fechaInicioTS = fechafin.getTime()/1000;
    fechaFinTS = fechaInicio.getTime()/1000;

    var filas = jQuery('.row1');
    jQuery.each(filas, function(key, value){
        var fila = jQuery(value);
        var campo = fila.find('input[id*="fecha"]');
        if( (fechaInicioTS >= campo.val()) && (fechaFinTS <= campo.val()) ){
            fila.show();
        }else{
            fila.hide();
        }
    });
}

function filtro_autorizadas(){
    var valor	= parseInt( jQuery(this).val() );

    var allrows = jQuery('#myTable').find('tr[class*="type_"]');
    allrows.hide();

    switch(valor){
        case 0:
            allrows.each( function(k, v){
                v = jQuery(v);
                var c = parseInt(v.data('filtro'));
                if ( c < 5 ) {
                    v.show();
                }
            });
            break;
        case 1:
            allrows.each( function(k, v){
                v = jQuery(v);
                var c = parseInt(v.data('filtro'));
                if ( c >= 5 && c <= 21 ) {
                    v.show();
                }
            });
            break;
        case 3:
            allrows.show();
            break;
    }
}
