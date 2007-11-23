<?php
/**
 * Pubsub module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Pubsub Module
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */
/**
 * initialise the pubsub module
 *
 * @access public
 * @param none
 * @return bool
 * @throws DATABASE_ERROR
 */
function pubsub_init()
{
    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $prefix = xarDBGetSiteTablePrefix();

    xarDBLoadTableMaintenanceAPI();

    // Create tables
    $pubsubeventstable = $xartable['pubsub_events'];
    $eventsfields = array(
        'xar_eventid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_modid'=>array('type'=>'integer','null'=>FALSE),
        'xar_itemtype'=>array('type'=>'integer','null'=>FALSE),
        'xar_cid'=>array('type'=>'integer','null'=>FALSE),
    // TODO: support other types of grouping later on
        'xar_extra'=>array('type'=>'varchar','size'=>254,'null'=>FALSE,'default'=>''),
        'xar_groupdescr'=>array('type'=>'varchar','size'=>64,'null'=>FALSE,'default'=>'')
    );
    $query = xarDBCreateTable($pubsubeventstable,$eventsfields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $pubsubregtable = $xartable['pubsub_reg'];
    $regfields = array(
        'xar_pubsubid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_eventid'=>array('type'=>'integer','null'=>FALSE),
        'xar_userid'=>array('type'=>'integer','null'=>FALSE),
        'xar_actionid'=>array('type'=>'varchar','size'=>100,'null'=>FALSE,'default'=>'0'),
        'xar_subdate'=>array('type'=>'integer','null'=>FALSE, 'default'=>'0'),
        'xar_email'=>array('type'=>'varchar','size'=>255,'null'=>TRUE, 'default'=>'')
    );
    $query = xarDBCreateTable($pubsubregtable,$regfields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $pubsubprocesstable = $xartable['pubsub_process'];
    $processfields = array(
        'xar_handlingid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_pubsubid'=>array('type'=>'integer','null'=>FALSE),
        'xar_objectid'=>array('type'=>'integer','null'=>FALSE),
        'xar_templateid'=>array('type'=>'integer','null'=>FALSE),
        'xar_status'=>array('type'=>'varchar','size'=>100,'null'=>FALSE)
    );
    $query = xarDBCreateTable($pubsubprocesstable,$processfields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $pubsubtemplatestable = $xartable['pubsub_templates'];
    $templatesfields = array(
        'xar_templateid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_name'=>array('type'=>'varchar','size'=>64,'null'=>FALSE,'default'=>''),
        'xar_template'=>array('type'=>'text'),
        'xar_compiled'=>array('type'=>'text')
    );
    $query = xarDBCreateTable($pubsubtemplatestable,$templatesfields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // template names must be unique (to avoid confusion)
    $index = array(
        'name'      => 'i_' . $prefix . '_pubsub_templatename',
        'fields'    => array('xar_name'),
        'unique'    => true
    );
    $query = xarDBCreateIndex($pubsubtemplatestable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $nextId = $dbconn->GenId($pubsubtemplatestable);
    $name = 'default';
    $template = '<xar:ml>
<xar:mlstring>A new item #(1) was created in module #(2).<br/>
Use the following link to view it : <a href="#(3)">#(4)</a></xar:mlstring>
<xar:mlvar>#$itemid#</xar:mlvar>
<xar:mlvar>#$module#</xar:mlvar>
<xar:mlvar>#$link#</xar:mlvar>
<xar:mlvar>#$title#</xar:mlvar>
</xar:ml>';
    // compile the template now
    $compiled = xarTplCompileString($template);


    $query = "INSERT INTO $pubsubtemplatestable (xar_templateid, xar_name, xar_template, xar_compiled)
              VALUES (?,?,?,?)";
    $bindvars=array($nextId, $name, $template, $compiled);
    $result =& $dbconn->Execute($query,$bindvars);
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
                           'update',
                           'API',
                           'pubsub',
                           'admin',
                           'updatehook')) {
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
// used by categories only (for now)
    if (!xarModRegisterHook('item',
                           'display',
                           'GUI',
                           'pubsub',
                           'user',
                           'displayicon')) {
        return false;
    }

// used by roles only
    if (!xarModRegisterHook('item',
                           'usermenu',
                           'GUI',
                           'pubsub',
                           'user',
                           'usermenu')) {
        return false;
    }

// TODO: review this :-)

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
    return pubsub_upgrade('1.5.0');
}

/**
 * upgrade the pubsub module from an old version
 *
 * @access public
 * @param oldversion float "Previous version upgrading from"
 * @returns bool
 * @throws DATABASE_ERROR
 */
function pubsub_upgrade($oldversion)
{
    xarDBLoadTableMaintenanceAPI();

    switch ($oldversion) {
        case '1.0':
            $dbconn =& xarDBGetConn();
            $prefix = xarDBGetSiteTablePrefix();

            $xarTables =& xarDBGetTables();
            $pubsubregtable = $xarTables['pubsub_reg'];
            $pubsubtemplatetable = $prefix.'_pubsub_template';

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

        case 1.1:
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $prefix = xarDBGetSiteTablePrefix();

            $pubsubtemplatestable = $xartable['pubsub_templates'];
            $templatesfields = array(
                'xar_templateid'=>array('type'=>'integer','null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
                'xar_name'=>array('type'=>'varchar','size'=>64,'null'=>FALSE,'default'=>''),
                'xar_template'=>array('type'=>'text'),
                'xar_compiled'=>array('type'=>'text')
            );
            $query = xarDBCreateTable($pubsubtemplatestable,$templatesfields);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // template names must be unique (to avoid confusion)
            $index = array(
                'name'      => 'i_' . $prefix . '_pubsub_templatename',
                'fields'    => array('xar_name'),
                'unique'    => true
            );
            $query = xarDBCreateIndex($pubsubtemplatestable,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            $name = 'default';
            $template = '<xar:ml>
<xar:mlstring>A new item #(1) was created in module #(2).<br/>
Use the following link to view it : <a href="#(3)">#(4)</a></xar:mlstring>
<xar:mlvar>#$itemid#</xar:mlvar>
<xar:mlvar>#$module#</xar:mlvar>
<xar:mlvar>#$link#</xar:mlvar>
<xar:mlvar>#$title#</xar:mlvar>
</xar:ml>';
            // compile the template now
            $compiled = xarTplCompileString($template);
            $nextId = $dbconn->GenId($pubsubtemplatestable);

            $query = "INSERT INTO $pubsubtemplatestable (xar_templateid, xar_name, xar_template, xar_compiled)
                      VALUES (?, ?, ?, ?)";
            $bindvars=array($nextId, $name, $template, $compiled);
            $result =& $dbconn->Execute($query,$bindvars);
            if (!$result) return;

            // fall through to the next upgrade

        case 1.2:
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $prefix = xarDBGetSiteTablePrefix();

            $query = xarDBDropTable($xartable['pubsub_eventcids']);
            if (empty($query)) return; // throw back

            // Drop the table and send exception if returns false.
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            // Add xar_cid to the events table
            $query = xarDBAlterTable($xartable['pubsub_events'],
                                     array('command' => 'add',
                                           'field' => 'xar_cid',
                                           'type' => 'integer',
                                           'null' => false));
            $result = &$dbconn->Execute($query);
            if (!$result) return;

        // TODO: support other types of grouping later on
            // Add xar_extra to the events table
            $query = xarDBAlterTable($xartable['pubsub_events'],
                                     array('command' => 'add',
                                           'field' => 'xar_extra',
                                           'type' => 'varchar',
                                           'size' => 254,
                                           'null' => false,
                                           'default' => ''));
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            // Add xar_templateid to the process table
            $query = xarDBAlterTable($xartable['pubsub_process'],
                                     array('command' => 'add',
                                           'field' => 'xar_templateid',
                                           'type' => 'integer',
                                           'null' => false));
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            // Remove the delsubscriptions hook
            if (!xarModUnregisterHook('item',
                                      'delete',
                                      'API',
                                      'pubsub',
                                      'user',
                                      'delsubscriptions')) {
                return false;
            }

            // Add the update hook
            if (!xarModRegisterHook('item',
                                    'update',
                                    'API',
                                    'pubsub',
                                    'admin',
                                    'updatehook')) {
                return false;
            }

        // used by roles only
            if (!xarModRegisterHook('item',
                                    'usermenu',
                                    'GUI',
                                    'pubsub',
                                    'user',
                                    'usermenu')) {
                return false;
            }

            // Let's start over with all this events stuff, shall we ?
            $query = "DELETE FROM $xartable[pubsub_events]";
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            // Let's start over with all this registration stuff, shall we ?
            $query = "DELETE FROM $xartable[pubsub_reg]";
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            // Let's start over with all this processing stuff, shall we ?
            $query = "DELETE FROM $xartable[pubsub_process]";
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            // You also need to go through Configure Hooks for pubsub again, to refresh the hooklist

            // fall through to the next upgrade
        case '1.3':
            $modversion['user'] = 0;
        case '1.4.0':
            $dbconn =& xarDBGetConn();
            $prefix = xarDBGetSiteTablePrefix();

            $xarTables =& xarDBGetTables();
            $pubsubregtable = $xarTables['pubsub_reg'];

            // Add a column to the register table
            $query = xarDBAlterTable($pubsubregtable,
                                     array('command' => 'add',
                                           'field' => 'xar_email',
                                           'type' => 'varchar',
                                           'size' => 255,
                                           'null' => TRUE,
                                           'default' => ''));

            $result = &$dbconn->Execute($query);
            if (!$result) return;
        case '1.5.0':
            // We can now use local templates in the pubsub/xartemplates dir
            xarModSetVar('pubsub','usetemplateids',1);
        default:
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
 * @throws DATABASE_ERROR
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
    if (!xarModUnregisterHook('item',
                           'update',
                           'API',
                           'pubsub',
                           'admin',
                           'updatehook')) {
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
    if (!xarModUnregisterHook('item',
                           'display',
                           'GUI',
                           'pubsub',
                           'user',
                           'displayicon')) {
        xarSessionSetVar('errormsg', xarML('Could not unregister hook for Pubsub module'));
    }
    if (!xarModUnregisterHook('item',
                           'usermenu',
                           'GUI',
                           'pubsub',
                           'user',
                           'usermenu')) {
        xarSessionSetVar('errormsg', xarML('Could not unregister hook for Pubsub module'));
    }

    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    //Load Table Maintainance API
    xarDBLoadTableMaintenanceAPI();

    // Generate the SQL to drop the table using the API
    $query = xarDBDropTable($xartable['pubsub_events']);
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

    $query = xarDBDropTable($xartable['pubsub_templates']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Deletion successful
    return true;
}

?>
