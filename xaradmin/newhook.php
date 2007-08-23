<?php
/**
 * Comments Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Johnny Robeson <johnny@xaraya.com>
*/
include_once('modules/comments/xarincludes/defines.php');
/**
 * Create an entry for a module item - hook for ('item','new','GUI')
 *
 * @param int $args['objectid'] ID of the object
 * @param array $args['extrainfo'] extra information
 * @return string hook output in HTML
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function comments_admin_newhook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'extrainfo', 'admin', 'newhook', 'comments');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (!isset($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'newhook', 'comments');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    $data = array();

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
                    'module name', 'admin', 'newhook', 'comments');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
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

    if (isset($extrainfo['commentstatus'])) {
        $allowcomments = $extrainfo['commentstatus'];
    } else {
        xarVarFetch('commentstatus', 'int', $commentstatus, _COM_STATUS_ON, XARVAR_NOT_REQUIRED);
    }

    //if (!xarSecurityCheck('Add-Comments',0,'Item', "$modid:$itemtype:All")) return;

    $data['commentstatus'] = $commentstatus;
    $data['commentoptions'] = array(_COM_STATUS_ON => xarML('On'),
                                    _COM_STATUS_OFF => xarML('Off'),
                                    _COM_STATUS_LOCKED => xarML('Locked'));

    return xarTplModule('comments','admin','newhook',$data);
}
?>
