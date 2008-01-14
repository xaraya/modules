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
 * delete entry for a module item - hook for ('item','delete','API')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function polls_adminapi_deletehook($args)
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
        $vars = array('module id', 'admin', 'newhook', 'polls');
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
        $vars = array('item id', 'admin', 'newhook', 'polls');
        throw new BadParameterException($vars,$msg);
    }

  

    // get the current poll for this item
    $oldpoll = xarModAPIFunc('polls','user','gethooked',
                             array('modname' => $modname,
                                   'itemtype' => $itemtype,
                                   'objectid' => $itemid));

    if (empty($oldpoll) || empty($oldpoll['pid'])) {
        // nothing to do here
        return $extrainfo;
    }

    // delete the old poll here
    if (!xarModAPIFunc('polls', 'admin', 'delete',
                       array('pid' => $oldpoll['pid']))) {
    if (empty($pid)) {
        throw new IDNotFoundException($oldpoll['pid'],'Unable to find poll id (#(1))');
    }
    }
    if (isset($extrainfo['poll'])) {
        unset($extrainfo['poll']);
    }
    return $extrainfo;
}

?>
