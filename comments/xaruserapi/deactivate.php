<?php

/**
 * Activate the specified comment
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer     $comment_id     id of the comment to lookup
 * @returns  bool      returns true on success, throws an exception and returns false otherwise
 */
function comments_userapi_deactivate( $args ) 
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
            SET xar_status='"._COM_STATUS_OFF."'
            WHERE xar_cid='$cid'";

    $result =& $dbconn->Execute($sql);

    if (!$result)
        return;
}

?>