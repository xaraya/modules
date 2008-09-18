<?php
/**
 * Initialise the xarpages module.
 *
 * @package modules
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarpages
 * @author Jason Judge
 */

/**
 * Initialise the xarpages module
 * @return bool
 */
function xarpages_init()
{
    // Set up database tables
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $prefix = xarDB::getPrefix();
    $pagestable = $prefix . "_xarpages_pages";
    $typestable = $prefix . "_xarpages_types";

    $query = "DROP TABLE IF EXISTS " . $pagestable;
    $dbconn->Execute($query);
    $query = "CREATE TABLE " . $pagestable ." (
          `xar_pid` int(11) NOT NULL auto_increment,
          `xar_name` varchar(100) NOT NULL default '',
          `xar_desc` text,
          `xar_itemtype` int(11) NOT NULL default '0',
          `xar_parent` int(11) NOT NULL default '0',
          `xar_left` int(11) NOT NULL default '0',
          `xar_right` int(11) NOT NULL default '0',
          `xar_template` varchar(100) default NULL,
          `xar_page_template` varchar(100) default NULL,
          `xar_theme` varchar(100) default NULL,
          `xar_encode_url` varchar(100) default NULL,
          `xar_decode_url` varchar(100) default NULL,
          `xar_function` varchar(100) default NULL,
          `xar_status` varchar(20) NOT NULL default 'ACTIVE',
          `xar_alias` tinyint(4) NOT NULL default '0',
          PRIMARY KEY  (`xar_pid`)
        )" ;
    $result = $dbconn->Execute($query);
    if (!$result) {return;}

   sys::import('xaraya.tableddl');
    $index = array('name' => 'i_' . $prefix . '_xarpages_page_left',
                   'fields' => array('xar_left')
                   );
    $query = xarDBCreateIndex($pagestable, $index);
    $result = $dbconn->Execute($query);
    if (!$result) {return;}

    $index = array('name' => 'i_' . $prefix . '_xarpages_page_name',
                   'fields' => array('xar_name')
    );
    $query = xarDBCreateIndex($pagestable, $index);
    $result = $dbconn->Execute($query);
    if (!$result) {return;}

    $index = array('name' => 'i_' . $prefix . '_xarpages_page_type',
                   'fields' => array('xar_itemtype')
    );
    $query = xarDBCreateIndex($pagestable, $index);
    $result = $dbconn->Execute($query);
    if (!$result) {return;}

/*    $result = $datadict->createIndex(
        'i_' . xarDB::getPrefix() . '_xarpages_page_type',
        $pagestable,
        'xar_itemtype'
    );
    if (!$result) {return;}
*/

    $query = "DROP TABLE IF EXISTS " . $typestable;
    $dbconn->Execute($query);
    $query = "CREATE TABLE " . $typestable ." (
          `xar_ptid` int(11) NOT NULL auto_increment,
          `xar_name` varchar(100) NOT NULL default '',
          `xar_desc` varchar(200) default NULL,
          PRIMARY KEY  (`xar_ptid`)
        )" ;
    $result = $dbconn->Execute($query);
    if (!$result) {return;}

    $index = array('name' => 'i_' . $prefix . '_xarpages_type_name',
                   'fields' => array('xar_name'),
                   'unique' => true
                   );
    $query = xarDBCreateIndex($typestable, $index);
    $result = $dbconn->Execute($query);
    if (!$result) {return;}

/*    // The page type name must be unique.
    $result = $datadict->createIndex(
        'i_' . xarDB::getPrefix() . '_xarpages_type_name',
        $typestable,
        'xar_name',
        array('UNIQUE' => true)
    );
    if (!$result) {return;}
*/

    // Set up module variables.
    xarModVars::set('xarpages', 'defaultpage', 0);
    xarModVars::set('xarpages', 'errorpage', 0);
    xarModVars::set('xarpages', 'notfoundpage', 0);
    xarModVars::set('xarpages', 'noprivspage', 0);
    xarModVars::set('xarpages', 'shortestpath', 1);
    xarModVars::set('xarpages', 'transformref', 1);
    xarModVars::set('xarpages', 'transformfields', 'body');

    // Switch short URL support on by default, as that is largely
    // the purpose of this module.
    xarModVars::set('xarpages', 'SupportShortURLs', 1);

    // Privileges.

    // Set up component 'Page'.
    // Each page will have a page name and a unique page type name, i.e. two instances
    // that can define a specific page or group of pages.
    // The page names are not unique, but it is up to the administrator to decide how
    // to handle that.
    // We are not supporting IDs to identify pages or page types - the name alone
    // will do. With the correct permissions, a user will not be allowed to rename a
    // page, so that should not be a problem.
    // Note page names beginning with '@' are system pages - not editable by a user
    // with any permissions.
    $instances = array (
        array (
            'header' => 'Page Name',
            'query' => 'SELECT DISTINCT xar_name FROM ' . $pagestable . ' ORDER BY xar_left',
            'limit' => 50
        ),
        array (
            'header' => 'Page Type',
            'query' => 'SELECT xar_name FROM ' . $typestable . ' WHERE xar_name NOT LIKE \'@%\' ORDER BY xar_name',
            'limit' => 50
        )
    );
    xarDefineInstance(
        'xarpages', 'Page', $instances, 0, 'All', 'All', 'All',
        xarML('Security component for xarpages page')
    );

    // Masks for the component 'Page'.
    // Each mask defines something the user is able to do.
    // The masks are linked to the instances at runtime when security checks
    // are made:
    // xarSecurityCheck($mask, $showException, $component, $instance, $module, ...)
    // xarRegisterMask($name, $realm, $module, $component, $instance, $level, $description='')
    xarRegisterMask(
        'ViewXarpagesPage', 'All', 'xarpages', 'Page', 'All', 'ACCESS_OVERVIEW',
        xarML('See that a page exists')
    );
    xarRegisterMask(
        'ReadXarpagesPage', 'All', 'xarpages', 'Page', 'All', 'ACCESS_READ',
        xarML('Read or view a page')
    );
    xarRegisterMask(
        'ModerateXarpagesPage', 'All', 'xarpages', 'Page', 'All', 'ACCESS_MODERATE',
        xarML('Change content of a page')
    );
    xarRegisterMask(
        'EditXarpagesPage', 'All', 'xarpages', 'Page', 'All', 'ACCESS_EDIT',
        xarML('Move and rename a page')
    );
    xarRegisterMask(
        'AddXarpagesPage', 'All', 'xarpages', 'Page', 'All', 'ACCESS_ADD',
        xarML('Add new pages')
    );
    xarRegisterMask(
        'DeleteXarpagesPage', 'All', 'xarpages', 'Page', 'All', 'ACCESS_DELETE',
        xarML('Remove pages')
    );
    xarRegisterMask(
        'AdminXarpagesPage', 'All', 'xarpages', 'Page', 'All', 'ACCESS_ADMIN',
        xarML('Administer the module')
    );

    // Set up component 'Pagetype'.
    // Each pagetype a unique page name.
    $instances = array (
        array (
            'header' => 'Page Type',
            'query' => 'SELECT xar_name FROM ' . $typestable . ' WHERE xar_name NOT LIKE \'@%\' ORDER BY xar_name',
            'limit' => 50
        )
    );
    xarDefineInstance(
        'xarpages', 'Pagetype', $instances, 0, 'All', 'All', 'All',
        xarML('Security component for xarpages page type')
    );

    // Masks for the component 'Page'.
    // Each mask defines something the user is able to do.
    // The masks are linked to the instances at runtime when security checks
    // are made:
    // xarSecurityCheck($mask, $showException, $component, $instance, $module, ...)
    // xarRegisterMask($name, $realm, $module, $component, $instance, $level, $description='')

    // Allow the user to view the page types that are available.
    xarRegisterMask(
        'ModerateXarpagesPagetype', 'All', 'xarpages', 'Pagetype', 'All', 'ACCESS_MODERATE',
        xarML('Overview of page types')
    );
    // Allow the user to change the description and any hooks on the page type,
    // but not to rename it, delete it or create any new ones.
    xarRegisterMask(
        'EditXarpagesPagetype', 'All', 'xarpages', 'Pagetype', 'All', 'ACCESS_EDIT',
        xarML('Modify page type description and hooks')
    );
    // Since creation of templates are involved here (each page type requires at least
    // one [default] template), we go straight to admin level to make any changes in that area.
    // This access allows creation, deletion and renaming of page types.
    xarRegisterMask(
        'AdminXarpagesPagetype', 'All', 'xarpages', 'Pagetype', 'All', 'ACCESS_ADMIN',
        xarML('Administer page types')
    );

    // TODO: Create some default types and DD objects.
    // NOTE: This would probably best be done via an import after
    // the module is installed.

    // Switch on all hooks from DD.
    if (xarModIsAvailable('dynamicdata')) {
        xarModAPIFunc('modules', 'admin', 'enablehooks',
            array('callerModName' => 'xarpages', 'hookModName' => 'dynamicdata')
        );
    }

    // Create the 'pagetype' page. This provides us with the itemtype
    // for pagetypes. NOTE: this is now done the first time a page
    // type is created.

    // Register block types.
    foreach(array('menu', 'crumb') as $blocktype) {
        if (!xarModAPIFunc(
            'blocks', 'admin', 'register_block_type',
            array(
                'modName' => 'xarpages',
                'blockType'=> $blocktype
            )
        )) return;
    }

    // Set up module hooks
    if (!xarModRegisterHook(
            'item', 'transform', 'API',
            'xarpages', 'user', 'transformhook')
    ) {return;}

    // Initialisation successful.
    return true;
}

/**
 * Upgrade the xarpages module from an old version.
 *
 * @param string oldversion
 * @return bool true on success
 */
function xarpages_upgrade($oldversion)
{
    // Set up database tables
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $pagestable = $xartable['xarpages_pages'];
    $typestable = $xartable['xarpages_types'];

    // Get a data dictionary object with item create methods.
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    // Upgrade dependent on old version number.
    switch ($oldversion) {
        case '0.1.0':
            // Upgrading from 0.1.0
            // Check these indexes exist before attempting to
            // drop and/or create them.

            // Get a list of indexes for the pages table.
            $indexes = $datadict->getIndexes($pagestable);

            // Drop an erroneous unique index and recreate it non-unique.
            $indexname = 'i_' . xarDB::getPrefix() . '_xarpages_page_type';
            if (isset($indexes[$indexname])) {
                $result = $datadict->dropIndex($indexname, $pagestable);
            }

            // Create the non-unique index of the same name.
            $result = $datadict->createIndex($indexname, $pagestable, 'xar_itemtype');
            if (!$result) {return;}

            // Create a new index.

            // Get a list of indexes for the page types table.
            $indexes = $datadict->getIndexes($typestable);

            // The page type name must be unique.
            $indexname = 'i_' . xarDB::getPrefix() . '_xarpages_type_name';
            if (!isset($indexes[$indexname])) {
                $result = $datadict->createIndex(
                    $indexname, $typestable, 'xar_name', array('UNIQUE' => true)
                );
                if (!$result) {return;}
            }

        case '0.1.1':
            // Upgrading from 0.1.1
            // An extra page property is introduced in 0.1.2

            $result = $datadict->ChangeTable(
                $pagestable, 'xar_page_template C(100) Null'
            );
            if (!$result) {return;}

        case '0.1.2':
            // Upgrading from 0.1.2
            // Register a 'menu' block type.

            // Register block types.
            if (!xarModAPIFunc(
                'blocks', 'admin', 'register_block_type',
                array(
                    'modName' => 'xarpages',
                    'blockType'=> 'menu'
                )
            )) return;

        case '0.2.1':
        case '0.2.2':
            // Upgrading from 0.2.1 or 0.2.2 to 0.2.3
            // This upgrade concerns the renaming of the privilege masks.
            // The masks are renamed directly in the privilege tables in the
            // absence of an API to do the job.

            // Update the sucurity masks table.
            $query_masks = 'UPDATE ' . $xartable['security_masks']
                . ' SET xar_name = ?'
                . ' WHERE xar_modid = ? AND xar_name = ?';

            // Loop for each mask to change.
            $masks = array(
                'ReadPage' => 'ReadXarpagesPage',
                'ModeratePage' => 'ModerateXarpagesPage',
                'EditPage' => 'EditXarpagesPage',
                'AddPage' => 'AddXarpagesPage',
                'DeletePage' => 'DeleteXarpagesPage',
                'AdminPage' => 'AdminXarpagesPage',
                'ModeratePagetype' => 'ModerateXarpagesPagetype',
                'EditPagetype' => 'EditXarpagesPagetype',
                'AdminPagetype' => 'AdminXarpagesPagetype'
            );

            $info = xarMod::getBaseInfo('roles');
            $sysid = $info['systemid'];
            foreach($masks as $old_mask => $new_mask) {
                // Update the mask.
                // TODO: not sure what affect this has cross-realm.
                $dbconn->execute($query_masks, array($new_mask, $sysid, $old_mask));
            }

        case '0.2.3':
        case '0.2.4':
            // Upgrade from 0.2.3 or 0.2.4 to 0.2.5
            xarModVars::set('xarpages', 'shortestpath', 1);

        case '0.2.5':
            // Upgrade to 0.2.6 - new crumbtrail block added.

            // Register block types.
            if (!xarModAPIFunc(
                'blocks', 'admin', 'register_block_type',
                array(
                    'modName' => 'xarpages',
                    'blockType'=> 'crumb'
                )
            )) return;

        case '0.2.6':
            // Upgrade to 0.2.7 - new transform hook.

            // Set up module hooks
            if (!xarModRegisterHook(
                    'item', 'transform', 'API',
                    'xarpages', 'user', 'transformhook')
            ) {return;}

            // New module variables.
            xarModVars::set('xarpages', 'transformfields', 'body');
            xarModVars::set('xarpages', 'transformref', 1);

        case '0.2.7':
            // Upgrade to 0.2.8 - new overview privilege on a page.
            xarRegisterMask(
                'ViewXarpagesPage', 'All', 'xarpages', 'Page', 'All', 'ACCESS_OVERVIEW',
                xarML('See that a page exists')
            );

        break;
    }

    // Update successful.
    return true;
}

/**
 * Delete (remove) the xarpages module.
 * @return bool true on success
 */
function xarpages_delete()
{
    return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => 'xarpages'));

    // Set up database tables
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $pagestable = $xartable['xarpages_pages'];
    $typestable = $xartable['xarpages_types'];

    // Get a data dictionary object with item create methods.
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    // TODO: delete module aliases.
    // Probably have to loop through all pages and check whether they
    // are module aliases to drop. This should be done in the core.

    // Drop tables
    $result = $datadict->dropTable($pagestable);
    $result = $datadict->dropTable($typestable);

    // TODO: remove blocks

    // Delete module variables
    xarModVars::delete_all('xarpages');

    // Drop privileges.
    xarRemoveMasks('xarpages');
    xarRemoveInstances('xarpages');

    // Deletion successful.
    return true;
}

?>
