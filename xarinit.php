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
    $xartable =& xarDBGetTables();

    $module = 'mag';

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
    // Masks

    // Register block types.
    // None.

    // Set up module hooks
    // None.

    // Create the DD objects

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
    $xartable =& xarDBGetTables();

    $module = 'mag';

    $magazines_table = $xartable[$module . '_magazines'];
    $issues_table = $xartable[$module . '_issues'];
    $series_table = $xartable[$module . '_series'];
    $articles_table = $xartable[$module . '_articles'];
    $authors_table = $xartable[$module . '_authors'];
    $articles_authors_table = $xartable[$module . '_articles_authors'];

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
function ievents_delete()
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $module = 'mag';

    $magazines_table = $xartable[$module . '_magazines'];
    $issues_table = $xartable[$module . '_issues'];
    $series_table = $xartable[$module . '_series'];
    $articles_table = $xartable[$module . '_articles'];
    $authors_table = $xartable[$module . '_authors'];
    $articles_authors_table = $xartable[$module . '_articles_authors'];

    // Delete module variables
    xarModDelAllVars($module);

    // Drop privileges.
    xarRemoveMasks($module);
    xarRemoveInstances($module);

    // Deletion successful.
    return true;
}

?>