<?php
/**
 * Polls module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Polls Module
 * @link http://xaraya.com/index.php/release/23.html
 * @author Jim McDonalds, dracos, mikespub et al.
 */
/**
 * create an entry for a module item - hook for ('item','create','GUI')
 * Optional $extrainfo['poll'] from arguments, or 'poll' from input
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return array extrainfo array
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function polls_adminapi_createhook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'createhook', 'polls');
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
                    'module name', 'admin', 'createhook', 'polls');
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

// TODO: security check based on calling module + item type + item id ?
    if (!xarSecurityCheck('EditPolls',0)) {
        return $extrainfo;
    }

    // check if we need to save some poll here
    xarVarFetch('poll', 'array', $poll, null, XARVAR_NOT_REQUIRED);

    if (empty($poll) && isset($extrainfo['poll']) && is_array($extrainfo['poll'])) {
        $poll = $extrainfo['poll'];
    }
    if (empty($poll) || empty($poll['title']) || empty($poll['type'])) {
        // no poll
        return $extrainfo;
    }
    if ($poll['type'] != 'single' && $poll['type'] != 'multi') {
        // invalid poll type
        return $extrainfo;
    }
    if (empty($poll['private'])) {
        $poll['private'] = 0;
    } else {
        $poll['private'] = 1;
    }

    // Pass to API
    $pid = xarModAPIFunc('polls', 'admin', 'create',
                         array('title' => $poll['title'],
                               'polltype' => $poll['type'],
                               'private' => $poll['private'],
                               // hooked poll
                               'module' => $modname,
                               'itemtype' => $itemtype,
                               'itemid' => $objectid));
    if (empty($pid)) {
        // Something went wrong - return
        $msg = xarML('Unable to create poll');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN');
        // we *must* return $extrainfo for now, or the next hook will fail
        return $extrainfo;
    }
    $poll['pid'] = $pid;

    $optlimit = xarModGetVar('polls', 'defaultopts');
    for ($i = 1; $i <= $optlimit; $i++) {
        if (!empty($poll['options'][$i])) {
            xarModAPIFunc('polls', 'admin', 'createopt',
                          array('pid' => $pid,
                                'option' => $poll['options'][$i]));
        }
    }

    $extrainfo['poll'] = $poll;

    return $extrainfo;
}

?>
