<?php 
/**
 * File: $Id$
 * 
 * Pubsub Initialise Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.org
 *
 * @subpackage Pubsub Module
 * @author Chris Dudley
*/ 
 
/**
 * initialise the pubsub module
 *
 * @access public
 * @param none
 * @returns bool
 * @raise DATABASE_ERROR
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
        'xar_actionid'=>array('type'=>'integer','size'=>100,'null'=>FALSE,'default'=>'0')
    );
    $query = xarDBCreateTable($pubsubeventstable,$eventsfields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $pubsubregtable = $xartable['pubsub_reg'];
    $regfields = array(
        'xar_pubsubid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_eventid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE),
        'xar_userid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE),
        'xar_actionid'=>array('type'=>'varchar','size'=>100,'null'=>FALSE,'default'=>'0')
    );
    $query = xarDBCreateTable($pubsubregtable,$regfields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $pubsubprocesstable = $xartable['pubsub_process'];
    $processfields = array(
        'xar_handlingid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_pubsubid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE),
        'xar_objectid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE),
        'xar_status'=>array('type'=>'varchar','size'=>100,'null'=>FALSE)
    );
    $query = xarDBCreateTable($pubsubprocesstable,$processfields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $pubsubtemplatetable = $xartable['pubsub_template'];
    $templatefields = array(
        'xar_templateid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_eventid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE),
        'xar_template'=>array('type'=>'text','size'=>'long','null'=>FALSE)
    );
    $query = xarDBCreateTable($pubsubtemplatetable,$templatefields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

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
 * 
 * @access public
 * @param oldversion float "Previous version upgrading from"
 * @returns bool
 * @raise DATABASE_ERROR
 */
function pubsub_upgrade($oldversion)
{
    return true;
}
/**
 * delete the pubsub module
 *
 * @access public
 * @param none
 * @returns bool
 * @raise DATABASE_ERROR
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
    $query = xarDBDropTable($xartable['pubsub_events']);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBDropTable($xartable['pubsub_reg']);

    $result =& $dbconn->Execute($query);
    if (!$result) return;
    
    $query = xarDBDropTable($xartable['pubsub_process']);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBDropTable($xartable['pubsub_template']);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Deletion successful
    return true;
}

?>
