<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file:  Initialisation functions for example
// ----------------------------------------------------------------------

/**
 * initialise the example module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function xproject_init()
{
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    include ('includes/xarTableDDL.php');

    $xprojecttable = $xartable['xproject'];

    $fields = array(
        'xar_projectid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_name'=>array('type'=>'varchar','size'=>255,'null'=>FALSE),
        'xar_description'=>array('type'=>'blob'),
        'xar_clientgroup'=>array('type'=>'integer','null'=>TRUE, 'default'=>'0'),
        'xar_ownergroup'=>array('type'=>'integer','null'=>TRUE, 'default'=>'0'),
        'xar_usedatefields'=>array('type'=>'integer','size'=>'tiny','default'=>'1'),
        'xar_usehoursfields'=>array('type'=>'integer','size'=>'tiny','default'=>'1'),
        'xar_usefreqfields'=>array('type'=>'integer','size'=>'tiny','default'=>'1'),
        'xar_allowprivate'=>array('type'=>'integer','size'=>'tiny','default'=>'1'),
        'xar_importantdays'=>array('type'=>'integer','size'=>'tiny','default'=>'1'),
        'xar_criticaldays'=>array('type'=>'integer','size'=>'tiny','default'=>'1'),
        'xar_sendmailfreq'=>array('type'=>'integer','size'=>'tiny','default'=>'1'),
        'xar_billable'=>array('type'=>'integer','size'=>'tiny','default'=>'1'));

    $sql = xarDBCreateTable($xprojecttable,$fields);
    if (empty($sql)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($sql);

    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $xprojecttable2 = $xartable['xproject_tasks'];

    $fields2 = array(
        'xar_taskid'				=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_parentid'			=>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'xar_projectid'			=>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'xar_name'				=>array('type'=>'varchar','size'=>255,'null'=>FALSE),
        'xar_status'				=>array('type'=>'integer','null'=>TRUE, 'default'=>'0'),
        'xar_priority'			=>array('type'=>'integer','null'=>FALSE, 'default'=>'1'),
        'xar_description'		=>array('type'=>'blob'),
        'xar_private'			=>array('type'=>'integer','null'=>TRUE,'size'=>'tiny', 'default'=>'0'),
        'xar_creator'			=>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'xar_owner'				=>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'xar_assigner'			=>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'xar_groupid'			=>array('type'=>'integer','null'=>TRUE, 'default'=>'0'),
        'xar_date_created'		=>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'xar_date_approved'		=>array('type'=>'integer','null'=>TRUE, 'default'=>'0'),
        'xar_date_changed'		=>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'xar_date_start_planned'	=>array('type'=>'integer','null'=>TRUE, 'default'=>'0'),
        'xar_date_start_actual'	=>array('type'=>'integer','null'=>TRUE, 'default'=>'0'),
        'xar_date_end_planned'	=>array('type'=>'integer','null'=>TRUE, 'default'=>'0'),
        'xar_date_end_actual'	=>array('type'=>'integer','null'=>TRUE, 'default'=>'0'),
        'xar_hours_planned'		=>array('type'=>'integer','null'=>TRUE, 'default'=>'0'),
        'xar_hours_spent'		=>array('type'=>'integer','null'=>TRUE, 'default'=>'0'),
        'xar_hours_remaining'	=>array('type'=>'integer','null'=>TRUE, 'default'=>'0'),
        'xar_cost'				=>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'xar_recurring'			=>array('type'=>'integer','null'=>TRUE, 'size'=>'tiny', 'default'=>'0'),
        'xar_periodicity'		=>array('type'=>'integer','null'=>TRUE, 'size'=>'tiny', 'default'=>'0'),
        'xar_reminder'			=>array('type'=>'integer','null'=>TRUE, 'size'=>'tiny', 'default'=>'0'));

    $sql2 = xarDBCreateTable($xprojecttable2,$fields2);
    if (empty($sql2)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table
    $dbconn->Execute($sql2);

    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql2);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }


    if(xarModIsAvailable('categories'))
    {
        $mastercid = xarModAPIFunc('categories',
                                    'admin',
                                    'create',
                                    Array('name' => 'xproject',
                                          'description' => 'xproject Categories',
                                          'parent_id' => 0));
        // Note: you can have more than 1 mastercid (cfr. articles module)
        xarModSetVar('xproject', 'mastercid', $mastercid);
        $projectcategories = array();
        $projectcategories[] = array('name' => "My Tasks",
                                      'description' => "My Personal ToDo List");
        $projectcategories[] = array('name' => "Public",
                                      'description' => "Events open to the public");
        $projectcategories[] = array('name' => "Administration",
                                      'description' => "Site Administration");
        foreach($projectcategories as $project)
        {
            $projectsubcid = xarModAPIFunc('categories',
                                           'admin',
                                           'create',
                                           Array('name' => $project['name'],
                                                 'description' => $project['description'],
                                                 'parent_id' => $mastercid));
        }

        $statuslistcid = xarModAPIFunc('categories',
                                    'admin',
                                    'create',
                                    Array('name' => 'Status List',
                                          'description' => 'Master container for status list',
                                          'parent_id' => 0));
        // Note: you can have more than 1 mastercid (cfr. articles module)
        xarModSetVar('xproject', 'statuslistcid', $statuslistcid);
        $statuslistcategories = array();
        $statuslistcategories[] = array('name' => "Open",
                                      'description' => "Task is not yet completed");
        $statuslistcategories[] = array('name' => "Completed",
                                      'description' => "Task is completed");
        foreach($statuslistcategories as $status)
        {
            $statusid = xarModAPIFunc('categories',
                                                                   'admin',
                                                                   'create',
                                                                   Array('name' => $status['name'],
                                                                                 'description' => $status['description'],
                                                                                 'parent_id' => $statuslistcid));
        }
    }
/*
    if (xarModAPILoad('users', 'admin')) {
                // Call the API function
                $state = xarModAPIFunc('users',
                                                          'admin',
                                                          'createvar',
                                                          array('name' => xarML('Email notification on task change'),
                                                                        'type' => _UDCONST_INTEGER,
                                                                        'default' => 1));

                $state = xarModAPIFunc('users',
                                                          'admin',
                                                          'createvar',
                                                          array('name' => xarML('Only show tasks assigned to me'),
                                                                        'type' => _UDCONST_INTEGER,
                                                                        'default' => 1));

                $state = xarModAPIFunc('users',
                                                          'admin',
                                                          'createvar',
                                                          array('name' => xarML('Primary project / tasklist'),
                                                                        'type' => _UDCONST_INTEGER,
                                                                        'default' => 1));
    } else {


        }
*/
    xarModSetVar('xproject', 'display_dates', 0);
    xarModSetVar('xproject', 'display_hours', 0);
    xarModSetVar('xproject', 'display_frequency', 0);
    xarModSetVar('xproject', 'ACCESS_RESTRICTED', 0);
    xarModSetVar('xproject', 'DATEFORMAT', "1");
    xarModSetVar('xproject', 'MAX_DONE', 10);
    xarModSetVar('xproject', 'MOST_IMPORTANT_DAYS', 3);
    xarModSetVar('xproject', 'REFRESH_MAIN', 600);
    xarModSetVar('xproject', 'SEND_MAILS', true);
    xarModSetVar('xproject', 'SHOW_EXTRA_ASTERISK', 1);
    xarModSetVar('xproject', 'SHOW_LINE_NUMBERS', true);
    xarModSetVar('xproject', 'SHOW_PERCENTAGE_IN_TABLE', true);
    xarModSetVar('xproject', 'SHOW_PRIORITY_IN_TABLE', true);
    xarModSetVar('xproject', 'TODO_HEADING', 'Task Management Administration');
    xarModSetVar('xproject', 'VERY_IMPORTANT_DAYS', 3);
    xarModSetVar('xproject', 'ITEMS_PER_PAGE', 20);
    xarModSetVar('xproject', 'prioritymax', 10);

//    xarBlockTypeRegister('xproject', 'first');
//    xarBlockTypeRegister('xproject', 'others');

    return true;
}

function xproject_upgrade($oldversion)
{
    switch($oldversion) {
        case 0.1:
        case '0.1.0':
            break;
        case 1.0:
            break;
        case 2.0:
            break;
    }

    return true;
}

function xproject_delete()
{
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

        include ('includes/xarTableDDL.php');

    $sql = xarDBDropTable($xartable['xproject']);
    if (empty($sql)) return; // throw back

    // Drop the table
    $dbconn->Execute($sql);
    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    xarModAPIFunc('categories', 'admin', 'deletecat', array('cid' => xarModGetVar('xproject', 'mastercid')));
    xarModDelVar('xproject', 'mastercid');
    xarModDelVar('xproject', 'statuslistcid');
    xarModDelVar('xproject', 'projectmastercid');
    xarModDelVar('xproject', 'private');
    xarModDelVar('xproject', 'public');
    xarModDelVar('xproject', 'admin');

    xarModDelVar('xproject', 'display_dates');
    xarModDelVar('xproject', 'display_hours');
    xarModDelVar('xproject', 'display_frequency');
    xarModDelVar('xproject', 'ACCESS_RESTRICTED');
    xarModDelVar('xproject', 'DATEFORMAT');
    xarModDelVar('xproject', 'MAX_DONE');
    xarModDelVar('xproject', 'MOST_IMPORTANT_DAYS');
    xarModDelVar('xproject', 'REFRESH_MAIN');
    xarModDelVar('xproject', 'SEND_MAILS');
    xarModDelVar('xproject', 'SHOW_EXTRA_ASTERISK');
    xarModDelVar('xproject', 'SHOW_LINE_NUMBERS');
    xarModDelVar('xproject', 'SHOW_PERCENTAGE_IN_TABLE');
    xarModDelVar('xproject', 'SHOW_PRIORITY_IN_TABLE');
    xarModDelVar('xproject', 'TODO_HEADING');
    xarModDelVar('xproject', 'VERY_IMPORTANT_DAYS');
    xarModDelVar('xproject', 'ITEMS_PER_PAGE');
        xarModDelVar('xproject', 'prioritymax');

    //xarBlockTypeUnregister('xproject', 'first');
    //xarBlockTypeUnregister('xproject', 'others');

    return true;
}

?>