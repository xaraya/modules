<?php

/**
 * Open a gap in the celko tree for inserting nodes
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   private
 * @param    integer    $startpoint    the point at wich the node will be inserted
 * @param    integer    $endpoint      end point for creating gap (used mostly for moving branches around)
 * @param    integer    $gapsize       the size of the gap to make (defaults to 2 for inserting a single node)
 * @param    integer    $modid         the module id
 * @param    integer    $itemtype      the item type
 * @param    string     $objectid      the item id
 * @returns  integer    number of affected rows or false [0] on error
 */
function comments_userapi_create_gap( $args ) 
{
    
    extract($args);
    
    if (!isset($startpoint)) {
        $msg = xarML('Missing or Invalid parameter \'startpoint\'!!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    
    if (!isset($endpoint) || !is_numeric($endpoint)) {
        $endpoint = NULL;
    }
       
    if (!isset($gapsize) || $gapsize <= 1) {
        $gapsize = 2;
    }
     
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $sql_left  = "UPDATE $xartable[comments]
                     SET xar_left = (xar_left + $gapsize)
                   WHERE xar_left > $startpoint";

    $sql_right = "UPDATE $xartable[comments]
                     SET xar_right = (xar_right + $gapsize)
                   WHERE xar_right >= $startpoint";

    // if we have an endpoint, use it :)
    if (!empty($endpoint) && $endpoint !== NULL) {
        $sql_left   .= " AND xar_left <= $endpoint";
        $sql_right  .= " AND xar_right <= $endpoint";
    }
    // if we have a modid, use it
    if (!empty($modid)) {
        $sql_left   .= " AND xar_modid = $modid";
        $sql_right  .= " AND xar_modid = $modid";
    }
    // if we have an itemtype, use it (0 is acceptable too here)
    if (isset($itemtype)) {
        $sql_left   .= " AND xar_itemtype = $itemtype";
        $sql_right  .= " AND xar_itemtype = $itemtype";
    }
    // if we have an objectid, use it
    if (!empty($objectid)) {
        $sql_left   .= " AND xar_objectid = '$objectid'";
        $sql_right  .= " AND xar_objectid = '$objectid'";
    }

    $result1 =& $dbconn->Execute($sql_left);
    $result2 =& $dbconn->Execute($sql_right);

    if(!$result1 || !$result2) {
        return;
    }

    return $dbconn->Affected_Rows();
}

?>
