Validation.add('validate-filepath', 'Please enter a writable file path!', function (v) {
    return xar_gallery_testfilepath(v);
});

// Loads the validation object on the form
new Validation('modifyconfig');

function xar_gallery_testfilepath(path)
{
    url = 'index.php?module=gallery&type=ajax&func=server&action=TestFilePath';
	var pars = 'file_path=' + path;
    var myAjax = new Ajax.Request( url
        , { method: 'post'
            , parameters:   pars
            , asynchronous: false});
     if( myAjax.transport.responseText == 'true' ){ return true; }
     else{ return false; }
}
