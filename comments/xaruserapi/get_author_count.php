<?php

/**
 * Get the number of comments for a module based on the author
 *
 * @author mikespub
 * @access public
 * @param integer    $modid     the id of the module that these nodes belong to
 * @param integer    $itemtype  the item type that these nodes belong to
 * @param integer    $author      the id of the author you want to count comments for
 * @param integer    $status    (optional) the status of the comments to tally up
 * @returns integer  the number of comments for the particular modid/objectid pair,
 *                   or raise an exception and return false.
 */
function comments_userapi_get_author_count($args) 
{
    extract($args);

    $exception = false;
    
    if ( !isset($modid) || empty($modid) ) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                 'modid', 'userapi', 'get_count', 'comments');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        $exception |= true;
            }


    if ( !isset($author) || empty($author) ) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                 'author', 'userapi', 'get_count', 'comments');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        $exception |= true;
    }

    if ($exception) {
        return;
    }
    
    if (!isset($status) || empty($status)) {
        $status = _COM_STATUS_ON;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ctable = &$xartable['comments_column'];

    $sql = "SELECT  COUNT($ctable[cid]) as numitems
              FROM  $xartable[comments]
             WHERE  ($ctable[author]='$author' AND $ctable[modid]='$modid')
               AND  $ctable[status]='$status'";

    if (isset($itemtype) && is_numeric($itemtype)) {
        $sql .= " AND $ctable[itemtype]='$itemtype'";
    }

    $result =& $dbconn->Execute($sql);
    if (!$result)
        return;

    if ($result->EOF) {
        return 0;
    }

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

?>