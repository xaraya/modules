<?php

/**
 * Activate the specified comment
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer     $cid     id of the comment to lookup
 * @returns  bool        returns true on success, throws an exception and returns false otherwise
 */
function comments_userapi_activate( $args ) 
{
    
    extract($args);
    
    if (empty($cid)) {
        $msg = xarML('Missing or Invalid parameter \'cid\'!!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // First grab the objectid and the modid so we can
    // then find the root node.
    $sql = "UPDATE $xartable[comments]
            SET xar_status='" . _COM_STATUS_ON."'
            WHERE xar_cid=?";
    $bindvars = array((int) $cid);

    $result =& $dbconn->Execute($sql,$bindvars);

    if (!$result)
        return;
}


?>
