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
 * @author Marc Lutolf <mfl@netspan.ch>
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
            id                  integer unsigned NOT NULL auto_increment,
            name                varchar(64) NOT NULL DEFAULT '',
            object_id           integer unsigned NOT NULL DEFAULT '0',
            module_id           integer unsigned NOT NULL DEFAULT '0',
            itemtype            integer unsigned NOT NULL DEFAULT '0',
            cid                 integer unsigned NOT NULL DEFAULT '0',
            event_type          varchar(64) NOT NULL DEFAULT '',
            extra               varchar(255) NOT NULL DEFAULT '',
            groupdescr          varchar(64) NOT NULL DEFAULT '',
            author              integer unsigned NOT NULL default 0, 
            time_created        integer unsigned NOT NULL default 0, 
            time_modified       integer unsigned NOT NULL default 0, 
            state               tinyint(3) NOT NULL default 3, 
            PRIMARY KEY(id)
            )";
    if (!$q->run($query)) return;
    
    $query = "DROP TABLE IF EXISTS " . $prefix . "_pubsub_subscriptions";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_pubsub_subscriptions (
            id                  integer unsigned NOT NULL auto_increment,
            event_id            integer unsigned NOT NULL DEFAULT '0',
            groupid             integer unsigned NOT NULL DEFAULT '0',
            userid              integer unsigned NOT NULL DEFAULT '0',
            action_id           integer unsigned NOT NULL DEFAULT '0',
            subdate             integer unsigned NOT NULL DEFAULT '0',
            email               varchar(255) NOT NULL DEFAULT '',
            author              integer unsigned NOT NULL default 0, 
            time_created        integer unsigned NOT NULL default 0, 
            time_modified       integer unsigned NOT NULL default 0, 
            state               tinyint(3) NOT NULL default 3, 
            PRIMARY KEY(id)
            )";
    if (!$q->run($query)) return;
    
    $query = "DROP TABLE IF EXISTS " . $prefix . "_pubsub_process";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_pubsub_process (
            id                  integer unsigned NOT NULL auto_increment,
            event_id            integer unsigned NOT NULL DEFAULT '0',
            object_id           integer unsigned NOT NULL DEFAULT '0',
            module_id           integer unsigned NOT NULL default 0, 
            itemtype            integer unsigned NOT NULL default 0, 
            itemid              integer unsigned NOT NULL default 0, 
            url                 varchar(255) NOT NULL DEFAULT '',
            template_id         integer unsigned NOT NULL DEFAULT '0',
            author              integer unsigned NOT NULL default 0, 
            time_created        integer unsigned NOT NULL default 0, 
            time_modified       integer unsigned NOT NULL default 0, 
            state               tinyint(3) NOT NULL default 3, 
            PRIMARY KEY(id)
            )";
    if (!$q->run($query)) return;
    
    $query = "DROP TABLE IF EXISTS " . $prefix . "_pubsub_templates";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_pubsub_templates (
            id                  integer unsigned NOT NULL auto_increment,
            name                varchar(64) NOT NULL DEFAULT '',
            description         text,
            object_id           integer unsigned NOT NULL default 0, 
            module_id           integer unsigned NOT NULL default 0, 
            itemtype            integer unsigned NOT NULL default 0, 
            author              integer unsigned NOT NULL default 0, 
            time_created        integer unsigned NOT NULL default 0, 
            time_modified       integer unsigned NOT NULL default 0, 
            state               tinyint(3) NOT NULL default 3, 
            PRIMARY KEY(id),
            KEY templatename (name)
            )";
    if (!$q->run($query)) return;
    
# --------------------------------------------------------
#
# Create DD objects
#
    $module = 'pubsub';
    $objects = array(
                    'pubsub_events',
                    'pubsub_subscriptions',
                    'pubsub_templates',
                    'pubsub_process',
                     );

    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

# --------------------------------------------------------
#
# Default data for other modules
#    
    // Add basic mailer templates
    if (xarMod::isAvailable('mailer')) {
        $dat_file = sys::code() . 'modules/' . $module . '/xardata/'.'mailer_templates-dat.xml';
        if(file_exists($dat_file)) {
            $data['file'] = $dat_file;
            $objectid = xarMod::apiFunc('dynamicdata','util','import', $data);
        }
    }

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


    $query = "INSERT INTO $pubsubtemplatestable (id, name, template, compiled)
              VALUES (?,?,?,?)";
    $bindvars=array($nextId, $name, $template, $compiled);
    $result = $dbconn->Execute($query,$bindvars);
    if (!$result) return; */
/*
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
    $query1 = "SELECT DISTINCT pubsubid FROM " . $pubsubsubscriptionstable;
    $query2 = "SELECT DISTINCT eventid FROM " . $pubsubeventstable;
    $query3 = "SELECT DISTINCT id FROM " . $pubsubprocesstable;

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
    xarRegisterMask('ManagePubSub','All','pubsub','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminPubSub','All','pubsub','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up modvars
    #
        xarModVars::set('pubsub', 'enable_default_template',0);
        xarModVars::set('pubsub', 'recognized_events','itemcreate,itemupdate,itemdelete');

    # --------------------------------------------------------
    #
    # Set up hooks
    #
    xarHooks::registerObserver('ItemCreate', 'pubsub');
    xarHooks::registerObserver('ItemUpdate', 'pubsub');
    xarHooks::registerObserver('ItemDelete', 'pubsub');

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
            xarModVars::set('pubsub','usetemplateids',1);
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
    // Remove any mailer templates
    sys::import('xaraya.structures.query');
    xarMod:apiLoad('mailer');
    $tables =& xarDB::getTables();
    $q = new Query('DELETE', $tables['mailer_mails']);
    $q->eq('module_id', xarMod::getRegid($module));
    $q->run();
    
    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => $module));
}

?>