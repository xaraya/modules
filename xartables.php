<?php

/**
 * File: $Id$
 *
 * Table information for comments utility module
 *
 * @package modules
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @author Carl P. Corliss <rabbitt@xaraya.com>
*/

function comments_xartables()
{
    // Initialise table array
    $xartable = array();

    // Name for template database entities
    $comments_table = xarDBGetSiteTablePrefix() . '_comments';

    // Table name
    $xartable['comments'] = $comments_table;

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
    // Return table information
    return $xartable;
}

?>