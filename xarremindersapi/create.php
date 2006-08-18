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
function xtasks_remindersapi_create($args)
{
    extract($args);

    $invalid = array();
    if (!isset($reminder_name) || !is_string($reminder_name)) {
        $invalid[] = 'reminder_name';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'reminders', 'create', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('AddXProject', 1, 'Item', "All:All:All")) {
        $msg = xarML('Not authorized to add #(1) items',
                    'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new SystemException($msg));
        return;
    }
    
    if(!isset($sequence)) {
        $ttlreminders = xarModAPIFunc('xproject', 'reminders', 'getall', array('projectid' => $projectid));
        $sequence = count($ttlreminders) + 1;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $remindertable = $xartable['xProject_reminders'];

    $nextId = $dbconn->GenId($remindertable);

    $query = "INSERT INTO $remindertable (
                  reminderid,
                  reminder_name,
                  projectid,
                  status,
                  sequence,
                  description,
                  relativeurl)
            VALUES (?,?,?,?,?,?,?)";

    $bindvars = array(
              $nextId,
              $reminder_name,
              $projectid,
              $status,
              $sequence,
              $description,
              $relativeurl);
              
    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;
    
    if((int)$sequence == $sequence) {
        xarModAPIFunc('xproject', 'reminders', 'sequence', array('projectid' => $projectid));
    }

    $logdetails = "Page created: ".$reminder_name.".";
    $logid = xarModAPIFunc('xproject',
                        'log',
                        'create',
                        array('projectid'   => $projectid,
                            'userid'        => xarUserGetVar('uid'),
                            'details'	    => $logdetails,
                            'changetype'	=> "PAGE"));

    $reminderid = $dbconn->PO_Insert_ID($remindertable, 'reminderid');

    return $reminderid;
}

?>