<?php

/** 
 *  Rename a file. (alias for file_move)
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   <type>
 *  @returns <type> 
 */

function uploads_fsapi_rename( $args ) 
{ 
    return xarModAPIFunc('uploads', 'fs', 'move', $args);
}

?>