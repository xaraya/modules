<?php
/**
 * File: $Id$
 *
 * Pubsub Admin API
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Pubsub Module
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */

/**
 * handle a pubsub 'delete' event
 * delete event for an item - hook for ('item','delete','API')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns array
 * @return $extrainfo, like any hook function should :)
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_deletehook($args)
{
    // Get arguments from argument array
    extract($args);

    // This has to be an argument
    if (empty($objectid)) {
        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
                     'object ID', 'deletehook', 'pubsub');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1) in function #(2)() in module #(3)',
                     'extrainfo', 'deletehook', 'pubsub');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (isset($extrainfo['module']) && is_string($extrainfo['module'])) {
        $modname = $extrainfo['module'];
    } else {
        $modname = xarModGetName();
    }
    $modid = xarModGetIDFromName($modname);
    if (!$modid) return $extrainfo; // throw back

    if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    $templateid = xarModGetVar('pubsub',"$modname.$itemtype.delete");
    // if there's no 'delete' template defined for this module+itemtype, we're done here
    if (empty($templateid)) {
        return $extrainfo;
    }

// FIXME: get categories for deleted item, if we're not too late already
    $cid = '';
    if (isset($extrainfo['cid']) && is_numeric($extrainfo['cid'])) {
        $cid = $extrainfo['cid'];
    } elseif (isset($extrainfo['cids'][0]) && is_numeric($extrainfo['cids'][0])) {
    // TODO: loop over all categories
        $cid = $extrainfo['cids'][0];
    } else {
        // Do nothing if we do not get a cid.
        return $extrainfo;
    }

    // process the event (i.e. create a job for each subscriber)
    if (!xarModAPIFunc('pubsub','admin','processevent',
                       array('modid' => $modid,
                             'itemtype' => $itemtype,
                             'cid' => $cid,
                             'objectid' => $objectid,
                             'templateid' => $templateid))) {
        // oops - but life goes on in hook functions :)
        return $extrainfo;
    }

    return $extrainfo;

} // END deletehook

?>
