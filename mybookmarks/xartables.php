<?php
/**
 * xartables.php
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage My Bookmarks module
 * @author Scot Gardner
 */

/**
 * Return MyBookmarks table names to xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 *
 * @access private
 * @return array
 */
function mybookmarks_xartables()
{
    // Initialise table array
    $xartables = array();
    $mybookmarkstable = xarDBGetSiteTablePrefix() . '_mybookmarks';
    $xartables['mybookmarks'] = $mybookmarkstable;
    return $xartables;
}
?>