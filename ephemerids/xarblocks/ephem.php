<?php
/**
 * File: $Id$
 * 
 * Ephemerids
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Ephemerids Module
 * @author Volodymyr Metenchuk
*/

function ephemerids_ephemblock_init()
{
    return true;    
}

function ephemerids_ephemblock_info()
{
    return array('text_type' => 'Ephemerids',
    'module' => 'articles',
    'text_type_long' => 'Ephemerids',
    'allow_multiple' => false,
    'form_content' => false,
    'form_refresh' => false,
    'show_preview' => true);
}

function ephemerids_ephemblock_display($blockinfo)
{
    // Security check
    if(!xarSecurityCheck('ReadArticlesBlock',1,'Block',$blockinfo['title'])) return;

    // Get database setup
    $dbconn         =& xarDBGetConn();
    $xartable       =& xarDBGetTables();
    $articlestable  = $xartable['articles'];
    $today          = date();

    $data['items'] = array();
    $data['emptycontent'] = false;

    $query = "SELECT xar_aid,
                   xar_title,
                   xar_summary,
                   xar_pubdate,
                   xar_pubtypeid,
                   xar_status,
                   xar_language
            FROM $articlestable
            WHERE xar_pubdate LIKE $today";
    $result =& $dbconn->SelectLimit($query);
    if (!$result) return;


    $data['items'] = $ephemlist;
    if (empty($data['items'])) {
        $data['emptycontent'] = true;
    }

    if (empty($blockinfo['title'])){
        $blockinfo['title'] = xarML('Historical Reference');
    }

    if (empty($blockinfo['template'])) {
        $template = 'ephem';
    } else {
        $template = $blockinfo['template'];
    }
    $blockinfo['content'] = xarTplBlock('ephemerids',$template, $data);

    return $blockinfo;
}
?>