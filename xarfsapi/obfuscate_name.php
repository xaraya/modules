<?php

/**
 *  Obscures the given name for added security
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   <type>
 *  @returns <type>
 */

function uploads_fsapi_obfuscate_name( $args )
{

    extract ($args);

    if (isset($fileName) && !isset($name)) {
        $name = $fileName;
    }
    
    if (!isset($name) || empty($name)) {
        return FALSE;
    }
    $hash = md5($name .  time() . getmypid());

    return $hash;

}

?>