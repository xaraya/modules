<?php

/**
 * Returns which parts are synchronized and which are not. 
 */
function cachesecurity_adminapi_getsyncinfo()
{
    $files = array();

    $files['masks'] = xarModAPIFunc(
        'cachesecurity','admin','filename', array('part'=>'masks')
     );
    $files['privileges'] = xarModAPIFunc(
        'cachesecurity','admin','filename', array('part'=>'privileges')
     );
    $files['rolesgraph'] = xarModAPIFunc(
        'cachesecurity','admin','filename', array('part'=>'rolesgraph')
     );
    $files['privsgraph'] = xarModAPIFunc(
        'cachesecurity','admin','filename', array('part'=>'privsgraph')
     );
    
    $exists = array();
    foreach ($files as $part => $filename) {
        if (!file_exists($filename)) {
            $exists[$part] = false;
        } else {
            $exists[$part] = true;
        }
    }

    return $exists;
}

?>