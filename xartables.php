<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
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

    // Column names
    $xartable['comments_column'] = array('id'      => $comments_table . '.id',
                                         'pid'      => $comments_table . '.pid',
                                         'modid'    => $comments_table . '.modid',
                                         'itemtype' => $comments_table . '.itemtype',
                                         'objectid' => $comments_table . '.objectid',
                                         'cdate'    => $comments_table . '.date',
                                         'author'   => $comments_table . '.author',
                                         'title'    => $comments_table . '.title',
                                         'hostname' => $comments_table . '.hostname',
                                         'comment'  => $comments_table . '.text',
                                         'left'     => $comments_table . '.cleft',
                                         'right'    => $comments_table . '.cright',
                                         'status'   => $comments_table . '.status',
                                         'postanon' => $comments_table . '.anonpost'
                                        );

    // Column names
    $xartable['blacklist_column'] = array('id'       => $blacklist_table . '.id',
                                          'pid'      => $blacklist_table . '.domain'
                                          );
    // Return table information
    return $xartable;
}
?>