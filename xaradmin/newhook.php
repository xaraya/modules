<?php
/**
 * XarBB - A lightweight BB for Xaraya
 *
 * @package modules
 * @copyright (C) 2003-2009 The Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarbb Module
 * @link http://xaraya.com/index.php/release/300.html
 * @author John Cox, Mikespub, Jo Dalle Nogare
*/
/**
 * modify an entry for a module item - hook for ('item','new','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return string hook output in HTML
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
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