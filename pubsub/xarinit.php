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
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    
    // Create tables
    $pubsubeventstable = $xartable['pubsub_events'];
    $eventsfields = array(
        'xar_eventid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_module'=>array('type'=>'varchar','size'=>32,'null'=>FALSE),
        'xar_eventtype'=>array('type'=>'varchar','size'=>64,'null'=>FALSE),
        'xar_groupdescr'=>array('type'=>'varchar','size'=>64,'null'=>FALSE),
        'xar_actionid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>'0')
    );
    $sql = xarDBCreateTable($pubsubeventstable,$eventsfields);
    if (empty($sql)) return;
    $dbconn->Execute($sql);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
   }

    $pubsubregtable = $xartable['pubsub_reg'];
    $regfields = array(
        'xar_pubsubid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_eventid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE),
        'xar_users'=>array('type'=>'text','size'=>'medium','null'=>FALSE),
        'xar_actionid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE,'default'=>'0')
    );
    $sql = xarDBCreateTable($pubsubregtable,$regfields);
    if (empty($sql)) return;
    $dbconn->Execute($sql);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $pubsubprocesstable = $xartable['pubsub_process'];
    $processfields = array(
        'xar_handlingid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_pubsubid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE),
        'xar_objectid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE),
        'xar_status'=>array('type'=>'varchar','size'=>100,'null'=>FALSE)
    );
    $sql = xarDBCreateTable($pubsubprocesstable,$processfields);
    if (empty($sql)) return;
    $dbconn->Execute($sql);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $pubsubtemplatetable = $xartable['pubsub_template'];
    $templatefields = array(
        'xar_templateid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_eventid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE),
        'xar_template'=>array('type'=>'text','size'=>'long','null'=>FALSE)
    );
    $sql = xarDBCreateTable($pubsubtemplatetable,$templatefields);
    if (empty($sql)) return;
    $dbconn->Execute($sql);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    // Set up module hooks
    if (!xarModRegisterHook('item',
                           'create',
                           'API',
                           'pubsub',
                           'admin',
                           'addevent')) {
        return false;
    }
    if (!xarModRegisterHook('item',
                           'delete',
                           'API',
                           'pubsub',
                           'admin',
                           'delevent')) {
        return false;
    }
    if (!xarModRegisterHook('item',
                           'create',
                           'API',
                           'pubsub',
                           'user',
                           'subscribe')) {
        return false;
    }
    if (!xarModRegisterHook('item',
                           'delete',
                           'API',
                           'pubsub',
                           'user',
                           'unsubscribe')) {
        return false;
    }
    if (!xarModRegisterHook('category',
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
    if (!xarModUnregisterHook('item',
                           'create',
                           'API',
                           'pubsub',
                           'admin',
                           'addevent')) {
        xarSessionSetVar('errormsg', _PUBSUBSCOULDNOTUNREGISTER);
    }
    if (!xarModUnregisterHook('item',
                           'create',
                           'API',
                           'pubsub',
                           'user',
                           'adduser')) {
        xarSessionSetVar('errormsg', _PUBSUBSCOULDNOTUNREGISTER);
    }
    if (!xarModUnregisterHook('category',
                           'display',
                           'GUI',
                           'pubsub',
                           'user',
                           'display')) {
        xarSessionSetVar('errormsg', _PUBSUBSCOULDNOTUNREGISTER);
    }
    if (!xarModUnregisterHook('item',
                           'delete',
                           'API',
                           'pubsub',
                           'user',
                           'deluser')) {
        xarSessionSetVar('errormsg', _PUBSUBSCOULDNOTUNREGISTER);
    }
    if (!xarModUnregisterHook('item',
                           'delete',
                           'API',
                           'pubsub',
                           'admin',
                           'delevent')) {
        xarSessionSetVar('errormsg', _PUBSUBSCOULDNOTUNREGISTER);
    }

    // Get database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    include ('includes/xarTableDDL.php');

    // Generate the SQL to drop the table using the API
    $sql = xarDBDropTable($xartable['pubsub_events']);
    if (empty($sql)) return; // throw back

    // Drop the table
    $dbconn->Execute($sql);
    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $sql = xarDBDropTable($xartable['pubsub_reg']);
    if (empty($sql)) return; // throw back

    // Drop the table
    $dbconn->Execute($sql);
    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $sql = xarDBDropTable($xartable['pubsub_process']);
    if (empty($sql)) return; // throw back

    // Drop the table
    $dbconn->Execute($sql);
    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $sql = xarDBDropTable($xartable['pubsub_template']);
    if (empty($sql)) return; // throw back

    // Drop the table
    $dbconn->Execute($sql);
    // Check for an error with the database code, and if so raise the
    // appropriate exception
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarMLByKey('DATABASE_ERROR', $query);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // Deletion successful
    return true;
}

?>
