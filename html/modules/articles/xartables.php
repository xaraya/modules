<?php
/**
 * File: $Id: s.xaradmin.php 1.28 03/02/08 17:38:40-05:00 John.Cox@mcnabb. $
 *
 * Articles System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage articles module
 * @author mikespub
*/

function articles_xartables()
{
    // Initialise table array
    $xartable = array();
    // Get Site Prefix
    $sitePrefix = xarDBGetSiteTablePrefix();
    // Name for articles database entities
    $articles = $sitePrefix . '_articles';

    // Table name
    $xartable['articles'] = $articles;

    // Column names
    $xartable['articles_column'] = array('aid'      => $articles . '.xar_aid',
                                        'title'    => $articles . '.xar_title',
                                        'summary'  => $articles . '.xar_summary',
                                        'authorid' => $articles . '.xar_authorid',
                                        'pubdate'  => $articles . '.xar_pubdate',
                                        'pubtypeid' => $articles . '.xar_pubtypeid',
                                        'pages'    => $articles . '.xar_pages',
                                        'body'     => $articles . '.xar_body',
                                        'notes'    => $articles . '.xar_notes',
                                        'status'   => $articles . '.xar_status',
                                        'language' => $articles . '.xar_language');

    // Name for publication types table
    $publicationtypes = $sitePrefix . '_publication_types';

    // Table name
    $xartable['publication_types'] = $publicationtypes;

    // Column names
    $xartable['publication_types_column'] = array(
                'pubtypeid'      => $publicationtypes . '.xar_pubtypeid',
                'pubtypename'    => $publicationtypes . '.xar_pubtypename',
                'pubtypedescr'   => $publicationtypes . '.xar_pubtypedescr',
                'pubtypeconfig'   => $publicationtypes . '.xar_pubtypeconfig');

    // Return table information
    return $xartable;
}

?>
