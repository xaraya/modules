<?php

/**
 * create a new userpoints type
 *
 * @param $args['extrainfo'] extra information
 * @param $args['modname'] name of the calling module (not used in hook calls)
 * @param $args['itemtype'] optional item type for the item (not used in hook calls)
 * @param $args['hits'] optional hit count for the item (not used in hook calls)
 * @returns int
 * @return hitcount item ID on success, void on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function userpoints_adminapi_createptype($args)
{
    
    extract($args);

    if (!xarSecurityCheck('AdminUserpoints')) return; 

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pointstable = $xartable['pointstypes'];

    // Get a new pointstype ID
    $nextId = $dbconn->GenId($pointstable);
    // Create new pointstype
    $query = "INSERT INTO $pointstable(xar_uptid,
                                       xar_module,
                                       xar_itemtype,
                                       xar_action,
                                       xar_tpoints)
            VALUES ($nextId,
                    '" . xarVarPrepForStore($pmodule) . "',
                    '" . xarVarPrepForStore($itemtype) . "',
                    '" . xarVarPrepForStore($paction) . "',
                    '" . xarVarPrepForStore($tpoints) . "')";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $uptid = $dbconn->PO_Insert_ID($pointstable, 'xar_uptid');

    // hmmm, I think we'll skip calling more hooks here... :-)
    //xarModCallHooks('item', 'create', $hcid, 'hitcountid');

    // Return the extra info with the id of the newly created item
    // (not that this will be of any used when called via hooks, but
    // who knows where else this might be used)
    if (!isset($extrainfo)) {
        $extrainfo = array();
    }
    $extrainfo['[uptid'] = $uptid;
    return $extrainfo;
}

?>