<?php

/**
 * Is security caching currently on? 
 */
function cachesecurity_adminapi_issynchronized()
{
    $files = array();
    $files['masks'] = xarModAPIFunc('logconfig','admin','filename', array('part'=>'masks'));
    $files['privileges'] = xarModAPIFunc('logconfig','admin','filename', array('part'=>'privileges'));
    $files['masks_struct'] = xarModAPIFunc('logconfig','admin','filename', array('part'=>'masks_struct'));
    $files['privileges_struct'] = xarModAPIFunc('logconfig','admin','filename', array('part'=>'privileges_struct'));

    $exists = true;
    foreach ($files as $file) {
        if (!file_exists($filename)) {
            $exists = false;
            break;
        }
    }

    return $exists;
}

?>