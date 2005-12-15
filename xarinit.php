<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
/**
 * initialise the xproject module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function xproject_init()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

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
    xarModSetVar('xproject', 'dateformat', 1);
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

    xarModSetVar('xproject', 'SupportShortURLs', 0);
    /* If you provide short URL encoding functions you might want to also
     * provide module aliases and have them set in the module's administration.
     * Use the standard module var names for useModuleAlias and aliasname.
     */
    xarModSetVar('xproject', 'useModuleAlias',false);
    xarModSetVar('xproject','aliasname','');

//    xarBlockTypeRegister('xproject', 'first');
//    xarBlockTypeRegister('xproject', 'others');

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

   // Tasks and projects
    xarRegisterMask('ViewXProject', 'All', 'xproject', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadXProject', 'All', 'xproject', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditXProject', 'All', 'xproject', 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddXProject', 'All', 'xproject', 'Item', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteXProject', 'All', 'xproject', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminXProject', 'All', 'xproject', 'Item', 'All:All:All', 'ACCESS_ADMIN');
   // Groups
    xarRegisterMask('ViewXProject', 'All', 'xproject', 'Group', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadXProject', 'All', 'xproject', 'Group', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditXProject', 'All', 'xproject', 'Group', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddXProject', 'All', 'xproject', 'Group', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteXProject', 'All', 'xproject', 'Group', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminXProject', 'All', 'xproject', 'Group', 'All:All:All', 'ACCESS_ADMIN');
    return true;
}

function xproject_upgrade($oldversion)
{
    switch($oldversion) {

        case '0.1.0':
            xarModSetVar('xproject', 'SupportShortURLs', 0);
            /* If you provide short URL encoding functions you might want to also
             * provide module aliases and have them set in the module's administration.
             * Use the standard module var names for useModuleAlias and aliasname.
             */
            xarModSetVar('xproject', 'useModuleAlias',false);
            xarModSetVar('xproject','aliasname','');
            /**
             * Register the module components that are privileges objects
             * Format is
             * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
             */

            xarRegisterMask('ViewXProject', 'All', 'xproject', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
            xarRegisterMask('ReadXProject', 'All', 'xproject', 'Item', 'All:All:All', 'ACCESS_READ');
            xarRegisterMask('EditXProject', 'All', 'xproject', 'Item', 'All:All:All', 'ACCESS_EDIT');
            xarRegisterMask('AddXProject', 'All', 'xproject', 'Item', 'All:All:All', 'ACCESS_ADD');
            xarRegisterMask('DeleteXProject', 'All', 'xproject', 'Item', 'All:All:All', 'ACCESS_DELETE');
            xarRegisterMask('AdminXProject', 'All', 'xproject', 'Item', 'All:All:All', 'ACCESS_ADMIN');
            return xproject_upgrade('0.1.1');
        case '0.1.1':
           // Groups
            xarRegisterMask('ViewXProject', 'All', 'xproject', 'Group', 'All:All:All', 'ACCESS_OVERVIEW');
            xarRegisterMask('ReadXProject', 'All', 'xproject', 'Group', 'All:All:All', 'ACCESS_READ');
            xarRegisterMask('EditXProject', 'All', 'xproject', 'Group', 'All:All:All', 'ACCESS_EDIT');
            xarRegisterMask('AddXProject', 'All', 'xproject', 'Group', 'All:All:All', 'ACCESS_ADD');
            xarRegisterMask('DeleteXProject', 'All', 'xproject', 'Group', 'All:All:All', 'ACCESS_DELETE');
            xarRegisterMask('AdminXProject', 'All', 'xproject', 'Group', 'All:All:All', 'ACCESS_ADMIN');
            break;

    }

    return true;
}

function xproject_delete()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
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
     /* Remove any module aliases before deleting module vars */
    /* Assumes one module alias in this case */
    $aliasname =xarModGetVar('xproject','aliasname');
    $isalias = xarModGetAlias($aliasname);
    if (isset($isalias) && ($isalias =='xproject')){
        xarModDelAlias($aliasname,'xproject');
    }

    xarModAPIFunc('categories', 'admin', 'deletecat', array('cid' => xarModGetVar('xproject', 'mastercid')));
    /* Delete any module variables */
    xarModDelAllVars('xproject');

    //xarBlockTypeUnregister('xproject', 'first');
    //xarBlockTypeUnregister('xproject', 'others');

    /* Remove Masks and Instances
     * these functions remove all the registered masks and instances of a module
     * from the database. This is not strictly necessary, but it's good housekeeping.
     */
    xarRemoveMasks('xproject');
    xarRemoveInstances('xproject');

    return true;
}

?>