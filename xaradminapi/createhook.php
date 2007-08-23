<?php
/**
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
 * create an entry for a module item - hook for ('item','create','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return array extrainfoo array
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function comments_adminapi_createhook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'createhook', 'comments');
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
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module id', 'admin', 'createhook', 'comments');
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


    if (isset($extrainfo['commentstatus'])) {
        $commentstatus = $extrainfo['commentstatus'];
    } else {
        xarVarFetch('commentstatus', 'int', $commentstatus, _COM_STATUS_ON, XARVAR_NOT_REQUIRED);
    }

     /**
      * If we don't have a root node, only create one if the status is not
      * _COM_STATUS_ON
      */
     if ($commentstatus == _COM_STATUS_ON) return $extrainfo;

     xarModAPIFunc('comments', 'user', 'add_rootnode',
             array('modid'    => $modid,
                   'itemtype' => $itemtype,
                   'objectid' => $objectid,
                   'status'   => $commentstatus));

    return $extrainfo;
}
?>
