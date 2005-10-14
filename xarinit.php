<?php

/**
 * File: $Id$
 *
 * init file for installing/upgrading BlackList module
 *
 * @package Modules
 * @copyright (C) 2002 by The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @subpackage BlackList
 */

/**
 * Initializes the module, adding to the list of 
 * currently initialized modules
 *
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @access private
 * @return bool True on success, False otherwise
 *
 */
function blacklist_init() 
{

    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    

	// Check to see if the table already exists, if 
	// it does, then we'll just use what's already there
	$sql = "SELECT COUNT(id) AS total 
			  FROM $xartable[blacklist]";

	$result =& $dbconn->Execute($sql);

	if (!is_object($result) || $result === NULL) {
		// If no result, then we don't have a 
		// table yet - so let's add it
		$fields = array(
   			'xar_id'       => array('type'=>'integer',  'null'=>FALSE,  'increment'=> TRUE, 'primary_key'=>TRUE),
        	'xar_domain'   => array('type'=>'varchar',  'null'=>FALSE,  'size'=>255)
	    );

	
    	$query = xarDBCreateTable($xartable['blacklist'], $fields);

	    $result =& $dbconn->Execute($query);
    	if (!$result) {
        	return;
    	}
	
		// Initialize the blacklist table with a base dataset
		if (!blacklist_initdb()) {
			return;
		}
	} 

	// Setup module variables
	xarModSetVar('blacklist', 'paging.numitems', 25);

	// Register Security Mask
    xarRegisterMask('BlackList-Admin', 'All','blacklist', 'All', 'All', 'ACCESS_ADMIN', 'Administrate BlackList');

    // Initialisation successful
    return TRUE;
}

/**
 * Populates the newly created tables with default domain patterns
 *
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @access private
 * @return bool True on success, False otherwise
 *
 */
function blacklist_initdb()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $btable = $xartable['blacklist'];
    $bbtable = &$xartable['blacklist_column'];
    $patterns = file('modules/blacklist/xardocs/blacklist.txt');

    foreach ($file as $lineNumber => $lineData) {
        $domain = trim($lineData);
        if (!empty($domain)) {
            $nextId = $dbconn->GenId($btable);
            $query = "INSERT
                        INTO $btable
                             (xar_id, xar_domain)
                      VALUES (?,?)";
            $bindvars = array($nextId, (string) $domain);
            $result =& $dbconn->Execute($query,$bindvars);
            if (!$result)
                return;
        }
    }

    return TRUE;
}

/**
 * Removes the module from the current list of installed modules
 *
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @access private
 * @return bool True on success, False otherwise
 *
 */
function blacklist_delete()
{
    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Delete tables
    $query = xarDBDropTable($xartable['blacklist']);
    $result =& $dbconn->Execute($query);

    if(!$result)
        return;

    // Remove Masks and Instances
    xarRemoveMasks('BlackList');
    xarRemoveInstances('BlackList');

    // Deletion successful
    return TRUE;

}

/**
 * Upgrades the module from a previous version to a new one
 *
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
 * @access private
 * @return bool True on success, False otherwise
 *
 */
function BlackList_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '1.0':
            // Code to upgrade from version 1.0 goes here
            // Register blocks
        case '1.1':
            // Code to upgrade from version 1.1 goes here
        case '2.5':
            // Code to upgrade from version 2.5 goes here
            break;
    }
    return TRUE;
}
?>
