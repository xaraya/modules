<?php

/**
 * Get the number of children comments for a list of comment ids
 *
 * @author mikespub
 * @access public
 * @param integer  $left the left limit for the list of comment ids
 * @param integer  $right the right limit for the list of comment ids
 * @returns array  the number of child comments for each comment id,
 *                   or raise an exception and return false.
 */
function comments_userapi_get_childcountlist($args) 
{

    extract($args);
    if ( !isset($left) || !is_numeric($left) ||
         !isset($right) || !is_numeric($right)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                 'left/right', 'userapi', 'get_childcountlist', 'comments');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ctable = &$xartable['comments_column'];

    $sql = "SELECT P1.xar_cid, COUNT(P2.xar_cid) AS numitems
              FROM $xartable[comments] AS P1,
                   $xartable[comments] AS P2
             WHERE P2.xar_left >= P1.xar_left
               AND P2.xar_left <= P1.xar_right
               AND P1.xar_left >= $left
               AND P1.xar_right <= $right
               AND P2.xar_status='"._COM_STATUS_ON."'
          GROUP BY P1.xar_cid";
/*
                   AND P1.xar_cid
                        IN (".join(', ',$ids).")
*/
    $result =& $dbconn->Execute($sql);
    if (!$result)
        return;

    if ($result->EOF) {
        return array();
    }

    $count = array();
    while (!$result->EOF) {
        list($id,$numitems) = $result->fields;
        // return total count - 1 ... the -1 is so we don't count the comment root.
        $count[$id] = $numitems - 1;
        $result->MoveNext();
    }

    $result->Close();

    return $count;
}

?>