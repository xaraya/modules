<?php
/**
 * File: $Id: s.xartables.php 1.7 03/03/18 02:35:04-05:00 johnny@falling.local.lan $
 * 
 * Example table definitions function
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author Michel V. 
 */

/**
 * Upgraded to the new security schema by Vassilis Stratigakis
 * http://www.tequilastarrise.net
 */

/**
 * Return sigmapersonnel table names to xaraya
 * 
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 * 
 * @access private 
 * @return array 
 */
function sigmapersonnel_xartables()
{ 
    // Initialise table array
    $xarTables = array(); 
    // Get the name for the sigmapersonnel item table.
    $sigmapersonneltable = xarDBGetSiteTablePrefix() . '_sigmapersonnel_person'; 
    $xarTables['sigmapersonnel_person'] = $sigmapersonneltable; 
	
	$presencetable = xarDBGetSiteTablePrefix() . '_sigmapersonnel_presence'; 
    $xarTables['sigmapersonnel_presence'] = $presencetable; 
	
	$presencetypestable = xarDBGetSiteTablePrefix() . '_sigmapersonnel_presencetypes'; 
    $xarTables['sigmapersonnel_presencetypes'] = $presencetypestable; 

    $citiestable = xarDBGetSiteTablePrefix() . '_sigmapersonnel_cities';
    $xarTables['sigmapersonnel_cities'] = $citiestable; 

    $statustable = xarDBGetSiteTablePrefix() . '_sigmapersonnel_status';
	$xarTables['sigmapersonnel_status'] = $statustable;
	
    $districtstable = xarDBGetSiteTablePrefix() . '_sigmapersonnel_districts';
    $xarTables['sigmapersonnel_districts'] = $districtstable;
    // Return the table information
    return $xarTables;
} 

?>
