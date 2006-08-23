<?php

/**
 * Get the number of comments for a list of module items
 *
 * @author  mikespub
 * @access  public
 * @param   integer   $modid        the id of the module that these nodes belong to
 * @param   integer   $itemtype     the item type that these nodes belong to
 * @param   array     $objectids    the list of ids of the items that these nodes belong to
 * @param   integer   $startdate    (optional) comments posted at startdate or later
 * @returns array     the number of comments for the particular modid/objectids pairs,
 *                    or raise an exception and return false.
 */
function comments_userapi_get_countlist($args) 
{
    extract($args);
    // $modid, $objectids

    $exception = false;
    
    if ( !isset($modid) || empty($modid) ) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                 'modid', 'userapi', 'get_countlist', 'comments');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        $exception |= true;
    }


    if ( !isset($objectids) || !is_array($objectids) ) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'objectids', 'userapi', 'get_countlist', 'comments');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        $exception |= true;
    }

    if ($exception) {
        return false;
    }
     
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ctable = &$xartable['comments_column'];

    $sql = "SELECT  $ctable[objectid], COUNT($ctable[cid]) as numitems
              FROM  $xartable[comments]
             WHERE  $ctable[modid]=$modid
               AND  $ctable[objectid] IN ('" . join("', '",$objectids) . "')
               AND  $ctable[status]="._COM_STATUS_ON;

    if (isset($itemtype) && is_numeric($itemtype)) {
        $sql .= " AND $ctable[itemtype]=$itemtype";
    }

    if (!empty($startdate) && is_numeric($startdate)) {
        $sql .= " AND $ctable[cdate]>=$startdate";
    }
 
    $sql .= " GROUP BY  $ctable[objectid]";

    $result =& $dbconn->Execute($sql);
    if (!$result)
        return;

    $count = array();
    while (!$result->EOF) {
        list($id,$numitems) = $result->fields;
        $count[$id] = $numitems;
        $result->MoveNext();
    }
    $result->Close();

    return $count;
}

?>
