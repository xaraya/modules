<?php

/**
 * Remove a gap in the celko tree
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   private
 * @param    integer    $startpoint    starting point for removing gap
 * @param    integer    $endpoint      end point for removing gap
 * @param    integer    $gapsize       the size of the gap to remove
 * @returns  integer    number of affected rows or false [0] on error
 */
function comments_userapi_remove_gap( $args ) {

    extract($args);
    
    if (!isset($startpoint)) {
        $msg = xarML('Missing or Invalid parameter \'startpoint\'!!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    
    if (!isset($gapsize) || $gapsize <= 1) {
        $gapsize = 2;
    }
     
    if (!isset($endpoint) || !is_numeric($endpoint)) {
        $endpoint = NULL;
    }
       
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $sql_left  = "UPDATE $xartable[comments]
                     SET xar_left = (xar_left - $gapsize)
                   WHERE xar_left > $startpoint";

    $sql_right = "UPDATE $xartable[comments]
                     SET xar_right = (xar_right - $gapsize)
                   WHERE xar_right >= $startpoint";

    // if we have an endpoint, use it :)
    if (!empty($endpoint) && $endpoint !== NULL) {
        $sql_left   .= " AND xar_left <= $endpoint";
        $sql_right  .= " AND xar_right <= $endpoint";
    }

    $result1 =& $dbconn->Execute($sql_left);
    $result2 =& $dbconn->Execute($sql_right);

    if(!$result1 || !$result2) {
        return;
    }

    return $dbconn->Affected_Rows();
}

?>