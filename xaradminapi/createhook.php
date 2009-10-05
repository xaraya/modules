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
 * create an entry for a module item - hook for ('item','create','GUI')
 * Optional $extrainfo['changelog_remark'] from arguments, or 'changelog_remark' from input
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @param $args['modname'] name of the calling module (not used in hook calls)
 * @param $args['itemtype'] optional item type for the item (not used in hook calls)
 * @param $args['item'] optional item info (not used in hook calls)
 * @return array extrainfo array
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function changelog_adminapi_createhook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'createhook', 'changelog');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $extrainfo = array();
    }

    // When called via hooks, modname wil be empty, but we get it from the
    // extrainfo or the current module
    if (empty($modname)) {
        if (!empty($extrainfo['module'])) {
            $modname = $extrainfo['module'];
        } else {
            $modname = xarModGetName();
        }
    }
    $modid = xarMod::getRegId($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'admin', 'createhook', 'changelog');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (!isset($itemtype) || !is_numeric($itemtype)) {
         if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
             $itemtype = $extrainfo['itemtype'];
         } else {
             $itemtype = 0;
         }
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $changelogtable = $xartable['changelog'];

    $editor = xarUserGetVar('uid');
    $forwarded = xarServer::getVar('HTTP_X_FORWARDED_FOR');
    if (!empty($forwarded)) {
        $hostname = preg_replace('/,.*/', '', $forwarded);
    } else {
        $hostname = xarServer::getVar('REMOTE_ADDR');
    }
    $date = time();
    $status = 'created';
    if (isset($extrainfo['changelog_remark']) && is_string($extrainfo['changelog_remark'])) {
        $remark = $extrainfo['changelog_remark'];
    } else {
        xarVarFetch('changelog_remark', 'str:1:', $remark, NULL, XARVAR_NOT_REQUIRED);
        if (empty($remark)){
            $remark = '';
        }
    }

    if (!empty($itemtype)) {
        $getlist = xarModVars::get('changelog',$modname.'.'.$itemtype);
    }
    if (!isset($getlist)) {
        $getlist = xarModVars::get('changelog',$modname);
    }
    if (!empty($getlist)) {
        $fieldlist = explode(',',$getlist);
    }
    $fields = array();
    foreach ($extrainfo as $field => $value) {
        // skip some common uninteresting fields
        if ($field == 'module' || $field == 'itemtype' || $field == 'itemid' ||
            $field == 'mask' || $field == 'pass' || $field == 'changelog_remark') {
            continue;
        }
        // skip fields we don't want here
        if (!empty($fieldlist) && !in_array($field,$fieldlist)) {
            continue;
        }
        $fields[$field] = $value;
    }
    // Check if we need to include any DD fields
    $withdd = xarModVars::get('changelog','withdd');
    if (empty($withdd)) {
        $withdd = '';
    }
    $withdd = explode(';',$withdd);
    if (xarModIsHooked('dynamicdata',$modname,$itemtype) && !empty($withdd) &&
        (in_array($modname,$withdd) || in_array("$modname.$itemtype",$withdd))) {
        // Note: we need to make sure the DD hook is called before the changelog hook here
        $ddfields = xarModAPIFunc('dynamicdata','user','getitem',
                                  array('modid' => $modid,
                                        'itemtype' => $itemtype,
                                        'itemid' => $objectid));
        if (!empty($ddfields)) {
            foreach ($ddfields as $field => $value) {
                // skip fields we don't want here
                if (!empty($fieldlist) && !in_array($field,$fieldlist)) {
                    continue;
                }
                $fields[$field] = $value;
            }
        }
    }
    $content = serialize($fields);
    $fields = array();

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

    $bindvars = array($nextId,
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

    return $extrainfo;
}

?>
