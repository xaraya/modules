<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
/**
 * modify an entry for a module item - hook for ('item','new','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns string
 * @return hook output in HTML
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xarbb_admin_newhook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1)', 'extrainfo');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return $msg;
    }

    if (!isset($objectid)) {
        $msg = xarML('Invalid #(1)', 'object ID');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return $msg;
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
        $msg = xarML('Invalid #(1)', 'module name');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return $msg;
    }

    if (!empty($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (!empty($extrainfo['itemid']) && is_numeric($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }

    if (empty($itemid)) {
        $itemid = 0;
    }

    $data['items'] = xarModAPIFunc('xarbb', 'user', 'getallforums');

    if (isset($extrainfo['xarbb_forum'])) {
        $xarbb_forum = $extrainfo['xarbb_forum'];
    } else {
        if (!xarVarFetch('xarbb_forum', 'id', $xarbb_forum, NULL, XARVAR_DONT_SET)) return;
    }

    if (empty($xarbb_forum)) {
        $xarbb_forum = '';
    }
    
    $default=$xarbb_forum;
    
    return xarTplModule('xarbb','admin','newhook',
        array(
            'xarbb_forum' => $xarbb_forum,
            'default' => $xarbb_forum,
            'items' =>$data['items']
        )
    );
}

?>