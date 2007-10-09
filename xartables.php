<?php
/**
 * Sitecontact table definitions function
 * 
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <icedlava@2skies.com>
*/

/*
 * Return sitecontact table names to xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded.
 *
 * @access private
 * @return array
 */
 
function sitecontact_xartables()
{ 
    // Initialise table array
    $xarTables = array();

    $sitecontactTable     = xarDBGetSiteTablePrefix() . '_sitecontact';
    $xarTables['sitecontact']     = $sitecontactTable;

    $sitecontactResponseTable     = xarDBGetSiteTablePrefix() . '_sitecontact_response';
    $xarTables['sitecontact_response']     = $sitecontactResponseTable;
    
    // Return the table information
    return $xarTables;
} 

?>