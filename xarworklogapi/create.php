<?php
/**
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xproject module
 * @author Chad Kraeft <stego@xaraya.com>
*/
function xtasks_worklogapi_create($args)
{
    extract($args);
    
    if (!isset($ownerid) || !is_numeric($ownerid)) {
        $ownerid = xarModGetUserVar('xproject', 'mymemberid');
    }

    $invalid = array();
    if (!isset($taskid) || !is_numeric($taskid)) {
        $invalid[] = 'taskid';
    }
    if (!isset($ownerid) || !is_numeric($ownerid)) {
        $invalid[] = 'taskid';
    }
    if (!isset($eventdate) || !is_string($eventdate)) {
        $invalid[] = 'eventdate';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'worklog', 'create', 'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('RecordWorkLog', 1, 'Item', "All:All:All")) {
        $msg = xarML('Not authorized to add #(1) items',
                    'xtasks');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new SystemException($msg));
        return;
    }
    /*
//    $eventdate xarMLS_userOffset($eventdate);
    $timestamp = strtotime($eventdate);
    $timezonestr = xarModGetUserVar('roles','usertimezone');
    if(!empty($timezonestr)) {
        $timezonearray = unserialize($timezonestr);
        $timezone = $timezonearray['timezone'];
        $offset = $timezonearray['offset'];
    } else {
        $timezone = xarConfigGetVar('Site.Core.TimeZone');
    }
    if (isset($timestamp) && !empty($timezone) && function_exists('xarModAPIFunc')) {
        $adjust = xarModAPIFunc('base','user','dstadjust',
                                array('timezone' => $timezone,
                                      // pass the timestamp *with* the offset
                                      'time'     => $timestamp + $offset * 3600));

    } else {
        $adjust = 0;
    }
    $offset = $offset + $adjust;
    */
    $offset = xarMLS_userOffset($eventdate);
    $eventdate = date("Y-m-d H:i:s", strtotime($eventdate) - ($offset * 3600));
    /*
    $eventdate = xarLocaleFormatDate("%Y-%m-%d %H:%M:%S",$eventdate)
    
    
    
    $eventdateproperty = xarModAPIFunc('dynamicdata','user','getproperty',
                                         array('name' => 'eventdate',
                                               'type' => 'calendar',
                                               'validation' => ""));
    $check = $eventdateproperty->checkInput('eventdate');
    if (!$check) {
        $eventdate = '';
    } else {
        $eventdate = $eventdateproperty->value;
    }
    */

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $worklogtable = $xartable['xtasks_worklog'];

    $nextId = $dbconn->GenId($worklogtable);

    $query = "INSERT INTO $worklogtable (
                  worklogid,
                  taskid,
                  ownerid,
                  eventdate,
                  hours,
                  notes)
            VALUES (?,?,?,?,?,?)";
// CONVERT_TZ('".$eventdate."','".(isset($offset) ? sprintf("%02d", $offset).":00" : "+00:00")."','+00:00')
    $bindvars = array(
              $nextId,
              $taskid,
              $ownerid,
              $eventdate,
              $hours,
              $notes);
//echo "test: ".$query."<pre>";print_r($bindvars);die("</pre>");
    $result = &$dbconn->Execute($query,$bindvars);
    
    if (!$result) return;

    $worklogid = $dbconn->PO_Insert_ID($worklogtable, 'worklogid');
    
    $taskinfo = xarModAPIFunc('xtasks', 'user', 'get', array('taskid' => $taskid));
        
    if(!isset($hours_remaining)) $hours_remaining = $taskinfo['hours_remaining'];

    xarModAPIFunc('xtasks', 'admin', 'updatehours',
                array('taskid' => $taskinfo['taskid'],
                    'hours_spent_delta' => $hours,
                    'hours_remaining_delta' => $hours_remaining - $taskinfo['hours_remaining']));

    return $worklogid;
}

?>