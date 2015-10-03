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
 * @author crisp <crisp@crispcreations.co.uk>
 * @access private
 * @return array
 */
function crispbb_xartables()
{
    $tables = array();
    $prefix = xarDB::getPrefix();
    $tables['crispbb_itemtypes']    = $prefix . '_crispbb_itemtypes';
    $tables['crispbb_forums']       = $prefix . '_crispbb_forums';
    $tables['crispbb_topics']       = $prefix . '_crispbb_topics';
    $tables['crispbb_posts']        = $prefix . '_crispbb_posts';
    $tables['crispbb_hooks']        = $prefix . '_crispbb_hooks';
    $tables['crispbb_posters']      = $prefix . '_crispbb_posters';
    return $tables;
}
?>