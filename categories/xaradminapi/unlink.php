<?php

/**
 * Delete all links for a specific Item ID
 * @param $args['iid'] the ID of the item
 * @param $args['modid'] ID of the module
 * @param $args['itemtype'] item type
 * @param $args['confirm'] from delete GUI
 */
function categories_adminapi_unlink($args)
{
    // Get arguments from argument array
    extract($args);

    if (!empty($confirm)) {
        if (!xarSecurityCheck('AdminCategories')) return;
    } else {
        // Argument check
        if ((empty($modid)) || !is_numeric($modid) ||
            (empty($iid)) || !is_numeric($iid))
        {
            $msg = xarML('Invalid Parameter Count', '', 'admin', 'linkcat', 'categories');
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
    }

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable =& xarDBGetTables();
    $categorieslinkagetable = $xartable['categories_linkage'];

    // Delete the link
    $query = "DELETE FROM $categorieslinkagetable";

    if (!empty($modid)) {
        if (!is_numeric($modid)) {
            $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                         'module id', 'admin', 'unlink', 'categories');
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                            new SystemException($msg));
            return false;
        }
        if (empty($itemtype) || !is_numeric($itemtype)) {
            $itemtype = 0;
        }
        $query .= " WHERE xar_modid = '" . xarVarPrepForStore($modid) . "'
                      AND xar_itemtype = '" . xarVarPrepForStore($itemtype) . "'";
        if (!empty($iid)) {
            $query .= " AND xar_iid = '" . xarVarPrepForStore($iid) . "'";
        }
    }

    $result = $dbconn->Execute($query);
    if (!$result) return;

    return true;
}

?>
