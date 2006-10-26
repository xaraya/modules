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
 * display changelog entry for a module item - hook for ('item','display','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function changelog_user_displayhook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'extrainfo', 'user', 'displayhook', 'changelog');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'user', 'displayhook', 'changelog');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (is_array($extrainfo) && !empty($extrainfo['module']) && is_string($extrainfo['module'])) {
        $modname = $extrainfo['module'];
    } else {
        $modname = xarModGetName();
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name ' . $modname, 'user', 'displayhook', 'changelog');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (is_array($extrainfo) && isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (is_array($extrainfo) && isset($extrainfo['itemid']) && is_numeric($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }

    $changes = xarModAPIFunc('changelog','admin','getchanges',
                             array('modid' => $modid,
                                   'itemtype' => $itemtype,
                                   'itemid' => $itemid,
                                   'numitems' => 1));
    // return empty string here
    if (empty($changes) || !is_array($changes) || count($changes) == 0) return '';

    $data = array_pop($changes);

    if (xarSecurityCheck('AdminChangeLog',0)) {
        $data['showhost'] = 1;
    } else {
        $data['showhost'] = 0;
    }

    $data['profile'] = xarModURL('roles','user','display',
                                 array('uid' => $data['editor']));
    if (!$data['showhost']) {
        $data['hostname'] = '';
    }
    if (!empty($data['remark'])) {
        $data['remark'] = xarVarPrepForDisplay($data['remark']);
    }
    $data['link'] = xarModURL('changelog','admin','showlog',
                              array('modid' => $modid,
                                    'itemtype' => $itemtype,
                                    'itemid' => $itemid));

// TODO: use custom template per module + itemtype ?
     return xarTplModule('changelog','user','displayhook',
                         $data);

}

?>
