<?php

/** 
 *  Rename a file. (alias for file_move)
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   <type>
 *  @returns <type> 
 */

function filemanager_fsapi_rename( $args ) 
{ 
    return xarModAPIFunc('filemanager', 'fs', 'move', $args);
}

?>