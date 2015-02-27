/**
 * Created by RicardoTIM on 19-Feb-15.
 */
var file_validation = function( event ) {

    var $fileInput = event.target;

    jQuery('.errormsg').remove();

    //check whether browser fully supports all File API
    if (window.File && window.FileReader && window.FileList && window.Blob)
    {
        var $error_span = '';

        if ($fileInput.files[0]) {

            var fsize = $fileInput.files[0].size;
            var ftype = $fileInput.files[0].type;

            switch(ftype)
            {
                case 'image/png':
                case 'image/gif':
                case 'image/jpeg':
                case 'image/pjpeg':
                case 'application/pdf':
                    break;
                default:
                    $error_span = '<span class="errormsg warning alert alert-danger">'+ event.data.msg +'</span>';
                    jQuery($fileInput).val('').after($error_span);
            }

            if (fsize >= 10000000) {
                $error_span = '<span class="errormsg warning alert alert-danger">'+ event.data.msg +'</span>';
                jQuery($fileInput).val('').after($error_span);
            }
        }
        return $fileInput;
    }else{
        alert("Please upgrade your browser, because your current browser lacks some new features we need!");
    }
};