<?php
/**
 * Polls Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */
/**
 * create an entry for a module item - hook for ('item','new','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return string hook output in HTML
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function polls_admin_newhook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'extrainfo', 'admin', 'newhook', 'polls');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (!isset($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'newhook', 'polls');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
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
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'admin', 'newhook', 'polls');
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

// TODO: security check based on calling module + item type + item id ?
    if (!xarSecurityCheck('EditPolls',0)) {
        return '';
    }

    $optcount = xarModGetVar('polls', 'defaultopts');
    if (empty($optcount)) {
        $optcount = 6;
    }

    if (isset($extrainfo['poll'])) {
        $poll = $extrainfo['poll'];
    } else {
         xarVarFetch('poll', 'array', $poll, null, XARVAR_NOT_REQUIRED);
    }
    if (empty($poll)) {
        $poll = array('title' => '',
                      'type' => 'single',
                      'private' => '',
                      'options' => array());
        for ($i = 1; $i <= $optcount; $i++) {
            $poll['options'][$i] = '';
        }
    }

    return xarTplModule('polls','admin','newhook',
                        array('optcount' => $optcount,
                              'poll' => $poll));
}

?>
