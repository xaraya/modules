<?php

// Handler for a data item from a previous result set
function reports_userapi_dataitemtag($args)
{
    extract($args);

    // Get some info on the datatype of the fieldname, most notably for blobs
    // those have to be cached and referred to.
    if(!isset($type)) {
        //$code = "htmlentities(".$dataset."->fields['$name'],ENT_COMPAT,'UTF-8')";
        $code = "htmlspecialchars(utf8_encode(".$dataset."->fields['$name']))";
    } else {
        // Type denotes a mime formatted type, generate a cache representation of
        // TODO: test for value of id
        $cacheDir = xarCoreGetVarDirPath() . '/cache/reports/';
        if(!isset($extension)) $extension='bin';// TODO: make this better
        // Offset allows for example to handle OLE-objects (where a header is added)
        // The offset for an OLE-object is 78 by the way
        if(!isset($offset)) $offset=0;

        $code = ';';
        $code.= '$cachekey = md5("'.$name.'".'.$dataset.'->fields[\''.$unique_id.'\']);';
        $code.= '$cacheFile = "$cachekey.'.$extension.'";';
        $code.= 'if(!file_exists("'.$cacheDir.'$cacheFile")) {';
        $code.= '    $fp = fopen("'.$cacheDir.'$cacheFile","wb");';
        $code.= '    if (!empty($fp)) {';
        $code.= '        fwrite($fp,substr('.$dataset.'->fields[\''.$name.'\'],'.$offset.'));';
        $code.= '        fclose($fp);';
        $code.= '    }';
        $code.= '}';
        $code.= '$'.$name.'="$cacheFile";';
        // Deliver the code back, having set the name to the link to the cached file
        return $code;
    }
    return $code;
}

?>