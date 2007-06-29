<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
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
    $xartable = array();

    // Name for template database entities
    $comments_table     = xarDBGetSiteTablePrefix() . '_comments';
    $blacklist_table    = xarDBGetSiteTablePrefix() . '_blacklist';

    // Table name
    $xartable['comments']   = $comments_table;
    $xartable['blacklist']  = $blacklist_table;

    // Column names
    $xartable['comments_column'] = array('cid'      => $comments_table . '.xar_cid',
                                         'pid'      => $comments_table . '.xar_pid',
                                         'modid'    => $comments_table . '.xar_modid',
                                         'itemtype' => $comments_table . '.xar_itemtype',
                                         'objectid' => $comments_table . '.xar_objectid',
                                         'cdate'    => $comments_table . '.xar_date',
                                         'author'   => $comments_table . '.xar_author',
                                         'title'    => $comments_table . '.xar_title',
                                         'hostname' => $comments_table . '.xar_hostname',
                                         'comment'  => $comments_table . '.xar_text',
                                         'left'     => $comments_table . '.xar_left',
                                         'right'    => $comments_table . '.xar_right',
                                         'status'   => $comments_table . '.xar_status',
                                         'postanon' => $comments_table . '.xar_anonpost'
                                        );

    // Column names
    $xartable['blacklist_column'] = array('id'       => $blacklist_table . '.xar_id',
                                          'pid'      => $blacklist_table . '.xar_domain'
                                          );
    return $xartable;
}
?>