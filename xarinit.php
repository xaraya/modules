<?php
/**
 * File: $Id: s.xarinit.php 1.28 03/07/19 15:58:09+02:00 marcel@hsdev.com $
 *
 * Xaraya Autolinks
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Autolinks Module
 * @author Jim McDonald; Jason Judge
*/

// Some initialisation constants.
define ('AUTOLINKS_PUNCTUATION', '.!?"\';:');

/**
 * initialise the autolinks module
 */
function autolinks_init()
{
    // Need Xaraya version 0.9.1.3 or above for correct exceptions core functions.
    if (!xarModAPIfunc('base', 'versions', 'assert_application', array('0.9.1.3'))) {
        return;
    }
            
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $autolinkstable = $xartable['autolinks'];
    $autolinkstypestable = $xartable['autolinks_types'];

    // Get a data dictionary object with item create methods.
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    // Table didn't exist, create table
    /*****************************************************************
    * CREATE TABLE xar_autolinks (
    * xar_lid int(11) NOT NULL auto_increment,
    * xar_name varchar(100) NOT NULL default '',
    * xar_keyword varchar(200) NOT NULL default '',
    * xar_title varchar(100) default NULL,
    * xar_url varchar(200) NOT NULL default '',
    * xar_comment varchar(200) default NULL,
    * xar_enabled tinyint(4) NOT NULL default '1',
    * xar_match_re tinyint(4) NOT NULL default '0',
    * xar_sample varchar(200) default '',
    * xar_type_tid int(11) NOT NULL default '0',
    * xar_cache_replace text,
    * PRIMARY KEY  (xar_lid),
    * UNIQUE KEY i_xar_autolinks_1 (xar_keyword)
    * );
    *****************************************************************/

    $flds = "
        xar_lid             I           AUTO    PRIMARY,
        xar_name            C(100)      NotNull DEFAULT '',
        xar_keyword         C(200)      NotNull DEFAULT '',
        xar_title           C(100)      Null    DEFAULT '',
        xar_url             C(200)      NotNull DEFAULT '',
        xar_comment         C(200)      Null    DEFAULT '',
        xar_enabled         I1           NotNull DEFAULT 1,
        xar_match_re        I1           NotNull DEFAULT 0,
        xar_sample          C(200)      Null    DEFAULT '',
        xar_type_tid        I           NotNull DEFAULT 0,
        xar_cache_replace   X           Null
    ";

    // Create or alter the table as necessary.
    $result = $datadict->changeTable($autolinkstable, $flds);    
    if (!$result) {return;}

    // Create a unique key on the name column.
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_autolinks_1',
        $autolinkstable,
        'xar_keyword'
    );
    if (!$result) {return;}


    /*****************************************************************
    * CREATE TABLE xar_autolinks_types (
    * xar_tid int(11) NOT NULL auto_increment,
    * xar_type_name varchar(60) NOT NULL default '',
    * xar_template_name varchar(60) NOT NULL default '',
    * xar_dynamic_replace tinyint(4) NOT NULL default '0',
    * xar_link_itemtype int(11) NOT NULL default '0',
    * xar_type_desc longtext,
    * PRIMARY KEY  (xar_tid)
    * ) 
    *****************************************************************/
    $flds = "
        xar_tid             I       AUTO    PRIMARY,
        xar_type_name       C(60)   NotNull DEFAULT '',
        xar_template_name   C(60)   NotNull DEFAULT '',
        xar_dynamic_replace I1       NotNull DEFAULT 0,
        xar_link_itemtype   I       NotNull DEFAULT 0,
        xar_type_desc       X
    ";

    $result = $datadict->changeTable($autolinkstypestable, $flds);    
    if (!$result) {return;}
    

    // Set up module variables
    xarModSetVar('autolinks', 'itemsperpage', 20);
    xarModSetVar('autolinks', 'decoration', '');
    xarModSetVar('autolinks', 'maxlinkcount', '');
    xarModSetVar('autolinks', 'newwindow', 1);
    xarModSetVar('autolinks', 'punctuation', AUTOLINKS_PUNCTUATION);
    xarModSetVar('autolinks', 'excludeelements', 'a code pre');
    xarModSetVar('autolinks', 'nbspiswhite', '1');
    xarModSetVar('autolinks', 'templatebase', 'link');
    xarModSetVar('autolinks', 'showerrors', 0);
    xarModSetVar('autolinks', 'showsamples', 1);
    xarModSetVar('autolinks', 'typeitemtype', 1);

    // Set up module hooks
    if (!xarModRegisterHook(
            'item', 'transform', 'API',
            'autolinks', 'user', 'transform')
    ) {return;}

    $instances = array (
        array (
            'header' => 'Autolink Name:',
            'query' => 'SELECT DISTINCT xar_name FROM ' . $autolinkstable,
            'limit' => 20
        ),
        array (
            'header' => 'Autolink ID:',
            'query' => 'SELECT DISTINCT xar_lid FROM ' . $autolinkstable,
            'limit' => 20
        )
    );

    xarDefineInstance(
        'autolinks', 'Autolinks', $instances, 0, 'All', 'All', 'All',
        xarML('Security instance for autolinks module.')
    );

    // Register Masks
    xarRegisterMask('ReadAutolinks','All','autolinks','All','All','ACCESS_READ');
    xarRegisterMask('EditAutolinks','All','autolinks','All','All','ACCESS_EDIT');
    xarRegisterMask('AddAutolinks','All','autolinks','All','All','ACCESS_ADD');
    xarRegisterMask('DeleteAutolinks','All','autolinks','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminAutolinks','All','autolinks','All','All','ACCESS_ADMIN');

    // Create or update sample data.
    //$result = xarModAPIfunc(
    //    'autolinks', 'admin', 'samples',
    //    array('action'=>'create')
    //);

    // Enable dynamic data hooks for autolinks
    if (xarModIsAvailable('dynamicdata')) {
        xarModAPIFunc('modules', 'admin', 'enablehooks',
            array('callerModName' => 'autolinks', 'hookModName' => 'dynamicdata')
        );
    }

    // Initialisation successful
    return true;
}

/**
 * upgrade the autolinks module from an older version
 */
function autolinks_upgrade($oldversion)
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    $autolinkstable = $xartable['autolinks'];
    $autolinkstypestable = $xartable['autolinks_types'];

    // Upgrade dependent on old version number
    switch ($oldversion) {

        // TODO: version numbers - normalise.
        case '1.0':
            // Changes to upgrade from 1.0 to 1.1

            // Convert some module variables to the new versions.
            // If only first link is selected, then set link count to one.
            if (xarModGetVar('autolinks', 'linkfirst') == 1) {
                xarModSetVar('autolinks', 'maxlinkcount', '1');
            } else {
                xarModSetVar('autolinks', 'maxlinkcount', '');
            }

            // If underlining is disabled then set the link style to 'none'.
            if (xarModGetVar('autolinks', 'invisilinks') == 1) {
                xarModSetVar('autolinks', 'decoration', 'none');
            } else {
                xarModSetVar('autolinks', 'decoration', '');
            }

            // Remove old module variables.
            xarModDelVar('autolinks', 'linkfirst');
            xarModDelVar('autolinks', 'invisilinks');

        case '1.1':
            // Changes to upgrade from 1.0 to 1.1

            // Add columns to the Autolinks table.
            $flds = "
                xar_enabled         I1           NotNull DEFAULT 1
            ";

            // Until we have a better method of handling errors, it is safer to continue.
            $result = $datadict->changeTable($autolinkstable, $flds);    
            if (!$result) {xarErrorHandled();}

        case '1.2':
            // Changes to upgrade from 1.2 to 1.3

            // Add columns to the Autolinks table.
            $flds = "
                xar_match_re        I1           NotNull DEFAULT 0,
                xar_sample          C(200)      Null    DEFAULT '',
                xar_cache_replace   X           Null
            ";

            // Until we have a better method of handling errors, it is safer to continue.
            $result = $datadict->changeTable($autolinkstable, $flds);    
            if (!$result) {xarErrorHandled();}
            
            xarModSetVar('autolinks', 'punctuation', AUTOLINKS_PUNCTUATION);
            xarModSetVar('autolinks', 'nbspiswhite', '1');

        case '1.3':
            // Changes to upgrade from 1.3 to 1.4
            // Main changes are:
            // - introduce autolink type
            // - drop and alter some autolink table columns
            // - set up some sample data

            // Need Xaraya version 0.9.1.3 or above for correct exceptions core functions.
            if (!xarModAPIfunc('base', 'versions', 'assert_application', array('0.9.1.3'))) {
                return;
            }
            
            // We are using the ADOdb Data Dictionary objects here. This is 
            // experimental, and they will be wrapped by Xaraya functions in the future.
            // Note: this does NOT maintain xar_tables meta-data.

            // Add/alter columns on autolinks table.

            $flds = "
                xar_type_tid    I       NotNull     DEFAULT 0,
                xar_name        C(100),
                xar_title       C(100)              DEFAULT NULL,
                xar_comment     C(200)              DEFAULT NULL,
                xar_keyword     C(200)  NotNull,
                xar_cache_replace X
            ";

            // TODO: need some serious error handling. But where to go in the
            // event of an error for now? Better probably to just blindly go
            // through to the end, for now.
            $sqlarray = $datadict->ChangeTable($autolinkstable, $flds);

            // Copy keyword to name column.
            $query = 'UPDATE ' . $autolinkstable
                . ' SET xar_name = xar_keyword'
                . ' WHERE xar_name IS NULL OR xar_name = \'\'';

            $result =& $dbconn->Execute($query);
            if (xarCurrentErrorType() <> XAR_NO_EXCEPTION) {
                xarErrorHandled();
            }

            // Make the name column mandatory. Must do this after executing the
            // other changes otherwise the data dictionary creates am 'add column'.
            $flds = "xar_name        C(100)   NotNull";
            $result = $datadict->ChangeTable($autolinkstable, $flds);
            if (!$result) {return;}

            // Create a unique index for the autolinks name.
            // TODO?

            // Create autolinks types table.
            
            // Now update the table using the data dictionary.
            $flds = "
                xar_tid             I       AUTO    PRIMARY,
                xar_type_name       C(60)   NotNull,
                xar_template_name   C(60)   NotNull,
                xar_dynamic_replace I1       NotNull DEFAULT 0,
                xar_link_itemtype   I       NotNull DEFAULT 0,
                xar_type_desc       X
            ";

            $result = $datadict->ChangeTable($autolinkstypestable, $flds);

            if (!$result) {return;}

            xarModSetVar('autolinks', 'templatebase', 'link');
            xarModSetVar('autolinks', 'showerrors', 0);
            xarModSetVar('autolinks', 'showsamples', 1);

        case '1.4':
        case '1.5':
            // Changes to upgrade from 1.4 or 1.5 to 1.6
            // There was an error in the orignal 1.4 to 1.5 upgrade.

            // New itemtype for the autolink types themselves.
            $typeitemtype = xarModGetVar('autolinks', 'typeitemtype');
            if (empty($typeitemtype)) {
                xarModSetVar('autolinks', 'typeitemtype', 1);
            }

            // The security instance 'keyword' has changed to 'name'. Only
            // need to update the query and the header.
            // There are no APIs for updating security instances, so this is
            // just a bit of a hack.
            $sitePrefix = xarDBGetSiteTablePrefix();
            $query = 'update ' . $sitePrefix . '_security_instances'
                . ' set xar_header = \'Autolink Name:\','
                . ' xar_query = \'SELECT DISTINCT xar_name FROM ' . $autolinkstable . '\''
                . ' where xar_module = \'autolinks\''
                . ' and xar_header = \'Autolink Keyword:\''
                . ' and xar_component = \'Autolinks\'';
            
            $result =& $dbconn->Execute($query);
            if (xarCurrentErrorType() <> XAR_NO_EXCEPTION) {
                xarErrorHandled();
            }
            
       case '1.6':
            // compatability upgrade     
       case '1.6.0':
            // The current version.

            // Create or update sample data.
            //$result = xarModAPIfunc(
            //    'autolinks', 'admin', 'samples',
            //    array('action'=>'create')
            //);

            xarModSetVar('autolinks', 'excludeelements', 'a code pre');

            return true;
    }

    // Enable dynamic data hooks for autolinks
    if (xarModIsAvailable('dynamicdata')) {
        xarModAPIFunc('modules', 'admin', 'enablehooks',
            array('callerModName' => 'autolinks', 'hookModName' => 'dynamicdata')
        );
    }

    // Should never reach this point.
    return false;
}

/**
 * Delete the Autolinks module
 */
function autolinks_delete()
{
    // Remove module hooks
    if (!xarModUnregisterHook(
        'item', 'transform', 'API',
        'autolinks', 'user', 'transform')
    ) {return;}

    // Drop the tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    $autolinkstable = $xartable['autolinks'];
    $result = $datadict->dropTable($autolinkstable);
    //if (!$result) {return;}

    $autolinkstypestable = $xartable['autolinks_types'];
    $result = $datadict->dropTable($autolinkstypestable);
    //if (!$result) {return;}

    // Remove module variables
    // TODO: 'removeall'?
    xarModDelVar('autolinks', 'itemsperpage');
    xarModDelVar('autolinks', 'maxlinkcount');
    xarModDelVar('autolinks', 'decoration');
    xarModDelVar('autolinks', 'punctuation');
    xarModDelVar('autolinks', 'nbspiswhite');
    xarModDelVar('autolinks', 'templatebase');
    xarModDelVar('autolinks', 'showerrors');
    xarModDelVar('autolinks', 'showsamples');
    xarModDelVar('autolinks', 'typeitemtype');
    xarModDelVar('autolinks', 'excludeelements');

    // Remove Masks and Instances
    xarRemoveMasks('autolinks');
    xarRemoveInstances('autolinks');

    // Deletion successful
    return true;
}

?>
