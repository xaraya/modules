<?php
/**
 * Define xarBB tables
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarbb Module
 * @link http://xaraya.com/index.php/release/300.html
 * @author John Cox
 */
/**
 * Define tables
 * @return array
 */
function xarbb_xartables()
{
    /* Initialise table array */
    $xartable = array();
    $prefix = xarDBGetSiteTablePrefix();
    /* Get the name for the autolinks item table */
    $xbbforums = $prefix . '_xbbforums';
    $xbbtopics = $prefix . '_xbbtopics';

    /* Set the table name */
    $xartable['xbbforums'] = $xbbforums;
    $xartable['xbbtopics'] = $xbbtopics;

    /* Name for template database entities */
    $comments_table = xarDBGetSiteTablePrefix() . '_comments';

    /* Table name */
    $xartable['comments'] = $comments_table;
    /* Column names */
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

    /* Return the table information */
    return $xartable;
}

?>