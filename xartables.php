<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */

/**
 * Pass table names back to the framework
 * @return array
 */
function comments_xartables()
{
    // Initialise table array
    $xartable = array();

    // Name for template database entities
    $comments_table     = xarDB::getPrefix() . '_comments';
    $blacklist_table    = xarDB::getPrefix() . '_blacklist';

    // Table name
    $xartable['comments']   = $comments_table;
    $xartable['blacklist']  = $blacklist_table;

    // Return table information
    return $xartable;
}
?>