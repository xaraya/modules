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
    $dbconn =& xarDBGetConn();
    $module = 'mag';

    // Get table names.
    extract(mag_init_tablelist($module));
    
    $magazines_table = $xartable[$module . '_magazines'];
    $issues_table = $xartable[$module . '_issues'];
    $series_table = $xartable[$module . '_series'];
    $articles_table = $xartable[$module . '_articles'];
    $authors_table = $xartable[$module . '_authors'];
    $articles_authors_table = $xartable[$module . '_articles_authors'];

    // Load Table Maintainance API
    xarDBLoadTableMaintenanceAPI();

    // Magazines
    // Create the magazines table.

    // Issues
    // Create the issues table.

    // Series
    // Create the series table.
    
    // Articles
    // Create the articles table.
    
    // Authors
    // Create the authors table.
    
    // Articles_Authors
    // Create the articles_authors table.

    // Create indexes

    // Set up module variables

    // Switch short URL support on by default.
    xarModSetVar($module, 'SupportShortURLs', 1);

    // Privileges

    // Components

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
    /*xarRegisterMask(
        'Comment' . $comp, 'All', $module, $comp, 'All', 'ACCESS_COMMENT',
        xarML('')
    );*/
    /*xarRegisterMask(
        'Moderate' . $comp, 'All', $module, $comp, 'All', 'ACCESS_MODERATE',
        xarML('')
    );*/
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
            'header' => 'Magazine ID',
            'query' => 'SELECT mid FROM ' . $table_magazines . ' ORDER BY mid',
            'limit' => 50
        ),
        // The premium flag can be OPEN, SAMPLE or PREMIUM.
        // However, the admin may extend that list, but there is no simple
        // place to put that data for selection.
        // *** TODO: Perhaps we need to create a custom form, though that seems like
        // *** overkill for something that should be a lot simpler: a simple API call.
        array (
            'header' => 'Premium Code',
            'query' => '',
            'limit' => 50
        ),
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


    // Register block types.
    // None.

    // Set up module hooks
    // None.

    // Create the DD objects
    $objectid = xarModAPIFunc(
        'dynamicdata', 'util', 'import',
        array('file' => 'modules/' .$module. '/xardata/' .$module. '_mags-def.xml', 'keepitemid' => false)
    );
    $objectid = xarModAPIFunc(
        'dynamicdata', 'util', 'import',
        array('file' => 'modules/' .$module. '/xardata/' .$module. '_issues-def.xml', 'keepitemid' => false)
    );
    $objectid = xarModAPIFunc(
        'dynamicdata', 'util', 'import',
        array('file' => 'modules/' .$module. '/xardata/' .$module. '_articles-def.xml', 'keepitemid' => false)
    );
    $objectid = xarModAPIFunc(
        'dynamicdata', 'util', 'import',
        array('file' => 'modules/' .$module. '/xardata/' .$module. '_series-def.xml', 'keepitemid' => false)
    );
    $objectid = xarModAPIFunc(
        'dynamicdata', 'util', 'import',
        array('file' => 'modules/' .$module. '/xardata/' .$module. '_authors-def.xml', 'keepitemid' => false)
    );
    $objectid = xarModAPIFunc(
        'dynamicdata', 'util', 'import',
        array('file' => 'modules/' .$module. '/xardata/' .$module. '_articles_authors-def.xml', 'keepitemid' => false)
    );

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
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $module = 'mag';

    // Get table names.
    extract(mag_init_tablelist($module));

    // Upgrade dependent on old version number.
    switch ($oldversion) {
        case '0.1.0':
            // Upgrading from 0.1.0
        case '0.1.1':
            // Upgrading from 0.1.1
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

    // Deletion successful.
    return true;
}

// Return an array of table names.
// TODO: would be nice if xarDBGetTables() accepted a parameter to limit the table names,
// including the ability to use wildcards.
// Returns array of "table_{name}" => {table_name}
function mag_init_tablelist($module = 'mag')
{
    $xartable =& xarDBGetTables();

    $return = array();

    foreach(array('magazines', 'issues', 'series', 'articles', 'authors', 'articles_authors') as $base) {
        $return['table_' . $base] = $xartable[$module . '_' . $base];
    }

    return $return;
}

?>