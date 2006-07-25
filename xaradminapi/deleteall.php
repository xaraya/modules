<?php

/**
 * delete all ratings items for a module - hook for ('module','remove','API')
 *
 * @param $args['objectid'] ID of the object (must be the module name here !!)
 * @param $args['extrainfo'] extra information
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xtasks_adminapi_deleteall($args)
{
    extract($args);

    // When called via hooks, we should get the real module name from objectid
    // here, because the current module is probably going to be 'modules' !!!
    if (!isset($objectid) || !is_string($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID (= module name)', 'admin', 'deleteall', 'xtasks');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    $modid = xarModGetIDFromName($objectid);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module ID', 'admin', 'deleteall', 'xtasks');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

// TODO: re-evaluate this for hook calls !!
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if(!xarSecurityCheck('DeleteXTask')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $xtasks_table = $xartable['xtasks'];

    $query = "DELETE FROM $xtasks_table
            WHERE xar_modid = ?";
    $result =& $dbconn->Execute($query, array($modid));
    if (!$result) return;

    // hmmm, I think we'll skip calling more hooks here... :-)
    //xarModCallHooks('item', 'delete', '', '');

// TODO: delete user votes with xarModDelVar('ratings',"$modname:$itemtype:$itemid");

    // Return the extra info
    if (!isset($extrainfo)) {
        $extrainfo = array();
    }
    return $extrainfo;
}

?>