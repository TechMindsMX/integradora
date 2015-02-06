
function mensajesValidaciones($obj){
    var $enviar = true;
    // reset accordions errors
    jQuery('.accordion-toggle').removeClass('alert-danger');

    if (typeof $obj === "string") {
        $obj = JSON.parse($obj);
    }

    jQuery.each($obj, function(k,v) {

        function inAccordion(spanError) {
            $accordion = spanError.parents('.accordion-group');

            $accordion.find('.accordion-toggle').addClass('alert-danger');
        }

        if(v != true && k != 'integradoId' && k != 'safeComplete'){
            var spanError = jQuery('#' + k);

            spanError.siblings('.error').remove();

            switch (v.success) {
                case false:
                    $enviar = false;

                    spanError.css('border-color', '#FF0000');
                    var $errMsg = '<span class="error">' + v.msg + '</span>';
                    spanError.after($errMsg);

                    inAccordion(spanError);

                    spanError.focus(function () {
                        spanError.css('border-color', '');
                        jQuery('.error').remove();
                    });
                    break;
                default :
                    break;
            }
        }
    });

    return $enviar;
}

function messageInfo(msg, tipo) {
    var divMsg = jQuery('.msgs_plataforma');

    divMsg.fadeIn();
    divMsg.prop('class', 'msgs_plataforma alert alert-'+tipo);
    divMsg.html(msg);
    divMsg.delay(3500).fadeOut(400).prop('class', 'msgs_plataforma alert alert-'+tipo);

}


/**
 * $.unserialize
 *
 * Takes a string in format "param1=value1&param2=value2" and returns an object { param1: 'value1', param2: 'value2' }. If the "param1" ends with "[]" the param is treated as an array.
 *
 * Example:
 *
 * Input:  param1=value1&param2=value2
 * Return: { param1 : value1, param2: value2 }
 *
 * Input:  param1[]=value1&param1[]=value2
 * Return: { param1: [ value1, value2 ] }
 *
 * @todo Support params like "param1[name]=value1" (should return { param1: { name: value1 } })
 * Usage example: console.log($.unserialize("one="+escape("& = ?")+"&two="+escape("value1")+"&two="+escape("value2")+"&three[]="+escape("value1")+"&three[]="+escape("value2")));
 */
(function($){
    $.unserialize = function(serializedString){
        var str = decodeURI(serializedString);
        var pairs = str.split('&');
        var obj = {}, p, idx;
        for (var i=0, n=pairs.length; i < n; i++) {
            p = pairs[i].split('=');
            idx = p[0];
            if (obj[idx] === undefined) {
                obj[idx] = unescape(p[1]);
            }else{
                if (typeof obj[idx] == "string") {
                    obj[idx]=[obj[idx]];
                }
                obj[idx].push(unescape(p[1]));
            }
        }
        return obj;
    };
})(jQuery);