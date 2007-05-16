<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage calendar
 * @author andrea.m
 */

 /**
 * @returns int (calendar id on success, false on failure)
 */
function calendar_adminapi_create_calendars($args)
{
    extract($args);

    // argument check
    if (!isset($calname)) {
        $msg = xarML('Calendar name not specified','admin','create','calendar');
        xarErrorSet(
            XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg)
        );
        return;
    }

    // TODO: should I move these two issets to the admin function
    // admin/create_calendars.php? --amoro
    if (!isset($mod_id)) {
        $module = xarRequestGetInfo();
        $mod_id = xarModGetIDFromName($module[0]);
    }
    if (!isset($role_id)) {
        $role_id = xarSession::getVar('uid');
    }

    // Load up database details.
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $caltable = $xartable['calendars'];

    // Insert instance details.
    $nextId = $dbconn->GenId($caltable);
    $query = 'INSERT INTO ' . $caltable . ' (
              xar_id,
              xar_role_id,
              xar_mod_id,
              xar_name
            ) VALUES (?, ?, ?, ?)';

    $result =& $dbconn->Execute(
        $query, array(
            $nextId, $role_id, $mod_id, $calname
        )
    );
    if (!$result) {return;}

    // Get ID of row inserted.
    $calendid = $dbconn->PO_Insert_ID($caltable, 'xar_id');

    // If not database type also add file info

    // Allow duplicate files here, to make it easier to delete them
    // WARNING: if somebody changes this you should also change the
    // delete function to avoid major dataloss!!! --amoro
    if ($addtype != 'db') {

        $filestable = $xartable['calfiles'];
        $cal_filestable = $xartable['calendars_files'];

        $nextID = $dbconn->GenId($filestable);
        $query = 'INSERT INTO ' . $filestable . ' (
                  xar_id,
                  xar_path
                ) VALUES (?, ?)';
        $result =& $dbconn->Execute(
            $query, array(
                $nextID,$fileuri
            )
        );

        // Get ID of row inserted.
        $fileid = $dbconn->PO_Insert_ID($filestable, 'xar_id');

        $query = 'INSERT INTO ' . $cal_filestable . ' (
                      xar_calendars_id,
                      xar_files_id
                    ) VALUES (?, ?)';
        $result =& $dbconn->Execute(
            $query, array(
                $calendid,$fileid
            )
        );
    }
    return $calendid;
}

?>
