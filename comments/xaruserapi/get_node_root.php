<?php

/**
 * Grab the id, left and right values for the
 * root node of a particular comment.
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer     modid      The module that comment is attached to
 * @param    integer     objectid   The particular object within that module 
 * @param    integer     itemtype   The itemtype of that object
 * @returns  array an array containing the left and right values or an
 *                 empty array if the comment_id specified doesn't exist
 */
function comments_userapi_get_node_root( $args ) 
{
    
    extract ($args);
    
    $exception = false;
    
    if (!isset($modid) || empty($modid)) {
        $msg = xarML('Missing or Invalid parameter \'modid\'!!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        $exception |= true;
    }

    if (!isset($objectid) || empty($objectid)) {
        $msg = xarML('Missing or Invalid parameter \'objectid\'!!');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        $exception |= true;
    }

    if ($exception) {
        return;
    }
    
    if (empty($itemtype)) {
        $itemtype = 0;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ctable = &$xartable['comments_column'];
    // grab the root node's id, left and right values
    // based on the objectid/modid pair
    $sql = "SELECT  $ctable[cid], $ctable[left], $ctable[right]
              FROM  $xartable[comments]
             WHERE  $ctable[modid]='$modid'
               AND  $ctable[itemtype]='$itemtype'
               AND  $ctable[objectid]='$objectid'
               AND  $ctable[status]='"._COM_STATUS_ROOT_NODE."'";

    $result =& $dbconn->Execute($sql);

    if(!$result)
        return;

    $count=$result->RecordCount();

    assert($count==1 | $count==0);

    if (!$result->EOF) {
        $node = $result->GetRowAssoc(false);
    } else {
        $node = array();
    }
    $result->Close();

    return $node;
}

?>