<?php

/**
 * File: $Id$
 *
 * Initialise the xarpages module.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage example
 * @author Jason Judge 
 */

function xarpages_init()
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $pagestable = $xartable['xarpages_pages'];
    $typestable = $xartable['xarpages_types'];

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
          `xar_theme` varchar(100) default NULL,
          `xar_encode_url` varchar(100) default NULL,
          `xar_decode_url` varchar(100) default NULL,
          `xar_function` varchar(100) default NULL,
          `xar_status` varchar(20) NOT NULL default 'ACTIVE',
          `xar_alias` tinyint(4) NOT NULL default '0',
          PRIMARY KEY  (`xar_pid`)
        ) ;
    */

    $fields = "
        xar_pid             I           AUTO    PRIMARY,
        xar_name            C(100)      NotNull DEFAULT '',
        xar_desc            X           Null,
        xar_itemtype        I           NotNull DEFAULT 0,
        xar_parent          I           NotNull DEFAULT 0,
        xar_left            I           NotNull DEFAULT 0,
        xar_right           I           NotNull DEFAULT 0,
        xar_template        C(100)      Null,
        xar_theme           C(100)      Null,
        xar_encode_url      C(100)      Null,
        xar_decode_url      C(100)      Null,
        xar_function        C(100)      Null,
        xar_status          C(20)       NotNull DEFAULT 'ACTIVE',
        xar_alias           L           NotNull DEFAULT 0
    ";

    // Create or alter the table as necessary.
    $result = $datadict->changeTable($pagestable, $fields);    
    if (!$result) {return;}

    // Create indexes.
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

    /*
        CREATE TABLE `xar_xarpages_types` (
          `xar_ptid` int(11) NOT NULL auto_increment,
          `xar_name` varchar(100) NOT NULL default '',
          `xar_desc` varchar(200) default NULL,
          PRIMARY KEY  (`xar_ptid`)
        ) ;
    */
    
    $fields = "
        xar_ptid            I           AUTO    PRIMARY,
        xar_name            C(100)      NotNull DEFAULT '',
        xar_desc            C(200)      Null
    ";

    // Create or alter the table as necessary.
    $result = $datadict->changeTable($typestable, $fields);    
    if (!$result) {return;}

    // Set up module variables.
    xarModSetVar('xarpages', 'defaultpage', 0);
    xarModSetVar('xarpages', 'errorpage', 0);
    xarModSetVar('xarpages', 'notfoundpage', 0);

    // Switch short URL support on by default, as that is largely
    // the purpose of this module.
    xarModSetVar('xarpages', 'SupportShortURLs', 1);

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
        'ReadPage', 'All', 'xarpages', 'Page', 'All', 'ACCESS_READ',
        xarML('Read or view a page')
    );
    xarRegisterMask(
        'ModeratePage', 'All', 'xarpages', 'Page', 'All', 'ACCESS_MODERATE',
        xarML('Change content of a page')
    );
    xarRegisterMask(
        'EditPage', 'All', 'xarpages', 'Page', 'All', 'ACCESS_EDIT',
        xarML('Move and rename a page')
    );
    xarRegisterMask(
        'AddPage', 'All', 'xarpages', 'Page', 'All', 'ACCESS_ADD',
        xarML('Add new pages')
    );
    xarRegisterMask(
        'DeletePage', 'All', 'xarpages', 'Page', 'All', 'ACCESS_DELETE',
        xarML('Remove pages')
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
        'ModeratePagetype', 'All', 'xarpages', 'Pagetype', 'All', 'ACCESS_MODERATE',
        xarML('Overview of page types')
    );
    // Allow the user to change the description and any hooks on the page type,
    // but not to rename it, delete it or create any new ones.
    xarRegisterMask(
        'EditPagetype', 'All', 'xarpages', 'Pagetype', 'All', 'ACCESS_EDIT',
        xarML('Modify page type description and hooks')
    );
    // Since creation of templates are involved here (each page type requires at least
    // one [default] template), we go straight to admin level to make any changes in that area.
    // This access allows creation, deletion and renaming of page types.
    xarRegisterMask(
        'AdminPagetype', 'All', 'xarpages', 'Pagetype', 'All', 'ACCESS_ADMIN',
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

    // TODO: create blocks

    // Initialisation successful.
    return true;
}

/**
 * Upgrade the example module from an old version.
 */
function xarpages_upgrade($oldversion)
{
    // Upgrade dependent on old version number.
    switch ($oldversion) {
        case '0.1.0':
        default:
            break;
    }

    // Update successful.
    return true;
}

/**
 * Delete (remove) the module.
 */
function xarpages_delete()
{
    // TODO: delete module aliases?
    // TODO: drop tables
    // TODO: remove blocks
    // TODO: delete module variables
    // TODO: drop privileges etc.

    // Deletion successful.
    return true;
}

?>