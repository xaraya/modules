<?php
/**
 * File: $Id$
 *
 * Pubsub Initialise Module
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
        'xar_modid'=>array('type'=>'integer','null'=>FALSE),
        'xar_itemtype'=>array('type'=>'integer','null'=>FALSE),
        'xar_groupdescr'=>array('type'=>'varchar','size'=>64,'null'=>FALSE,'default'=>'')
    );
    $query = xarDBCreateTable($pubsubeventstable,$eventsfields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $pubsubeventcidstable = $xartable['pubsub_eventcids'];
    $eventcidsfields = array(
        'xar_eid'=>array('type'=>'integer','null'=>FALSE,'primary_key'=>TRUE),
        'xar_cid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_flag'=>array('type'=>'integer','null'=>FALSE,'default'=>'0')
    );
    $query = xarDBCreateTable($pubsubeventcidstable,$eventcidsfields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $pubsubregtable = $xartable['pubsub_reg'];
    $regfields = array(
        'xar_pubsubid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_eventid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE),
        'xar_userid'=>array('type'=>'integer','size'=>'medium','null'=>FALSE),
        'xar_actionid'=>array('type'=>'varchar','size'=>100,'null'=>FALSE,'default'=>'0'),
        'xar_subdate'=>array('type'=>'integer','null'=>FALSE, 'default'=>'0')
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

    // Set up module hooks
    if (!xarModRegisterHook('item',
                           'create',
                           'API',
                           'pubsub',
                           'admin',
                           'createhook')) {
        return false;
    }
    if (!xarModRegisterHook('item',
                           'delete',
                           'API',
                           'pubsub',
                           'admin',
                           'deletehook')) {
        return false;
    }
    #if (!xarModRegisterHook('item',
    #                       'create',
    #                       'API',
    #                       'pubsub',
    #                       'user',
    #                       'subscribe')) {
    #   return false;
    #}
    #if (!xarModRegisterHook('item',
    #                       'delete',
    #                       'API',
    #                       'pubsub',
    #                       'user',
    #                       'unsubscribe')) {
    #    return false;
    #}
    if (!xarModRegisterHook('item',
                           'delete',
                           'API',
                           'pubsub',
                           'user',
                           'delsubscriptons')) {
        return false;
    }
    if (!xarModRegisterHook('item',
                           'display',
                           'GUI',
                           'pubsub',
                           'user',
                           'displayicon')) {
        return false;
    }

    // Define instances for this module
    $query1 = "SELECT DISTINCT xar_pubsubid FROM " . $pubsubregtable;
    $query2 = "SELECT DISTINCT xar_eventid FROM " . $pubsubeventstable;
    $query3 = "SELECT DISTINCT xar_handlingid FROM " . $pubsubprocesstable;

    $instances = array(
                        array('header' => 'Pubsub ID:',
                                'query' => $query1,
                                'limit' => 20
                            ),
                        array('header' => 'Event ID:',
                                'query' => $query2,
                                'limit' => 20
                            ),
                        array('header' => 'Handling ID:',
                                'query' => $query3,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('pubsub','Item',$instances);

    // Define mask definitions for security checks
    xarRegisterMask('OverviewPubSub','All','pubsub','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadPubSub','All','pubsub','All','All','ACCESS_READ');
    xarRegisterMask('EditPubSub','All','pubsub','All','All','ACCESS_EDIT');
    xarRegisterMask('AddPubSub','All','pubsub','All','All','ACCESS_ADD');
    xarRegisterMask('DeletePubSub','All','pubsub','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminPubSub','All','pubsub','All','All','ACCESS_ADMIN');

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
	switch ($oldversion) {
		case '1.0':
		    list($dbconn) = xarDBGetConn();
		    $prefix = xarDBGetSiteTablePrefix();
		    
		    $xarTables = xarDBGetTables();
		    $pubsubregtable = $xarTables['pubsub_reg'];
			$pubsubtemplatetable = $prefix.'_pubsub_template';

            xarDBLoadTableMaintenanceAPI();

			// Drop the template table
            $query = xarDBDropTable($pubsubtemplatetable);
            $result =& $dbconn->Execute($query);
			
			// Add a column to the register table
            $query = xarDBAlterTable($pubsubregtable,
                                     array('command' => 'add',
                                           'field' => 'xar_subdate',
                                           'type' => 'integer',
                                           'null' => false));
            $result = &$dbconn->Execute($query);
            if (!$result) return;

			$sql = "UPDATE $pubsubregtable
					   SET xar_subdate = ".time()."";
		    $result =& $dbconn->Execute($sql);
		    if (!$result) return;
		    break;
	} // END switch

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
                           'createhook')) {
        xarSessionSetVar('errormsg', xarML('Could not unregister hook for Pubsub module'));
    }
    #if (!xarModUnregisterHook('item',
    #                       'create',
    #                       'API',
    #                       'pubsub',
    #                       'user',
    #                       'subscribe')) {
    #    xarSessionSetVar('errormsg', xarML('Could not unregister hook for Pubsub module'));
    #}
    if (!xarModUnregisterHook('item',
                           'display',
                           'GUI',
                           'pubsub',
                           'user',
                           'displayicon')) {
        xarSessionSetVar('errormsg', xarML('Could not unregister hook for Pubsub module'));
    }
    #if (!xarModUnregisterHook('item',
    #                       'delete',
    #                       'API',
    #                       'pubsub',
    #                       'user',
    #                       'unsubscribe')) {
    #    xarSessionSetVar('errormsg', xarML('Could not unregister hook for Pubsub module'));
    #}
    if (!xarModUnregisterHook('item',
                           'delete',
                           'API',
                           'pubsub',
                           'user',
                           'delsubscriptions')) {
        xarSessionSetVar('errormsg', xarML('Could not unregister hook for Pubsub module'));
    }
    if (!xarModUnregisterHook('item',
                           'delete',
                           'API',
                           'pubsub',
                           'admin',
                           'deletehook')) {
        xarSessionSetVar('errormsg', xarML('Could not unregister hook for Pubsub module'));
    }

    // Get database information
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    //Load Table Maintainance API
    xarDBLoadTableMaintenanceAPI();

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['pubsub_events']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBDropTable($xartable['pubsub_eventcids']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBDropTable($xartable['pubsub_reg']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = xarDBDropTable($xartable['pubsub_process']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Deletion successful
    return true;
}

?>
