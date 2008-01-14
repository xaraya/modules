<?php
/*
 *
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
 * create an entry for a module item - hook for ('item','create','GUI')
 * Optional $extrainfo['poll'] from arguments, or 'poll' from input
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns array
 * @return extrainfo array
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function polls_adminapi_createhook($args)
{
    extract($args);

    if (!isset($extrainfo)) throw new EmptyParameterException('extrainfo');
    if (!isset($objectid)) throw new EmptyParameterException('objectid');
    if (!is_numeric($objectid)) throw new VariableValidationException(array('objectid',$objectid,'numeric'));

    if (!is_array($extrainfo)) {
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
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('module name', 'admin', 'newhook', 'polls');
        throw new BadParameterException($vars,$msg);
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
    if ($poll['type'] != '0' && $poll['type'] != '1') {
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
        throw new IDNotFoundException($pid,'Unable to find poll id (#(1))');
    }

    $poll['pid'] = $pid;

    $optlimit = xarModVars::Get('polls', 'defaultopts');
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
