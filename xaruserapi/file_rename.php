<?php

/** 
 *  Rename a file. (alias for file_move)
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   <type>
 *  @returns <type> 
 */

function uploads_userapi_file_rename( $args ) 
{ 
    return xarModAPIFunc('uploads','user','file_move', $args);
}

?>