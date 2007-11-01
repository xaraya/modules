<?php

/**
 * Initialise the mag module.
 *
 * @package modules
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @author Jason Judge
 */

/**
 * Initialise the mag module
 * @return bool
 */
function mag_init()
{
    // Set up database tables
    $module = 'mag';

    // Create the database tables.
    $result = mag_init_tables($module);
    if (!$result) return;
    
    // Set up module variables

    // Switch short URL support on by default.
    xarModSetVar($module, 'SupportShortURLs', 1);

    // Privileges
    $result = mag_init_privileges($module);
    if (empty($result)) xarErrorFree();

    // Register block types.
    // None.

    // Set up module hooks
    // None.

    // Set up the DD objects.
    $result = mag_init_ddobjects($module);
    // For now, ignore any create errors.
    // We don't know how many errors are pushed into the stack, 
    // so we can't use xarErrorHandled() to pop it off. We will
    // just discard them all for now.
    // We are stuck between a rock and a hard place when it comes
    // to handling errors.
    if (empty($result)) xarErrorFree();

    return true;
}

/**
 * Upgrade the mag module from an old version.
 *
 * @param string oldversion
 * @return bool true on success
 */
function mag_upgrade($oldversion)
{
    $module = 'mag';

    // Get table names.
    extract(mag_init_tablelist($module));

    // Upgrade dependent on old version number.
    switch ($oldversion) {
        case '0.1.0':
            // Upgrading from 0.1.0
            // Set up the privileges.
            mag_init_privileges($module);
            // Create the database tables.
            $result = mag_init_tables($module);
            if (empty($result)) xarErrorFree();
            // Set up the DD objects.
            $result = mag_init_ddobjects($module);
            if (empty($result)) xarErrorFree();

            // Switch short URL support on by default.
            xarModSetVar($module, 'SupportShortURLs', 1);

        case '0.2.0':
            // Upgrading from 0.2.0

        break;
    }

    return true;
}

/**
 * Delete (remove) the mag module.
 * @return bool true on success
 */
function mag_delete()
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $module = 'mag';

    // Get table names.
    extract(mag_init_tablelist($module));

    // Delete module variables
    xarModDelAllVars($module);

    // Drop privileges.
    xarRemoveMasks($module);
    xarRemoveInstances($module);

    // TODO: Drop the DD objects and tables.
    // For now, and until there is a method by which this data can be backed up,
    // we will leave this as a manual excerise for the administrator.

    // Deletion (i.e. removal) successful.
    return true;
}

// Return an array of table names.
// TODO: would be nice if xarDBGetTables() accepted a parameter to limit the table names,
// including the ability to use wildcards.
// Returns array of "table_{name}" => {table_name}
function mag_init_tablelist($module)
{
    $xartable =& xarDBGetTables();

    $return = array();

    foreach(mag_init_table_bases() as $table_base) {
        $return['table_' . $table_base] = $xartable[$module . '_' . $table_base];
    }

    return $return;
}

// Return the list of table/object names.
function mag_init_table_bases()
{
    return array('mags', 'issues', 'series', 'articles', 'authors', 'articles_authors');
}

// Set up the privileges.
function mag_init_privileges($module)
{
    // Components

    // Get table names.
    extract(mag_init_tablelist($module));

    // Set up 'Mag' component.
    $comp = 'Mag';
    $instances = array (
        array (
            'header' => 'Magazine ID',
            'query' => 'SELECT mid FROM ' . $table_magazines . ' ORDER BY mid',
            'limit' => 50
        ),
    );
    // This function is a misnomer. It actually defines a *component*, not an instance.
    xarDefineInstance(
        $module, $comp, $instances, 0, 'All', 'All', 'All',
        xarML('Security component for #(1) #(2)', $module, $comp)
    );

    // Masks for component 'Mag'.
    xarRegisterMask(
        'Overview' . $comp, 'All', $module, $comp, 'All', 'ACCESS_OVERVIEW',
        xarML('Read the magazine overviews and TOCs')
    );
    xarRegisterMask(
        'Read' . $comp, 'All', $module, $comp, 'All', 'ACCESS_READ',
        xarML('Read articles')
    );
    xarRegisterMask(
        'Edit' . $comp, 'All', $module, $comp, 'All', 'ACCESS_EDIT',
        xarML('Edit articles in a magazine')
    );
    xarRegisterMask(
        'Delete' . $comp, 'All', $module, $comp, 'All', 'ACCESS_DELETE',
        xarML('Edit series and magazine details')
    );
    xarRegisterMask(
        'Admin' . $comp, 'All', $module, $comp, 'All', 'ACCESS_ADMIN',
        xarML('Administer the magazine')
    );

    // Set up 'Art' (Article) component.
    $comp = 'MagArt';
    $instances = array (
        array (
            'header' => 'external',
            'query' => xarModURL($module, 'admin', 'privileges', array('component' => $comp)),
            'limit' => 50
        ),
        /*array (
            'header' => 'Magazine ID',
            'query' => 'SELECT mid FROM ' . $table_magazines . ' ORDER BY mid',
            'limit' => 50
        ),*/
        // The premium flag can be OPEN, SAMPLE or PREMIUM.
        // However, the admin may extend that list, but there is no simple
        // place to put that data for selection.
        // *** TODO: Perhaps we need to create a custom form, though that seems like
        // *** overkill for something that should be a lot simpler: a simple API call.
        /*array (
            'header' => 'Premium Code',
            'query' => '',
            'limit' => 50
        ),*/
    );
    // This function is a misnomer. It actually defines a *component*, not an instance.
    xarDefineInstance(
        $module, $comp, $instances, 0, 'All', 'All', 'All',
        xarML('Security component for #(1) #(2)', $module, $comp)
    );

    // Masks for component 'MagArt'.
    xarRegisterMask(
        'Overview' . $comp, 'All', $module, $comp, 'All', 'ACCESS_OVERVIEW',
        xarML('View summary details only for the article')
    );
    xarRegisterMask(
        'Read' . $comp, 'All', $module, $comp, 'All', 'ACCESS_READ',
        xarML('Read full article')
    );

    return;
}

// Create DD objects.
// Return false if any errors occured, but otherwise continue.
function mag_init_ddobjects($module)
{
    // Create the DD objects

    $return = true;

    foreach(mag_init_table_bases() as $table_base) {
        $objectid = xarModAPIFunc(
            'dynamicdata', 'util', 'import',
            array('file' => 'modules/' .$module. '/xardata/' .$module. '_' . $table_base . '-def.xml', 'keepitemid' => false)
        );
        if (empty($object)) $return = false;
    }

    return $return;
}

// Create the database objects.
// Return false if any errors occured, but otherwise continue.
function mag_init_tables($module)
{
    // Get database connection.
    $dbconn =& xarDBGetConn();

    // Get table names.
    extract(mag_init_tablelist($module));
    
    // Load Table Maintainance API
    xarDBLoadTableMaintenanceAPI();

    $return = true;
    $indexes = array();
    $fields = array();

    // Magazines (mag_mags)
    /*
        CREATE TABLE `xar_mag_mags` (
            `mid` int(11) NOT NULL auto_increment,
            `ref` varchar(60) NOT NULL default '',
            `title` varchar(255) NOT NULL default '',
            `showin` varchar(30) NOT NULL default 'ALL',
            `subtitle` varchar(255) default NOT NULL '',
            `status` varchar(30) NOT NULL default 'ACTIVE',
            `synopsis` text,
            `logo` varchar(255) NOT NULL default '',
            `premium` varchar(30) NOT NULL default '',
            PRIMARY KEY  (`mid`),
            UNIQUE KEY `ref` (`ref`)
        );
    */
    $table = $table_mags;
    $fields[$table] = array(
        'mid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'ref' => array('type' => 'varchar', 'size' => 60, 'null' => false, 'default' => ''),
        'title' => array('type' => 'varchar', 'size' => 255, 'null' => false, 'default' => ''),
        'showin' => array('type' => 'varchar', 'size' => 30, 'null' => false, 'default' => 'ALL'),
        'subtitle' => array('type' => 'varchar', 'size' => 255, 'null' => false, 'default' => ''),
        'status' => array('type' => 'varchar', 'size' => 30, 'null' => false, 'default' => 'ACTIVE'),
        'synopsis' => array('type' => 'text', 'null' => true),
        'logo' => array('type' => 'varchar', 'size' => 255, 'null' => false, 'default' => ''),
        'premium' => array('type' => 'varchar', 'size' => 30, 'null' => false, 'default' => 'ACTIVE'),
    );

    // Record the indexes for this table.
    $indexes[$table] = array();
    $indexes[$table][] = array(
        'name' => 'i_' . $table . '_1',
        'fields'    => array('ref'),
        'unique'    => true
    );

    // Issues (mag_issues)
    /*
        CREATE TABLE `xar_mag_issues` (
            `iid` int(11) NOT NULL auto_increment,
            `mag_id` int(11) NOT NULL default '0',
            `ref` varchar(60) NOT NULL default '',
            `title` varchar(255) NOT NULL default '',
            `number` int(11) default NULL,
            `status` varchar(30) NOT NULL default 'DRAFT',
            `pubdate` int(11) NOT NULL default '0',
            `tagline` varchar(255) NOT NULL default '',
            `cover_img` varchar(255) NOT NULL default '',
            `abstract` text,
            `premium` varchar(30) NOT NULL default '',
            PRIMARY KEY  (`iid`),
            KEY `mag_id` (`mag_id`),
            KEY `ref` (`ref`)
        );
    */

    $table = $table_issues;
    $fields[$table] = array(
        'iid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'mag_id' => array('type' => 'integer', 'size' => 11, 'null' => false, 'default' => '0'),
        'ref' => array('type' => 'varchar', 'size' => 60, 'null' => false, 'default' => ''),
        'title' => array('type' => 'varchar', 'size' => 255, 'null' => false, 'default' => ''),
        'number' => array('type' => 'integer', 'size' => 11, 'null' => true, 'default' => NULL),
        'status' => array('type' => 'varchar', 'size' => 30, 'null' => false, 'default' => 'DRAFT'),
        'pubdate' => array('type' => 'integer', 'size' => 11, 'null' => false, 'default' => '0'),
        'tagline' => array('type' => 'varchar', 'size' => 255, 'null' => false, 'default' => ''),
        'cover_img' => array('type' => 'varchar', 'size' => 255, 'null' => false, 'default' => ''),
        'abstract' => array('type' => 'text', 'null' => true),
        'premium' => array('type' => 'varchar', 'size' => 30, 'null' => false, 'default' => ''),
    );

    // Record the indexes for this table.
    $indexes[$table] = array();
    $indexes[$table][] = array(
        'name' => 'i_' . $table . '_1',
        'fields'    => array('mag_id'),
        'unique'    => false
    );
    $indexes[$table][] = array(
        'name' => 'i_' . $table . '_2',
        'fields'    => array('ref'),
        'unique'    => true
    );

    // Series (mag_series)
    /*
        CREATE TABLE `xar_mag_series` (
            `sid` int(11) NOT NULL auto_increment,
            `mag_id` int(11) NOT NULL default '0',
            `ref` varchar(60) NOT NULL default '',
            `title` varchar(255) NOT NULL default '',
            `synopsis` text,
            `description` text,
            `display_order` int(11) NOT NULL default '0',
            `style` varchar(30) NOT NULL default '',
            `status` varchar(30) NOT NULL default 'DRAFT',
            PRIMARY KEY  (`sid`),
            KEY `mag_id` (`mag_id`)
        );
    */

    $table = $table_series;
    $fields[$table] = array(
        'sid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'mag_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
        'ref' => array('type' => 'varchar', 'size' => 60, 'null' => false, 'default' => ''),
        'title' => array('type' => 'varchar', 'size' => 255, 'null' => false, 'default' => ''),
        'synopsis' => array('type' => 'text', 'null' => true),
        'description' => array('type' => 'text', 'null' => true),
        'display_order' => array('type' => 'integer', 'null' => false, 'default' => '0'),
        'style' => array('type' => 'varchar', 'size' => 30, 'null' => false, 'default' => ''),
        'status' => array('type' => 'varchar', 'size' => 30, 'null' => false, 'default' => 'DRAFT'),
    );

    // Record the indexes for this table.
    $indexes[$table] = array();
    $indexes[$table][] = array(
        'name' => 'i_' . $table . '_1',
        'fields'    => array('mag_id'),
        'unique'    => false
    );

    // Articles (mag_articles)
    /*
        CREATE TABLE `xar_mag_articles` (
            `aid` int(11) NOT NULL auto_increment,
            `issue_id` int(11) NOT NULL default '0',
            `series_id` int(11) NOT NULL default '0',
            `ref` varchar(255) character set latin1 NOT NULL default '',
            `title` varchar(255) character set latin1 NOT NULL default '',
            `subtitle` varchar(255) character set latin1 NOT NULL default '',
            `summary` text character set latin1,
            `body` text character set latin1 NOT NULL,
            `footer` text character set latin1,
            `status` varchar(30) character set latin1 NOT NULL default 'DRAFT',
            `pubdate` int(11) NOT NULL default '0',
            `refs` text character set latin1,
            `page` int(11) default NULL,
            `tags` varchar(255) character set latin1 default NULL,
            `premium` varchar(30) character set latin1 NOT NULL default '',
            `style` varchar(30) character set latin1 NOT NULL default '',
            `image1` varchar(255) character set latin1 default NULL,
            `image1_alt` varchar(255) character set latin1 NOT NULL default '',
            PRIMARY KEY  (`aid`),
            KEY `issue_id` (`issue_id`),
            KEY `series_id` (`series_id`)
        );
    */
    
    $table = $table_articles;
    $fields[$table] = array(
        'aid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'issue_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
        'series_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
        'ref' => array('type' => 'varchar', 'size' => 60, 'null' => false, 'default' => ''),
        'title' => array('type' => 'varchar', 'size' => 255, 'null' => false, 'default' => ''),
        'subtitle' => array('type' => 'varchar', 'size' => 255, 'null' => false, 'default' => ''),
        'summary' => array('type' => 'text', 'null' => false),
        'body' => array('type' => 'text', 'null' => false),
        'footer' => array('type' => 'text', 'null' => false),
        'status' => array('type' => 'varchar', 'size' => 30, 'null' => false, 'default' => 'DRAFT'),
        'pubdate' => array('type' => 'integer', 'size' => 11, 'null' => FALSE, 'default' => '0'),
        'refs' => array('type' => 'text', 'null' => false),
        'page' => array('type' => 'integer', 'size' => 11, 'null' => true, 'default' => NULL),
        'tags' => array('type' => 'varchar', 'size' => 255, 'null' => false, 'default' => ''),
        'premium' => array('type' => 'varchar', 'size' => 30, 'null' => false, 'default' => ''),
        'style' => array('type' => 'varchar', 'size' => 30, 'null' => false, 'default' => ''),
        'image1' => array('type' => 'varchar', 'size' => 255, 'null' => false, 'default' => ''),
        'image1_alt' => array('type' => 'varchar', 'size' => 255, 'null' => false, 'default' => ''),
    );

    // Record the indexes for this table.
    $indexes[$table] = array();
    $indexes[$table][] = array(
        'name' => 'i_' . $table . '_1',
        'fields'    => array('issue_id'),
        'unique'    => false
    );
    $indexes[$table][] = array(
        'name' => 'i_' . $table . '_2',
        'fields'    => array('series_id'),
        'unique'    => false
    );

    // Authors (mag_authors)
    /*
        CREATE TABLE `xar_mag_authors` (
            `auid` int(11) NOT NULL auto_increment,
            `name` varchar(255) NOT NULL default '',
            `mini_bio` text,
            `full_bio` text,
            `photo` varchar(255) default NULL,
            `email` varchar(255) default NULL,
            `website` varchar(255) default NULL,
            `contact` text,
            `notes` text,
            PRIMARY KEY  (`auid`)
        );
    */

    $table = $table_authors;
    $fields[$table] = array(
        'auid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'name' => array('type' => 'varchar', 'size' => 255, 'null' => false, 'default' => ''),
        'mini_bio' => array('type' => 'text', 'null' => false),
        'full_bio' => array('type' => 'text', 'null' => false),
        'photo' => array('type' => 'varchar', 'size' => 255, 'null' => false, 'default' => ''),
        'email' => array('type' => 'varchar', 'size' => 255, 'null' => false, 'default' => ''),
        'website' => array('type' => 'varchar', 'size' => 255, 'null' => false, 'default' => ''),
        'contact' => array('type' => 'text', 'null' => false),
        'notes' => array('type' => 'text', 'null' => false),
    );

    // Articles_Authors (mag_articles_authors)
    /*
        CREATE TABLE `xar_mag_articles_authors` (
            `aaid` int(11) NOT NULL auto_increment,
            `article_id` int(11) NOT NULL default '0',
            `author_id` int(11) NOT NULL default '0',
            `role` varchar(30) NOT NULL default 'WRITER',
            `notes` text,
            PRIMARY KEY  (`aaid`),
            KEY `article_id` (`article_id`),
            KEY `author_id` (`author_id`)
        );
    */

    $table = $table_articles_authors;
    $fields[$table] = array(
        'aaid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'article_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
        'author_id' => array('type' => 'integer', 'null' => false, 'default' => '0'),
        'role' => array('type' => 'varchar', 'size' => 30, 'null' => false, 'default' => ''),
        'notes' => array('type' => 'text', 'null' => false),
    );

    // The data is set up, so now do the actual processing.
    
    // Create the tables.
    // Loop over each table and sets of fields.
    foreach($fields as $table_name => $field_set) {
        $query = xarDBCreateTable($table_name, $field_set);
        $result =& $dbconn->Execute($query);
        if (!$result) $return = false;
    }
    
    // Create indexes
    // Loop for tables and indexes, and create the indexes.
    foreach($indexes as $table_name => $index_list) {
        foreach($index_list as $index) {
            $query = xarDBCreateIndex($table_name, $index);
            $result =& $dbconn->Execute($query);
            if (!$result) $result = false;
        }
    }

    return $return;
}

?>