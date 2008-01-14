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
 * update entry for a module item - hook for ('item','update','API')
 * Optional $extrainfo['poll'] from arguments, or 'poll' from input
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function polls_adminapi_updatehook($args)
{
    extract($args);

    if (!isset($extrainfo)) throw new EmptyParameterException('extrainfo');
    if (!isset($objectid)) throw new EmptyParameterException('objectid');
    if (!is_numeric($objectid)) throw new VariableValidationException(array('objectid',$objectid,'numeric'));

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('module name', 'admin', 'newhook', 'polls');
        throw new BadParameterException($vars,$msg);
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
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('module name', 'admin', 'newhook', 'polls');
        throw new BadParameterException($vars,$msg);
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

    if (empty($poll)) {
        return $extrainfo;
    }

    if (empty($poll['title']) || empty($poll['type'])) {
        // no poll
        $poll = null;
    } elseif ($poll['type'] != '0' && $poll['type'] != '1') {
        // invalid poll type
        $poll = null;
    } else {
        if (empty($poll['private'])) {
            $poll['private'] = 0;
        } else {
            $poll['private'] = 1;
        }
//    }
    if (empty($poll['start_date'])) {
        $poll['start_date'] = time();
            }
    if (empty($poll['end_date'])) {
        $poll['end_date'] = 0;
            }
 }
    // get the current poll for this item
    $oldpoll = xarModAPIFunc('polls','user','gethooked',
                             array('modname' => $modname,
                                   'itemtype' => $itemtype,
                                   'objectid' => $itemid));

    if (empty($oldpoll) && empty($poll)) {
        // nothing to do here
        return $extrainfo;

    } elseif (empty($oldpoll) && !empty($poll)) {
        // create the new poll here
        $pid = xarModAPIFunc('polls', 'admin', 'create',
                             array('title' => $poll['title'],
                                   'polltype' => $poll['type'],
                                   'private' => $poll['private'],
                                   // hooked poll
                                   'module' => $modname,
                                   'itemtype' => $itemtype,
                                   'itemid' => $objectid));
        if (empty($pid)) {
            throw new IDNotFoundException($pid,'Unable to create poll');
        
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

    } elseif (!empty($oldpoll) && empty($poll)) {
        // delete the old poll here
        if (!xarModAPIFunc('polls', 'admin', 'delete',
                           array('pid' => $oldpoll['pid']))) {
            throw new IDNotFoundException($oldpoll['pid'],'Unable to find poll id (#(1))');
        }
        if (isset($extrainfo['poll'])) {
            unset($extrainfo['poll']);
        }
        return $extrainfo;

    }

    // we need to check for changes here
    if (empty($oldpoll['pid'])) {
        // hmmm, something went wrong here :-)
        return $extrainfo;
    }
    $pid = $oldpoll['pid'];

    if ($poll['title'] != $oldpoll['title'] || $poll['type'] != $oldpoll['type'] || $poll['private'] != $oldpoll['private']) {
        // update the poll itself here
        $updated = xarModAPIFunc('polls', 'admin', 'update',
                                 array('pid' => $pid,
                                       'title' => $poll['title'],
                                       'type' => $poll['type'],
                                       'private' => $poll['private'],
                                       'start_date' => $poll['start_date'],
                                       'end_date' => $poll['end_date']));
        if (empty($updated)) {
                throw new IDNotFoundException('Unable to find poll id');
        }
    }

    $delete = array();
    $keep = array();
    $new = array();
    $change = array();
    // check what options we need to delete, what we can keep, and what's new
    if (count($oldpoll['options']) > 0) {
        foreach ($oldpoll['options'] as $id => $option) {
            if (empty($poll['options'][$id])) {
                $delete[$id] = $option;
            } else {
                $keep[$id] = $option;
            }
        }
        foreach ($poll['options'] as $id => $name) {
            if (empty($name)) continue;
            if (empty($keep[$id])) {
                $new[$id] = $name;
            } elseif ($name != $keep[$id]['name']) {
                $change[$id] = $name;
            }
        }
        if (count($delete) == 0 && count($new) == 0 && count($change) == 0) {
            // nothing has changed for the options
            $extrainfo['poll'] = $poll;
            return $extrainfo;
        }
    } elseif (count($poll['options']) > 0) {
        $new = $poll['options'];
    } else {
        // nothing has changed for the options
        $extrainfo['poll'] = $poll;
        return $extrainfo;
    }

    // Change existing options for this poll
    if (count($change) > 0) {
        foreach ($change as $id => $name) {
            xarModAPIFunc('polls', 'admin', 'updateopt',
                          array('pid' => $pid,
                                'opt' => $id,
                                'option' => $name));
        }
    }

    // Delete old options for this poll
    if (count($delete) > 0) {
        // Note : delete from last to first here, because deleteopt() re-sequences the options
        krsort($delete, SORT_NUMERIC);
        foreach ($delete as $id => $option) {
            xarModAPIFunc('polls', 'admin', 'deleteopt',
                          array('pid' => $pid,
                                'opt' => $id));
        }
    }

    // Create new options for this poll
    if (count($new) > 0) {
        foreach ($new as $id => $name) {
            xarModAPIFunc('polls', 'admin', 'createopt',
                          array('pid' => $pid,
                                'option' => $name));
        }
    }

    $extrainfo['poll'] = $poll;

    // Return the extra info
    return $extrainfo;
}

?>
