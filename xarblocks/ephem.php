<?php
/**
 * Ephemerids
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ephemerids Module
 * @link http://xaraya.com/index.php/release/15.html
 * @author Volodymyr Metenchuk
 */
/**
 * Ephemerids block
 */
function ephemerids_ephemblock_init()
{
    return true;
}

function ephemerids_ephemblock_info()
{
    return array('text_type' => 'Ephemerids',
    'module' => 'ephemerids',
    'text_type_long' => 'Ephemerids',
    'allow_multiple' => true,
    'form_content' => false,
    'form_refresh' => false,
    'show_preview' => true);
}

function ephemerids_ephemblock_display($blockinfo)
{
    // Security Check
    if(!xarSecurityCheck('ReadEphemerids', 0)) return;

    // Database information
    xarModDBInfoLoad('ephemerids');
    $dbconn =& xarDBGetConn();

    $xartable =& xarDBGetTables();
    $ephemtable = $xartable['ephem'];

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