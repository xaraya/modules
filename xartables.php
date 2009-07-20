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

    $itemtypesTable = xarDBGetSiteTablePrefix() . '_crispbb_itemtypes';
    $forumsTable    = xarDBGetSiteTablePrefix() . '_crispbb_forums';
    $topicsTable    = xarDBGetSiteTablePrefix() . '_crispbb_topics';
    $postsTable     = xarDBGetSiteTablePrefix() . '_crispbb_posts';
    $hooksTable     = xarDBGetSiteTablePrefix() . '_crispbb_hooks';

    $xarTables['crispbb_itemtypes'] = $itemtypesTable;
    $xarTables['crispbb_forums']    = $forumsTable;
    $xarTables['crispbb_topics']    = $topicsTable;
    $xarTables['crispbb_posts']     = $postsTable;
    $xarTables['crispbb_hooks']     = $hooksTable;

    return $xarTables;
}
?>