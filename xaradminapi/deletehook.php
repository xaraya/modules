<?php
/**
 * Change Log Module version information
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage changelog
 * @link http://xaraya.com/index.php/release/185.html
 * @author mikespub
 */
/**
 * delete entry for a module item - hook for ('item','delete','API')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return bool true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function changelog_adminapi_deletehook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object id', 'admin', 'deletehook', 'changelog');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'extrainfo', 'admin', 'deletehook', 'changelog');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'admin', 'deletehook', 'changelog');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    if (!empty($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (!empty($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }
    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'deletehook', 'changelog');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $changelogtable = $xartable['changelog'];

    $editor = xarUserGetVar('uid');
    $forwarded = xarServerGetVar('HTTP_X_FORWARDED_FOR');
    if (!empty($forwarded)) {
        $hostname = preg_replace('/,.*/', '', $forwarded);
    } else {
        $hostname = xarServerGetVar('REMOTE_ADDR');
    }
    $date = time();
    $status = 'deleted';
/*
    if (isset($extrainfo['changelog_remark']) && is_string($extrainfo['changelog_remark'])) {
        $remark = $extrainfo['changelog_remark'];
    } else {
        xarVarFetch('changelog_remark', 'str:1:', $remark, NULL, XARVAR_NOT_REQUIRED);
        if (empty($remark)){
            $remark = '';
        }
    }
*/
    $remark = '';
// probably not relevant here...
//    $content = serialize($extrainfo);
    $content = '';

    // Get a new changelog ID
    $nextId = $dbconn->GenId($changelogtable);
    // Create new changelog
    $query = "INSERT INTO $changelogtable(xar_logid,
                                       xar_moduleid,
                                       xar_itemtype,
                                       xar_itemid,
                                       xar_editor,
                                       xar_hostname,
                                       xar_date,
                                       xar_status,
                                       xar_remark,
                                       xar_content)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $bindvars = array((int) $nextId,
                      (int) $modid,
                      (int) $itemtype,
                      (int) $objectid,
                      (int) $editor,
                      (string) $hostname,
                      (int) $date,
                      (string) $status,
                      (string) $remark,
                      (string) $content);

    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) {
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    $logid = $dbconn->PO_Insert_ID($changelogtable, 'xar_logid');

    // Return the extra info with the id of the newly created item
    // (not that this will be of any used when called via hooks, but
    // who knows where else this might be used)
    $extrainfo['changelogid'] = $logid;

    // Return the extra info
    return $extrainfo;
}

?>
