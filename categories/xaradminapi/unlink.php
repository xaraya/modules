<?php

/**
 * Delete all links for a specific Item ID
 * @param $args['iid'] the ID of the item
 * @param $args['modid'] ID of the module
 * @param $args['itemtype'] item type
 */
function categories_adminapi_unlink($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($modid)) ||
        (!isset($iid)))
    {
        $msg = xarML('Invalid Parameter Count', join(', ', $invalid), 'admin', 'linkcat', 'categories');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if (!isset($itemtype) || !is_numeric($itemtype)) {
        $itemtype = 0;
    }

    // Confirm linkage exists
    $childiids = xarModAPIFunc('categories',
                              'user',
                              'getlinks',
                              array('iids' => array($iid),
                                    'itemtype' => $itemtype,
                                    'modid' => $modid,
                                    'reverse' => 0));

// Note : this is a feature, not a bug in this case :-)
    // If Link doesn´t exist then
    if ($childiids == Array()) {
        return true;
    }

// Note : yes, edit is enough here (cfr. updatehook)
    $cids = array_keys($childiids);
    foreach ($cids as $cid) {
        if(!xarSecurityCheck('EditCategoryLink',1,'Link',"$modid:All:$iid:$cid")) return;
    }

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $categorieslinkagetable = $xartable['categories_linkage'];

    // Delete the link
    $sql = "DELETE FROM $categorieslinkagetable
            WHERE xar_modid = " . xarVarPrepForStore($modid) . "
            AND xar_itemtype = " . xarVarPrepForStore($itemtype) . "
            AND xar_iid = " . xarVarPrepForStore($iid);
    $result = $dbconn->Execute($sql);
    if (!$result) return;

    return true;
}

?>
