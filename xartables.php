<?php
/**
 * File: $Id: xartables.php,v 1.1.1.1 2005/11/28 18:55:21 curtis Exp $
 * 
 * Bible table definitions function
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf 
 */

/**
 * Return text table names to xaraya
 * 
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 * 
 * @access private 
 * @return array 
 */
function bible_xartables()
{ 
	$prefix = xarDBGetSiteTablePrefix();

    $xarTables = array(); 
    $xarTables['bible_texts'] = $prefix . '_bible_texts';
    $xarTables['bible_aliases'] = $prefix . '_bible_aliases';

	// Create template table name for holding Bible texts.
	// This table is NOT created directly.
    $xarTables['bible_text'] = $prefix . '_bible_text';

    // Return the table information
    return $xarTables;
} 

?>
