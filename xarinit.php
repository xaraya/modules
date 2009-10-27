<?php
/**
 * Xaraya Headlines
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005-2009 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Headlines Module
 * @author John Cox
 */
/**
 * Initialise the headlines module
 *
 * @return bool
 * @throws DATABASE_ERROR
 */
function headlines_init()
{
    $module = 'headlines';

# --------------------------------------------------------
#
# Create tables
#
    $dbconn =& xarDB::getConn();
    $tables =& xarDB::getTables();
    $prefix = xarDB::getPrefix();
    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

    $headlinesTable = $tables['headlines'];

    // Create tables inside a transaction
    try {
        $charset = xarSystemVars::get(sys::CONFIG, 'DB.Charset');
        $dbconn->begin();
        sys::import('xaraya.structures.query');
        $q = new Query();
        // forums table
        $query = "DROP TABLE IF EXISTS " . $headlinesTable;
        if (!$q->run($query)) return;
        $fields = array(
            'xar_hid' => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
            'xar_title' => array('type' => 'varchar','size' => 255,'null' => false, 'charset' => $charset),
            'xar_desc' => array('type' => 'varchar','size' => 255,'null' => false, 'charset' => $charset),
            'xar_url' => array('type' => 'varchar','size' => 255,'null' => false, 'charset' => $charset),
            'xar_order' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'xar_string' => array('type' => 'varchar','size' => 255,'null' => false, 'charset' => $charset),
            'xar_date' => array('type' => 'integer', 'unsigned' => true, 'null' => false,'default' => '0'),
            'xar_settings' => array('type' => 'text', 'charset' => $charset),
        );
        $query = xarDBCreateTable($headlinesTable,$fields);
        $dbconn->Execute($query);
        // hid
        $index = array('name' => $prefix . '_headlines_xar_hid',
                       'fields' => array('xar_hid')
                       );
        $query = xarDBCreateIndex($headlinesTable, $index);
        $dbconn->Execute($query);

        // We're done, commit
        $dbconn->commit();
    } catch (Exception $e) {
        $dbconn->rollback();
        throw $e;
    }

# --------------------------------------------------------
#
# Set up configuration modvars (module specific)
#

    xarModVars::set('headlines', 'itemsperpage', 20);
    xarModVars::set('headlines','importpubtype', 0);
    xarModVars::set('headlines','showfeeds', '');
    xarModVars::set('headlines', 'uniqueid', 'feed;link');
    // added in 0.9.0
    xarModVars::set('headlines', 'SupportShortURLs', 1);
    // added in 1.1.0
    xarModVars::set('headlines', 'parser', 'default');
    // added > 1.1.0
    xarModVars::set('headlines', 'feeditemsperpage', 20);
    xarModVars::set('headlines','maxdescription', 0);
    xarModVars::set('headlines','showcomments', 0);
    xarModVars::set('headlines', 'showratings', 0);
    xarModVars::set('headlines', 'showhitcount', 0);
    xarModVars::set('headlines','showkeywords', 0);
    xarModVars::set('headlines','useModuleAlias', 0);
    xarModVars::set('headlines', 'aliasname', '');
    // added in 1.2.1
    xarModVars::set('headlines', 'adminajax', 0);
    xarModVars::set('headlines', 'userajax', 0);

# --------------------------------------------------------
#
# Set up configuration modvars (common)
#
    $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => $module));
    $module_settings->initialize();

# --------------------------------------------------------
#
# Register blocks
#
    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'headlines',
                             'blockType'=> 'rss'))) return;

    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'headlines',
                             'blockType'=> 'cloud'))) return;

# --------------------------------------------------------
#
# Register masks
#

    xarRegisterMask('OverviewHeadlines','All','headlines','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadHeadlines','All','headlines','All','All','ACCESS_READ');
    xarRegisterMask('EditHeadlines','All','headlines','All','All','ACCESS_EDIT');
    xarRegisterMask('AddHeadlines','All','headlines','All','All','ACCESS_ADD');
    xarRegisterMask('DeleteHeadlines','All','headlines','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminHeadlines','All','headlines','All','All','ACCESS_ADMIN');

    return true;
}

/**
 * Upgrade the example module from an old version
 *
 * This function can be called multiple times
 *
 * @param string oldVersion old version to upgrade from
 * @return bool
 * @raise DATABASE_ERROR
 */
function headlines_upgrade($oldVersion)
{
    $dbconn =& xarDB::getConn();
    $tables =& xarDB::getTables();
    $prefix = xarDB::getPrefix();
    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

    $headlinesTable = $tables['headlines'];

    // Upgrade dependent on old version number
    switch($oldVersion) {
        case '2.0.0':

           break;
    }
    // Update successful
    return true;
}
/**
 * Delete the headlines module
 *
 * @returns bool
 */
function headlines_delete()
{
    $dbconn =& xarDB::getConn();
    $tables =& xarDB::getTables();
    $prefix = xarDB::getPrefix();
    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

    $headlinesTable = $tables['headlines'];

    // Generate the SQL to drop the table using the API
    $query = xarDB::dropTable($headlinesTable);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // UnRegister blocks
    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'headlines',
                             'blockType'=> 'rss'))) return;
    if (!xarMod::apiFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'headlines',
                             'blockType'=> 'cloud'))) return;
    xarModDelAllVars('headlines');
    xarRemoveMasks('headlines');
    xarRemoveInstances('headlines');
    return true;
}
?>
