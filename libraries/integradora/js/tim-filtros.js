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
