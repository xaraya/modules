<?php

// Handler for a data item from a previous result set
function reports_userapi_dataitemtag($args)
{
    extract($args);
    
    // Get some info on the datatype of the fieldname, most notably for blobs
    // those have to be cached and referred to. 
    if(!isset($type)) {
        $code = $dataset."->fields['$name']";
    } else {
        // Type denotes a mime formatted type, generate a cache representation of
        // TODO: test for value of id
        if(!isset($extension)) $extension='bin';// TODO: make this better
        if(!isset($offset)) $offset=0;
        $code = ';
        $cachekey = md5("'.$name.'".'.$dataset.'->fields[\''.$unique_id.'\']);
        $cacheFile = "var/cache/reports/$cachekey.'.$extension.'";
        $fp = @fopen($cacheFile,"w");
        if (!empty($fp)) {
            @fwrite($fp,substr('.$dataset.'->fields[\''.$name.'\'],'.$offset.'));
            @fclose($fp);
        }
        $'.$name.'="$cacheFile"';
        // Deliver the code back, having set the name to the link to the cached file
        return $code;
    }
    return $code;    
}

?>