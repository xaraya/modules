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
 * get information about a pubsub event
 *
 * @param $args['eventid'] the event id for the event
 * @returns array
 * @return array event information on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_getevent($args)
{
    // Get arguments from argument array
    extract($args);

    if (empty($eventid) || !is_numeric($eventid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'event id', 'admin', 'getevent', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (!xarModAPILoad('categories','user')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubsubeventstable = $xartable['pubsub_events'];
    $modulestable = $xartable['modules'];
    $categoriestable = $xartable['categories'];

    $query = "SELECT $pubsubeventstable.xar_modid,
                     $modulestable.xar_name,
                     xar_itemtype,
                     $pubsubeventstable.xar_cid,
                     xar_extra,
                     xar_groupdescr,
                     $categoriestable.xar_name
              FROM  $pubsubeventstable, $modulestable, $categoriestable
              WHERE xar_eventid = " . xarVarPrepForStore($eventid) . "
              AND   $pubsubeventstable.xar_cid = $categoriestable.xar_cid
              AND   $pubsubeventstable.xar_modid = $modulestable.xar_regid";
    $result = $dbconn->Execute($query);
    if (!$result) return;

    $info = array();

    if ($result->EOF) return false;

    list($info['modid'],
         $info['modname'],
         $info['itemtype'],
         $info['cid'],
         $info['extra'],
         $info['groupdescr'],
         $info['catname']) = $result->fields;

    return $info;
}

?>
