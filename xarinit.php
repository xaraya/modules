<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
    sys::import('xaraya.structures.query');
    $xartable =& xarDB::getTables();

    $q = new Query();
    $prefix = xarDB::getPrefix();

    $query = "DROP TABLE IF EXISTS " . $prefix . "_pubsub_events";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_pubsub_events (
            eventid             integer unsigned NOT NULL auto_increment,
            modid               integer unsigned NOT NULL DEFAULT '0',
            itemtype            integer unsigned NOT NULL DEFAULT '0',
            cid                 integer unsigned NOT NULL DEFAULT '0',
            extra               varchar(255) NOT NULL DEFAULT '',
            groupdescr          varchar(64) NOT NULL DEFAULT '',
            PRIMARY KEY(eventid)
            )";
    if (!$q->run($query)) return;
    
    $query = "DROP TABLE IF EXISTS " . $prefix . "_pubsub_reg";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_pubsub_reg (
            pubsubid             integer unsigned NOT NULL auto_increment,
            eventid               integer unsigned NOT NULL DEFAULT '0',
            userid            integer unsigned NOT NULL DEFAULT '0',
            actionid                 integer unsigned NOT NULL DEFAULT '0',
            subdate                 integer unsigned NOT NULL DEFAULT '0',
            email               varchar(255) NOT NULL DEFAULT '',
            PRIMARY KEY(pubsubid)
            )";
    if (!$q->run($query)) return;
    
    $query = "DROP TABLE IF EXISTS " . $prefix . "_pubsub_process";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_pubsub_process (
            handlingid             integer unsigned NOT NULL auto_increment,
            pubsubid               integer unsigned NOT NULL DEFAULT '0',
            objectid            integer unsigned NOT NULL DEFAULT '0',
            templateid                 integer unsigned NOT NULL DEFAULT '0',
            status               varchar(100) NOT NULL DEFAULT '',
            PRIMARY KEY(handlingid)
            )";
    if (!$q->run($query)) return;
    
    $query = "DROP TABLE IF EXISTS " . $prefix . "_pubsub_templates";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_pubsub_templates (
            templateid             integer unsigned NOT NULL auto_increment,
            name          varchar(64) NOT NULL DEFAULT '',
            template               text,
            compiled            text,
            PRIMARY KEY(templateid),
            KEY templatename (name)
            )";
    if (!$q->run($query)) return;
    
/*    $nextId = $dbconn->GenId($pubsubtemplatestable);
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
    if (!$result) return; */
/*
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
*/
// TODO: review this :-)

/*    // Define instances for this module
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
    xarDefineInstance('pubsub','Item',$instances);*/

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
 * @throws DATABASE_ERROR
 */
function pubsub_upgrade($oldversion)
{
    switch ($oldversion) {
        case '2.0.0':
            // We can now use local templates in the pubsub/xartemplates dir
            xarModSetVar('pubsub','usetemplateids',1);
        default:
            break;
    }

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
{/*
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
*/
    $module = 'pubsub';
    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => $module));
}

?>
