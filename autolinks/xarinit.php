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

//Load Table Maintainance API
xarDBLoadTableMaintenanceAPI();

// Some initialisation constants.

define ('AUTOLINKS_PUNCTUATION', '.!?"\';:');

/**
 * initialise the autolinks module
 */
function autolinks_init()
{
    // Need Xaraya version 0.9.1 or above for correct ADOdb version and
    // to allow APIs to be called while the module is being installed and
    // upgraded.
    if (!xarModAPIfunc('base', 'versions', 'assert_application', array('0.9.1'))) {
        return;
    }
            
    // Set up database tables
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $autolinkstable = $xartable['autolinks'];
    $autolinkstypestable = $xartable['autolinks_types'];

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
    $fields = array (
    'xar_lid'           => array ('type'=>'integer', 'null'=>false, 'increment'=>true, 'primary_key'=>true),
    'xar_name'          => array ('type'=>'varchar', 'size'=>100, 'null'=>false, 'default'=>''),
    'xar_keyword'       => array ('type'=>'varchar', 'size'=>200, 'null'=>false, 'default'=>''),
    'xar_title'         => array ('type'=>'varchar', 'size'=>100, 'null'=>true, 'default'=>''),
    'xar_url'           => array ('type'=>'varchar', 'size'=>200, 'null'=>false, 'default'=>''),
    'xar_comment'       => array ('type'=>'varchar', 'size'=>200, 'null'=>true, 'default'=>''),
    'xar_enabled'       => array ('type'=>'integer', 'size'=>'tiny', 'null'=>false, 'default'=>'1'),
    'xar_match_re'      => array ('type'=>'integer', 'size'=>'tiny', 'null'=>false, 'default'=>'0'),
    'xar_sample'        => array ('type'=>'varchar', 'size'=>200, 'null'=>true, 'default'=>''),
    'xar_type_tid'      => array ('type'=>'integer', 'null'=>false, 'default'=>'0'),
    'xar_cache_replace' => array ('type'=>'text', 'null'=>true, 'default'=>'')
    );

    $query = xarDBCreateTable($autolinkstable, $fields);
    $result =& $dbconn->Execute($query);
    if (!$result) {return;}

    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_autolinks_1',
        'fields'    => array ('xar_keyword'),
        'unique'    => TRUE
    );
    $query = xarDBCreateIndex($autolinkstable, $index);
    $result =& $dbconn->Execute($query);
    if (!$result) {return;}


    // Table didn't exist, create table
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
    $fields = array (
    'xar_tid'           => array ('type'=>'integer', 'null'=>false, 'increment'=>true, 'primary_key'=>true),
    'xar_type_name'     => array ('type'=>'varchar', 'size'=>60, 'null'=>false, 'default'=>''),
    'xar_template_name' => array ('type'=>'varchar', 'size'=>60, 'null'=>false, 'default'=>''),
    'xar_dynamic_replace' => array ('type'=>'integer', 'size'=>'tiny', 'null'=>false, 'default'=>'0'),
    'xar_link_itemtype' => array ('type'=>'integer', 'null'=>false, 'default'=>'0'),
    'xar_type_desc'     => array ('type'=>'text', 'null'=>true, 'default'=>'')
    );

    $query = xarDBCreateTable($autolinkstypestable, $fields);
    $result =& $dbconn->Execute($query);
    if (!$result) {return;}
    
    // Set up module variables
    xarModSetVar('autolinks', 'itemsperpage', 20);
    xarModSetVar('autolinks', 'decoration', '');
    xarModSetVar('autolinks', 'maxlinkcount', '');
    xarModSetVar('autolinks', 'newwindow', 1);
    xarModSetVar('autolinks', 'punctuation', AUTOLINKS_PUNCTUATION);
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

    // Create the first autolinks type data.
    if (!autolinks_init_upgrade_data()) {
        return;
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
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

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
            $queries = array ();

            $queries[] = xarDBAlterTable(
                $autolinkstable,
                array (
                    'command'   => 'add',
                    'field'     => 'xar_enabled',
                    'type'      => 'integer',
                    'size'      => 'tiny',
                    'null'      => false,
                    'first'     => false,
                    'default'   => '1'
                )
            );

            $queries[] = xarDBAlterTable(
                $autolinkstable,
                array (
                    'command'   => 'add',
                    'field'     => 'xar_valid',
                    'type'      => 'integer',
                    'size'      => 'tiny',
                    'null'      => false,
                    'first'     => false,
                    'default'   => '1'
                )
            );

            foreach ($queries as $query)
            {
                // Pass to ADODB, and send exception if the result isn't valid.
                $result =& $dbconn->Execute($query);
                if (!$result) {
                    //return;
                    // Until we have a better method of handling errors, it is safer to continue.
                    xarExceptionHandled();
                }
            }

        case '1.2':
            // Changes to upgrade from 1.2 to 1.3

            // Add columns to the Autolinks table.
            $queries = array ();

            $queries[] = xarDBAlterTable(
                $autolinkstable,
                array (
                    'command'   => 'add',
                    'field'     => 'xar_match_re',
                    'type'      => 'integer',
                    'size'      => 'tiny',
                    'null'      => false,
                    'first'     => false,
                    'default'   => '0'
                )
            );

            $queries[] = xarDBAlterTable(
                $autolinkstable,
                array (
                    'command'   => 'add',
                    'field'     => 'xar_sample',
                    'type'      => 'varchar',
                    'size'      => '200',
                    'null'      => true,
                    'first'     => false,
                    'default'   => ''
                )
            );

            $queries[] = xarDBAlterTable(
                $autolinkstable,
                array (
                    'command'   => 'add',
                    'field'     => 'xar_cache_replace',
                    'type'      => 'varchar',
                    'size'      => '200',
                    'null'      => true,
                    'first'     => false,
                    'default'   => ''
                )
            );

            foreach ($queries as $query)
            {
                // Pass to ADODB, and send exception if the result isn't valid.
                $result =& $dbconn->Execute($query);
                if (!$result) {
                    //return;
                    // Until we have a better method of handling errors, it is safer to continue.
                    xarExceptionFree();
                }
            }

            xarModSetVar('autolinks', 'punctuation', AUTOLINKS_PUNCTUATION);
            xarModSetVar('autolinks', 'nbspiswhite', '1');

        case '1.3':
            // Changes to upgrade from 1.3 to 1.4
            // Main changes are:
            // - introduce autolink type
            // - drop and alter some autolink table columns
            // - set up some sample data

            // Need Xaraya version 0.9.1 or above for correct ADOdb version.
            if (!xarModAPIfunc('base', 'versions', 'assert_application', array('0.9.1'))) {
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
            $dict = NewDataDictionary($dbconn);

            $sqlarray = $dict->ChangeTableSQL($autolinkstable, $flds);

            // Copy keyword to name column.
            $sqlarray[] = 'UPDATE ' . $autolinkstable
                . ' SET xar_name = xar_keyword'
                . ' WHERE xar_name IS NULL OR xar_name = \'\'';

            $sqlarray = array_merge($sqlarray, $dict->ChangeTableSQL($autolinkstable, $flds));

            // Drop an old column on the autolinks table.
            $droparray = $dict->DropColumnSQL($autolinkstable, "xar_valid");

            // If column exists (i.e. no exception) then add the drop statement to the SQL array.
            if (!xarExceptionId()) {
                $sqlarray = array_merge($sqlarray, $droparray);
            } else {
                xarExceptionHandled();
            }

            // TODO: this would be a neat thing to use in upgrades. We could
            // do with a xar wrapper for it.
            // Execute the DDL and SQL we have built up.
            if (!$dict->ExecuteSQLArray($sqlarray)) {
                return;
            }

            // Make the name column mandatory. Must do this after executing the
            // other changes otherwise the data dictionary creates am 'add column'.
            $flds = "xar_name        C(100)   NotNull";
            $sqlarray = $dict->ChangeTableSQL($autolinkstable, $flds);
            if (!$dict->ExecuteSQLArray($sqlarray)) {
                return;
            }
            
            // Create a unique index for the autolinks name.
            // TODO?

            // Create autolinks types type.
            
            // Create a simple table the legacy way first, so xar knows it exists (not using data dict yet).
            $fields = array (
            'xar_tid'           => array ('type'=>'integer', 'null'=>false, 'increment'=>true, 'primary_key'=>true)
            );
            $query = xarDBCreateTable($autolinkstypestable, $fields);
            $result =& $dbconn->Execute($query);
            if (!$result) {return;}

            // Now update the table using the data dictionary.
            $flds = "
                xar_tid             I       AUTO    PRIMARY,
                xar_type_name       C(60)   NotNull,
                xar_template_name   C(60)   NotNull,
                xar_dynamic_replace L       NotNull DEFAULT 0,
                xar_link_itemtype   I       NotNull DEFAULT 0,
                xar_type_desc       X
            ";

            $sqlarray = $dict->ChangeTableSQL($autolinkstypestable, $flds);

            if (!$dict->ExecuteSQLArray($sqlarray)) {
                return;
            }

            xarModSetVar('autolinks', 'templatebase', 'link');
            xarModSetVar('autolinks', 'showerrors', 0);
            xarModSetVar('autolinks', 'showsamples', 1);

        case '1.4':
            // Changes to upgrade from 1.4 to 1.5

            // New itemtype for the autolink types themselves.
            xarModSetVar('autolinks', 'typeitemtype', 1);

            // The security instance 'keyword' has changed to 'name'. Only
            // need to update the query and the header.
            $sitePrefix = xarDBGetSiteTablePrefix();
            $query = 'update ' . $sitePrefix . '_privileges'
                . ' set xar_header = \'Autolink Name:\','
                . ' xar_query = \'SELECT DISTINCT xar_name FROM ' . $autolinkstable . '\''
                . ' where xar_module = \'autolinks\''
                . ' and xar_header = \'Autolink Keyword:\''
                . ' and xar_component = \'Autolinks\'';
            
            $result =& $dbconn->Execute($query);
            if (xarExceptionId()) {
                xarExceptionHandled();
            }

        case '1.5':
            // The current version.

            // Create or update sample data.
            autolinks_init_upgrade_data();

            return true;
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
    if (!xarModUnregisterHook('item',
                             'transform',
                             'API',
                             'autolinks',
                             'user',
                             'transform')) {return;}

    // Drop the table
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $autolinkstable = $xartable['autolinks'];
    $query = xarDBDropTable($autolinkstable );
    $result =& $dbconn->Execute($query);
    //if (!$result) {return;}

    $autolinkstypestable = $xartable['autolinkstypes'];
    $query = xarDBDropTable($autolinkstypestable);
    $result =& $dbconn->Execute($query);
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

    // Remove Masks and Instances
    xarRemoveMasks('autolinks');
    xarRemoveInstances('autolinks');

    // Deletion successful
    return true;
}

// If required, create default autolink type and update unlinked autolinks to point to it.
// TODO: revisit error handling when a better upgrade model is available.
// TODO: structure so each new sample autolink type can be added for each upgrade.

function autolinks_init_upgrade_data()
{
    $setuptypes = array(
        array(
            'type_name' => xarML('Standard autolink'),
            'template_name' => 'standard',
            'default' => true),
        array(
            'type_name' => xarML('Sample autolink type 1'),
            'template_name' => 'sample1',
            'type_desc' => xarML('URL in [square brackets] after the matched keyword.')
        ),
        array(
            'type_name' => xarML('External'),
            'template_name' => 'external',
            'type_desc' => xarML('External URLs. Opens in an "external" window. These URLs are marked up with a "WWW" world icon.')
        ),
        array(
            'type_name' => xarML('Articles'),
            'template_name' => 'article',
            'dynamic_replace' => '1',
            'type_desc' => xarML('Various links for fetching articles links.'),
            'links' => array(
                array(
                    'name' => xarML('Article title by article ID'),
                    'keyword' => '\[article:title:aid:([\d]+)\]',
                    'match_re' => '1',
                    'title' => '$2',
                    'url' => 'display',
                    'comment' => 'Use format: [article:title:aid:<article-id>]',
                    'sample' => 'Valid article: [article:title:aid:1]; invalid: [article:title:aid:9999]',
                    'enabled' => '1'
                )
            )
        )
    );

    // Create some autolink types where they do not exist.

    foreach ($setuptypes as $setuptype) {
        // Check if a type for that template exists.
        $links = xarModAPIfunc(
            'autolinks', 'user', 'getalltypes',
            array('template_name' => $setuptype['template_name'])
        );

        if (!$links) {
            // Create the autolink type
            $tid = xarModAPIfunc('autolinks', 'admin', 'createtype', $setuptype);
            if ($tid) {
                // Now if this is the default type, point existing links to it.
                if (!empty($setuptype['default'])) {
                    // Scan the current autolinks for tids to be updated.
                    $links = xarModAPIfunc('autolinks', 'user', 'getall');
                    if (is_array($links)) {
                        foreach ($links as $lid => $link) {
                            if ($links['tid'] == 0 || $links['type_name'] == '') {
                                // Update the tid in this link.
                                $result = xarModAPIfunc('autolinks', 'admin', 'update',
                                    array('lid'=>$lid, 'tid'=>$tid));
                            }
                        }
                    }
                }

                // If there are example links to add, do them.
                if (isset($setuptype['links'])) {
                    foreach ($setuptype['links'] as $samplelink) {
                        $samplelink['tid'] = $tid;
                        $result = xarModAPIfunc('autolinks', 'admin', 'create', $samplelink);
                        //if (!$result) {return;}
                    }
                }
            } else {
                if (xarExceptionValue()) {
                    xarExceptionHandled();
                }
            }
        }
    }

    return true;
}

?>