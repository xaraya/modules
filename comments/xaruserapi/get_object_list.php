<?php

/**
 * Acquire a list of objectid's associated with a
 * particular Module ID in the comments table
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  private
 * @param   integer     $modid      the id of the module that the objectids are associated with
 * @param   integer     $itemtype   the item type that these nodes belong to
 * @returns array       A list of objectid's
 */
function comments_userapi_get_object_list( $modid, $itemtype = null ) 
{
    if (!isset($modid) || empty($modid)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                                'modid', 'userapi', 'get_object_list', 'comments');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $ctable = &$xartable['comments_column'];

    $sql     = "SELECT DISTINCT $ctable[objectid] AS pageid
                           FROM $xartable[comments]
                          WHERE $ctable[modid] = '$modid'";

    if (isset($itemtype) && is_numeric($itemtype)) {
        $sql .= " AND $ctable[itemtype]='$itemtype'";
    }

    $result =& $dbconn->Execute($sql);
    if (!$result) return;

    // if it's an empty set, return array()
    if ($result->EOF) {
        return array();
    }

    // zip through the list of results and
    // create the return array
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        $ret[$row['pageid']]['pageid'] = $row['pageid'];
        $result->MoveNext();
    }
    $result->Close();

    return $ret;

}

?>