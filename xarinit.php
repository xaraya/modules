<?php
/**
 * Initialization for Stats module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Stats Module
 * @link http://xaraya.com/index.php/release/34.html
 * @author Frank Besler <frank@besler.net>
 */
/**
 * Install the Stats module
 *
 * This function installs the tables of the Stats module
 * and sets the state of the module from uninitialized to inactive
 *
 * @access  private
 * @param   none
 * @return  bool
 */
function stats_init()
{
    // Dependancy check - will be removed when core supports dependancies
    if (!xarModIsAvailable('sniffer')) {
        $msg = xarML('Please install and activate the module \'Sniffer\'');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_MODULE', new DefaultUserException($msg));
        return false;
    }

    // Set up module variable
    xarModSetVar('stats', 'startdate', time());
    xarModSetVar('stats', 'countadmin', 0);
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable     =& xarDBGetTables();
    $statstable   = $xartable['stats'];

    // Load the Table Maintainance API
    xarDBLoadTableMaintenanceAPI();

    // Create the table
    $query = xarDBCreateTable($statstable,
                              array('xar_sta_year'    => array('type' => 'integer', 'size' => 'small',
                                                               'null' => false, 'default' => '0',
                                                               'primary_key' => true),
                                    'xar_sta_month'   => array('type' => 'integer', 'size' => 'tiny',
                                                               'null' => false, 'default' => '0',
                                                               'primary_key' => true),
                                    'xar_sta_weekday' => array('type' => 'integer', 'size' => 'tiny',
                                                               'null' => false, 'default' => '0'),
                                    'xar_sta_day'     => array('type' => 'integer', 'size' => 'tiny',
                                                               'null' => false, 'default' => '0',
                                                               'primary_key'=> true),
                                    'xar_sta_hour'    => array('type' => 'integer', 'size' => 'tiny',
                                                               'null' => false, 'default' => '0', 'primary_key'=> true),
                                    'xar_ua_id'       => array('type' => 'integer', 'size' => 'small',
                                                               'unsigned' => true, 'null' => false,
                                                               'default' => '0', 'primary_key'=> true),
                                    'xar_sta_hits'    => array('type' => 'integer', 'size' => 'medium',
                                                               'unsigned' => true, 'null' => false,
                                                               'default' => '0'),
                                    'xar_sta_unique'  => array('type' => 'integer', 'size' => 'medium',
                                                               'unsigned' => true, 'null' => false,
                                                               'default' => '0')
                                    ));
    if (empty($query)) return false;

    // Pass the generated query to adodb to create the table
    $result =& $dbconn->Execute($query);
    if (!$result) return false;

    // set index
    $query = xarDBCreateIndex($statstable,
                              array('name'   => 'i_'.xarDBGetSiteTablePrefix().'stats_wd',
                                    'fields' => array('xar_sta_weekday')));

    // Pass the generated query to adodb to create the table
    $result =& $dbconn->Execute($query);
    if (!$result) return false;

    xarRegisterMask('AdminStats','All','stats','All','All','ACCESS_ADMIN');
    xarRegisterMask('OverviewStats','All','stats','All','All','ACCESS_OVERVIEW');

    // Initialisation successful
    return true;
}

/**
 * Upgrade the Stats module from an old version
 *
 * @access  private
 * @param   $oldversion float - the version number of the old Stats module
 * @return  bool
 */
function stats_upgrade($oldversion)
{
    // Get database setup
//    $dbconn =& xarDBGetConn();
//    $xartable =& xarDBGetTables();

    // load the table maintenance API
//    xarDBLoadTableMaintenanceAPI();

    // Upgrade dependent on old version number
    switch($oldversion) {
        case '2.00':
            break;
        case '2.0.1':
            break;
        case '2.0.2':
            xarModSetVar('stats', 'countadmin', 0);
            xarRegisterMask('AdminStats','All','stats','All','All','ACCESS_ADMIN');
            $modversion['admin']          = 1;
            break;
        case '2.0.3':
            break;
        case '2.0.4':
            break;
    }
    return true;
}

/**
 * Uninstall the Stats module
 *
 * This function removes the database table of the Stats module
 * unrecoverably from the database
 *
 * @access  private
 * @param   none
 * @return  bool
 */
function stats_delete()
{
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Load the Table Maintainance API
    xarDBLoadTableMaintenanceAPI();

    // Generate the deletion query
    $query = xarDBDropTable($xartable['stats']);
    if (empty($query)) return; // throw back

    // Drop the table
    $result =& $dbconn->Execute($query);

    // Check for an error with the database code, and if so raise the
    if (!$result) return false;

    xarModDelVar('stats', 'startdate');

    xarRemoveMasks('stats');
    xarRemoveInstances('stats');

    // Deletion successful
    return true;
}

?>
