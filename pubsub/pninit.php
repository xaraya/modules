<?php 
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------

/**
 * initialise the pubsub module
 */
function pubsub_init()
{
    // Get database information
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    pnDBLoadTableMaintenanceAPI();
    
    // Create tables
    $pubsubeventstable = $pntable['pubsub_events'];
    $eventsfields = array(
        'pn_eventid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'pn_module'=>array('type'=>'varchar','size'=>32,'null'=>FALSE),
        'pn_eventtype'=>array('type'=>'varchar','size'=>64,'null'=>FALSE),
        'pn_groupdescr'=>array('type'=>'varchar','size'=>64,'null'=>FALSE),
        'pn_actionid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>'0')
    );
    $sql = pnDBCreateTable($pubsubeventstable,$eventsfields);
    if (empty($sql)) return;
    $dbconn->Execute($sql);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        $msg = pnMLByKey('DATABASE_ERROR', $query);
        pnExceptionSet(PN_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
   }

    $pubsubregtable = $pntable['pubsub_reg'];
    $regfields = array(
        'pn_pubsubid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'pn_eventid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE),
        'pn_users'=>array('type'=>'text','size'=>'medium','null'=>FALSE),
        'pn_actionid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>'0')
    );
    $sql = pnDBCreateTable($pubsubregtable,$regfields);
    if (empty($sql)) return;
    $dbconn->Execute($sql);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        $msg = pnMLByKey('DATABASE_ERROR', $query);
        pnExceptionSet(PN_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $pubsubprocesstable = $pntable['pubsub_process'];
    $processfields = array(
        'pn_handlingid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'pn_pubsubid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE),
        'pn_objectid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE),
        'pn_status'=>array('type'=>'varchar','size'=>100,'null'=>FALSE)
    );
    $sql = pnDBCreateTable($pubsubprocesstable,$processfields);
    if (empty($sql)) return;
    $dbconn->Execute($sql);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        $msg = pnMLByKey('DATABASE_ERROR', $query);
        pnExceptionSet(PN_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $pubsubtemplatetable = $pntable['pubsub_template'];
    $templatefields = array(
        'pn_templateid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'pn_eventid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE),
        'pn_template'=>array('type'=>'text','size'=>'long','null'=>FALSE)
    );
    $sql = pnDBCreateTable($pubsubtemplatetable,$templatefields);
    if (empty($sql)) return;
    $dbconn->Execute($sql);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        $msg = pnMLByKey('DATABASE_ERROR', $query);
        pnExceptionSet(PN_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    // Set up module hooks
    if (!pnModRegisterHook('item',
                           'create',
                           'API',
                           'pubsub',
                           'admin',
                           'addevent')) {
        return false;
    }
    if (!pnModRegisterHook('item',
                           'delete',
                           'API',
                           'pubsub',
                           'admin',
                           'delevent')) {
        return false;
    }
    if (!pnModRegisterHook('item',
                           'create',
                           'API',
                           'pubsub',
                           'user',
                           'subscribe')) {
        return false;
    }
    if (!pnModRegisterHook('item',
                           'delete',
                           'API',
                           'pubsub',
                           'user',
                           'unsubscribe')) {
        return false;
    }
    if (!pnModRegisterHook('category',
                           'display',
                          'GUI',
                           'pubsub',
                           'user',
                           'display')) {
        return false;
    }

    // Initialisation successful
    return true;
}

/**
 * upgrade the pubsub module from an old version
 */
function pubsub_upgrade($oldversion)
{
    return true;
}
/**
 * delete the pubsub module
 */
function pubsub_delete()
{
    // Remove module hooks
    if (!pnModUnregisterHook('item',
                           'create',
                           'API',
                           'pubsub',
                           'admin',
                           'addevent')) {
        pnSessionSetVar('errormsg', _PUBSUBSCOULDNOTUNREGISTER);
    }
    if (!pnModUnregisterHook('item',
                           'create',
                           'API',
                           'pubsub',
                           'user',
                           'adduser')) {
        pnSessionSetVar('errormsg', _PUBSUBSCOULDNOTUNREGISTER);
    }
    if (!pnModUnregisterHook('category',
                           'display',
                           'GUI',
                           'pubsub',
                           'user',
                           'display')) {
        pnSessionSetVar('errormsg', _PUBSUBSCOULDNOTUNREGISTER);
    }
    if (!pnModUnregisterHook('item',
                           'delete',
                           'API',
                           'pubsub',
                           'user',
                           'deluser')) {
        pnSessionSetVar('errormsg', _PUBSUBSCOULDNOTUNREGISTER);
    }
    if (!pnModUnregisterHook('item',
                           'delete',
                           'API',
                           'pubsub',
                           'admin',
                           'delevent')) {
        pnSessionSetVar('errormsg', _PUBSUBSCOULDNOTUNREGISTER);
    }

    // Get database information
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();
    include ('includes/pnTableDDL.php');

    // Generate the SQL to drop the table using the API
    $sql = pnDBDropTable($pntable['pubsub_events']);
    if (empty($sql)) return; // throw back

    // Drop the table
    $dbconn->Execute($sql);
    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = pnMLByKey('DATABASE_ERROR', $query);
        pnExceptionSet(PN_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $sql = pnDBDropTable($pntable['pubsub_reg']);
    if (empty($sql)) return; // throw back

    // Drop the table
    $dbconn->Execute($sql);
    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = pnMLByKey('DATABASE_ERROR', $query);
        pnExceptionSet(PN_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $sql = pnDBDropTable($pntable['pubsub_process']);
    if (empty($sql)) return; // throw back

    // Drop the table
    $dbconn->Execute($sql);
    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = pnMLByKey('DATABASE_ERROR', $query);
        pnExceptionSet(PN_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $sql = pnDBDropTable($pntable['pubsub_template']);
    if (empty($sql)) return; // throw back

    // Drop the table
    $dbconn->Execute($sql);
    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = pnMLByKey('DATABASE_ERROR', $query);
        pnExceptionSet(PN_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // Deletion successful
    return true;
}

?>
