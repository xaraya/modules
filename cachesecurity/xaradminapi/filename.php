<?php

/**
 * Returns the filename and path of the config file 
 */
function cachesecurity_adminapi_filename ($args)
{
    $part = $args['part'];
    $touchFile = xarCoreGetVarDirPath() . "/security/$part.touch";
    return $touchFile;
}

?>