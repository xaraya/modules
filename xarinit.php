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
function ievents_init()
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $module = 'ievents';
    $eventstable = $xartable[$module . '_events'];
    $calendarstable = $xartable[$module . '_calendars'];

    // Get a data dictionary object with item create methods.
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    /*
        CREATE TABLE `xar_xarpages_pages` (
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
        ) ;
    */
/*
    $fields = "
        xar_pid             I           AUTO    PRIMARY,
        xar_name            C(100)      NotNull DEFAULT '',
        xar_desc            X           Null,
        xar_itemtype        I           NotNull DEFAULT 0,
        xar_parent          I           NotNull DEFAULT 0,
        xar_left            I           NotNull DEFAULT 0,
        xar_right           I           NotNull DEFAULT 0,
        xar_template        C(100)      Null,
        xar_page_template   C(100)      Null,
        xar_theme           C(100)      Null,
        xar_encode_url      C(100)      Null,
        xar_decode_url      C(100)      Null,
        xar_function        C(100)      Null,
        xar_status          C(20)       NotNull DEFAULT 'ACTIVE',
        xar_alias           I1          NotNull DEFAULT 0
    ";
*/
    // Create or alter the table as necessary.
/*
    $result = $datadict->changeTable($eventsstable, $fields);
    if (!$result) {return;}
*/
    // Create indexes.
/*
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_xarpages_page_left',
        $pagestable,
        'xar_left'
    );
    if (!$result) {return;}

    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_xarpages_page_name',
        $pagestable,
        'xar_name'
    );
    if (!$result) {return;}

    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_xarpages_page_type',
        $pagestable,
        'xar_itemtype'
    );
    if (!$result) {return;}
*/
    /*
        CREATE TABLE `xar_xarpages_types` (
          `xar_ptid` int(11) NOT NULL auto_increment,
          `xar_name` varchar(100) NOT NULL default '',
          `xar_desc` varchar(200) default NULL,
          PRIMARY KEY  (`xar_ptid`)
        ) ;
    */
/*
    $fields = "
        xar_ptid            I           AUTO    PRIMARY,
        xar_name            C(100)      NotNull DEFAULT '',
        xar_desc            C(200)      Null
    ";
*/
    // Create or alter the table as necessary.
/*
    $result = $datadict->changeTable($typestable, $fields);
    if (!$result) {return;}
*/
    // The page type name must be unique.
/*
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_xarpages_type_name',
        $typestable,
        'xar_name',
        array('UNIQUE' => true)
    );
    if (!$result) {return;}
*/

    // Set up module variables.
/*
    xarModSetVar('xarpages', 'defaultpage', 0);
    xarModSetVar('xarpages', 'errorpage', 0);
    xarModSetVar('xarpages', 'notfoundpage', 0);
    xarModSetVar('xarpages', 'noprivspage', 0);
    xarModSetVar('xarpages', 'shortestpath', 1);
    xarModSetVar('xarpages', 'transformref', 1);
    xarModSetVar('xarpages', 'transformfields', 'body');
*/
    // Switch short URL support on by default.
    xarModSetVar($module, 'SupportShortURLs', 1);

    // Privileges.

    // Set up component 'IEvent'.

    $comp = 'IEvent';
    $instances = array (
        array (
            'header' => 'Calendar ID',
            'query' => 'SELECT cid FROM ' . $calendarstable . ' ORDER BY cid',
            'limit' => 50
        ),
        array (
            'header' => 'Event ID',
            'query' => 'SELECT DISTINCT eid FROM ' . $eventstable . ' ORDER BY eid',
            'limit' => 50
        ),
    );
    // This function is a misnomer. It actually defines a _component_
    xarDefineInstance(
        $module, $comp, $instances, 0, 'All', 'All', 'All',
        xarML('Security component for #(1) #(2)', $module, $comp)
    );

    // Masks for the component 'IEvent'.
    // Each mask defines something the user is able to do.
    // The masks are linked to the instances at runtime when security checks
    // are made:
    // xarSecurityCheck($mask, $showException, $component, $instance, $module, ...)
    // xarRegisterMask($name, $realm, $module, $component, $instance, $level, $description='')
    xarRegisterMask(
        'Overview' . $comp, 'All', $module, $comp, 'All', 'ACCESS_OVERVIEW',
        xarML('Overview of an event')
    );
    xarRegisterMask(
        'Read' . $comp, 'All', $module, $comp, 'All', 'ACCESS_READ',
        xarML('View an event')
    );
    xarRegisterMask(
        'Comment' . $comp, 'All', $module, $comp, 'All', 'ACCESS_COMMENT',
        xarML('Submit an event')
    );
    xarRegisterMask(
        'Moderate' . $comp, 'All', $module, $comp, 'All', 'ACCESS_MODERATE',
        xarML('Change status of an event')
    );
    xarRegisterMask(
        'Edit' . $comp, 'All', $module, $comp, 'All', 'ACCESS_EDIT',
        xarML('Move and rename an event')
    );
    xarRegisterMask(
        'Delete' . $comp, 'All', $module, $comp, 'All', 'ACCESS_DELETE',
        xarML('Add and remove events')
    );
    xarRegisterMask(
        'Admin' . $comp, 'All', $module, $comp, 'All', 'ACCESS_ADMIN',
        xarML('Administer the module')
    );

    // Set up component 'IEventCal'.
    $comp = 'IEventCal';
    $instances = array (
        array (
            'header' => 'Calendar',
            'query' => 'SELECT cid FROM ' . $calendarstable . ' ORDER BY cid',
            'limit' => 50
        )
    );
    xarDefineInstance(
        $module, $comp, $instances, 0, 'All', 'All', 'All',
        xarML('Security component for #(1) #(2)', $module, $comp)
    );

    // Masks for the component 'IEventCal'.
    xarRegisterMask(
        'Edit' . $comp, 'All', $module, $comp, 'All', 'ACCESS_EDIT',
        xarML('Edit details for a calendar')
    );
    xarRegisterMask(
        'Delete' . $comp, 'All', $module, $comp, 'All', 'ACCESS_DELETE',
        xarML('Add and remove calendars')
    );
    xarRegisterMask(
        'Admin' . $comp, 'All', $module, $comp, 'All', 'ACCESS_ADMIN',
        xarML('Administer module')
    );

    // TODO: Create some default types and DD objects.
    // NOTE: This would probably best be done via an import after
    // the module is installed.

    // Switch on all hooks from DD.
    if (xarModIsAvailable('dynamicdata')) {
        xarModAPIFunc('modules', 'admin', 'enablehooks',
            array('callerModName' => $module, 'hookModName' => 'dynamicdata')
        );
    }


    // Register block types.
/*
    foreach(array('menu', 'crumb') as $blocktype) {
        if (!xarModAPIFunc(
            'blocks', 'admin', 'register_block_type',
            array(
                'modName' => $module,
                'blockType'=> $blocktype
            )
        )) return;
    }
*/

    // Set up module hooks
/*
    if (!xarModRegisterHook(
            'item', 'transform', 'API',
            $module, 'user', 'transformhook')
    ) {return;}
*/
    // Initialisation successful.
    return true;
}

/**
 * Upgrade the xarpages module from an old version.
 *
 * @param string oldversion
 * @return bool true on success
 */
function ievents_upgrade($oldversion)
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $module = 'ievents';

    $eventstable = $xartable[$module . '_events'];
    $typestable = $xartable[$module . '_calendars'];

    // Get a data dictionary object with item create methods.
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    // Upgrade dependent on old version number.
/*
    switch ($oldversion) {
        case '0.1.0':
            // Upgrading from 0.1.0
            // Check these indexes exist before attempting to
            // drop and/or create them.

            // Get a list of indexes for the pages table.
            $indexes = $datadict->getIndexes($pagestable);

            // Drop an erroneous unique index and recreate it non-unique.
            $indexname = 'i_' . xarDBGetSiteTablePrefix() . '_xarpages_page_type';
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
            $indexname = 'i_' . xarDBGetSiteTablePrefix() . '_xarpages_type_name';
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
                . ' WHERE xar_module = ? AND xar_name = ?';

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

            foreach($masks as $old_mask => $new_mask) {
                // Update the mask.
                // TODO: not sure what affect this has cross-realm.
                $dbconn->execute($query_masks, array($new_mask, 'xarpages', $old_mask));
            }

        case '0.2.3':
        case '0.2.4':
            // Upgrade from 0.2.3 or 0.2.4 to 0.2.5
            xarModSetVar('xarpages', 'shortestpath', 1);

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
            xarModSetVar('xarpages', 'transformfields', 'body');
            xarModSetVar('xarpages', 'transformref', 1);

        break;
    }
*/
    // Update successful.
    return true;
}

/**
 * Delete (remove) the xarpages module.
 * @return bool true on success
 */
function ievents_delete()
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $module = 'ievents';
    $eventstable = $xartable[$module . '_events'];
    $calendarstable = $xartable[$module . '_calendars'];

    // Get a data dictionary object with item create methods.
/*
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
*/
    // TODO: delete module aliases.
    // Probably have to loop through all pages and check whether they
    // are module aliases to drop. This should be done in the core.

    // Drop tables
/*
    $result = $datadict->dropTable($pagestable);
    $result = $datadict->dropTable($typestable);
*/
    // TODO: remove blocks

    // Delete module variables
    xarModDelAllVars($module);

    // Drop privileges.
    xarRemoveMasks($module);
    xarRemoveInstances($module);

    // Deletion successful.
    return true;
}

?>