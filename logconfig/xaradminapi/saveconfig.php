<?php

/*
* Save the config file to its appropriate place 
*/

function logconfig_adminapi_saveconfig()
{
    $content = xarModAPIFunc('logconfig','admin','produceconfig');
    $filename = xarModAPIFunc('logconfig','admin','filename');

     // BY: agrenier at assertex dot com
     // FROM: PHP's Manual entry for 'is_writeable' 

    if (file_exists($filename) && !is_writable($filename)) {
        if (!chmod($filename, 0666)) {
            $msg = xarML('Cannot change the mode of file "#(1)" so it can be writeable', $filename);
            xarExceptionSet(XAR_SYSTEM_EXCEPTION,'FUNCTION_FAILED',$msg);
            return;
        }
    }
    
    if (!$fp = @fopen($filename, "w")) {
        $msg = xarML('Cannot open file "#(1)', $filename);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'FUNCTION_FAILED',$msg);
        return;
    }
    
    if (fwrite($fp, "<?php \n" . $content . " \n?>") === FALSE) {
        $msg = xarML('Cannot write to file "#(1)"', $filename);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'FUNCTION_FAILED',$msg);
        return;
    } 
    
    if (!fclose($fp)) {
        $msg = xarML('Cannot close file "#(1)"', $filename);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'FUNCTION_FAILED',$msg);
        return;
    }

    return true;
}

?>