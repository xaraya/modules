<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
/**
 * Table definition functions
 *
 * Return crispBB module table names to xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded. It is loaded by xarMod__loadDbInfo().
 * @author crisp <crisp@crispcreations.co.uk>
 * @access private
 * @return array
 */
function crispbb_xartables()
{
    $xarTables = array();
    $prefix = xarDBGetSiteTablePrefix();

    $itemtypesTable = $prefix . '_crispbb_itemtypes';
    $forumsTable    = $prefix . '_crispbb_forums';
    $topicsTable    = $prefix . '_crispbb_topics';
    $postsTable     = $prefix . '_crispbb_posts';
    $hooksTable     = $prefix . '_crispbb_hooks';
    $postersTable   = $prefix . '_crispbb_posters';
    $xarTables['crispbb_itemtypes'] = $itemtypesTable;
    $xarTables['crispbb_forums']    = $forumsTable;
    $xarTables['crispbb_topics']    = $topicsTable;
    $xarTables['crispbb_posts']     = $postsTable;
    $xarTables['crispbb_hooks']     = $hooksTable;
    $xarTables['crispbb_posters']   = $postersTable;

    return $xarTables;
}
?>