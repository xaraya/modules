<?php

/** 
 *  Obscures the given filename for added security
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   <type>
 *  @returns <type> 
 */

function uploads_userapi_file_obfuscate_name( $args ) 
{

    extract ($args);
    
    if (!isset($fileName) || empty($fileName)) {
        return FALSE;
    }
    $hash = crypt($fileName, substr(md5(time() . $fileName . getmypid()), 0, 2));
    $hash = substr(md5($hash), 0, 8) . time() . getmypid();
    
    return $hash;
    
}

?>