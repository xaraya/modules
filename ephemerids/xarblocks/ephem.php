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
    'module' => 'Ephemerids',
    'text_type_long' => 'Ephemerids',
    'allow_multiple' => false,
    'form_content' => false,
    'form_refresh' => false,
    'show_preview' => true);
}

function ephemerids_ephemblock_display($blockinfo)
{
    // Database information
    xarModDBInfoLoad('Ephemerids');
    $dbconn =& xarDBGetConn();

    $xartable =& xarDBGetTables();
    $ephemtable = $xartable['ephem'];

    // Security Check
    if(!xarSecurityCheck('ReadEphemerids', 0)) return;

    $data['items'] = array();
    $data['emptycontent'] = false;

    // The admin API function is called. 
    $ephemlist = xarModAPIFunc('ephemerids',
                               'user',
                               'getalltoday');

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