jQuery.fn.clearForm = function() {
    return this.each(function() {
        var type = this.type, tag = this.tagName.toLowerCase();
        if (tag == 'form')
            return jQuery(':input',this).clearForm();
        if (type == 'text' || type == 'password' || tag == 'textarea')
            this.value = '';
        else if (type == 'checkbox' || type == 'radio')
            this.checked = false;
        else if (tag == 'select')
            this.selectedIndex = -1;
    });
}


jQuery(document).ready( function() {
    var $body = jQuery('body');
    var $divLoading = '<link type="text/css" href="libraries/integradora/js/css/ajaxloader.css" rel="stylesheet" />' +
    '<div id="loadingDiv"><div id="fadingBarsG"><div id="fadingBarsG_1" class="fadingBarsG"></div><div id="fadingBarsG_2" class="fadingBarsG"></div><div id="fadingBarsG_3" class="fadingBarsG"></div><div id="fadingBarsG_4" class="fadingBarsG"></div><div id="fadingBarsG_5" class="fadingBarsG"></div><div id="fadingBarsG_6" class="fadingBarsG"></div><div id="fadingBarsG_7" class="fadingBarsG"></div><div id="fadingBarsG_8" class="fadingBarsG"></div></div></div>';
    $body.append($divLoading);

    var $loading = jQuery('#loadingDiv').hide();
    jQuery(document)
        .ajaxStart(function () {
            $loading.show();
        })
        .ajaxStop(function () {
            $loading.hide();
        });

    jQuery('input[type="submit"], input.submit, button[type="submit"], button.submit, a.submit').click( function() {
        $loading.show();
    });
});
