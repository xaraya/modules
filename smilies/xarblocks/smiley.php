<?php
/**
 * File: $Id$
 * 
 * Xaraya Smilies
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Smilies Module
 * @author Jim McDonald, Mikespub, John Cox
*/

/**
 * Block init - holds security.
 */
function smilies_smileyblock_init()
{
    // Security
    return true;
}


/**
 * block information array
 */
function smilies_smileyblock_info()
{
    return array('text_type' => 'Smiley',
		 'text_type_long' => 'Show a random smiley, with description',
		 'module' => 'smilies',
		 'allow_multiple' => true,
		 'form_content' => false,
		 'form_refresh' => false,
		 'show_preview' => true);

}


function smilies_smileyblock_display($blockinfo)
{
    // Security Check
	if(!xarSecurityCheck('EditSmilies', 0)) return;

    if (empty($blockinfo['title'])){
        $blockinfor['title'] = xarML('I can not believe you use this block');
    }

    xarModDBInfoLoad('smilies');
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $smiliestable = $xartable['smilies'];

    // Query
    mt_srand((double)microtime()*1000000);
    $total_result = $dbconn->Execute("SELECT count(xar_sid) FROM $smiliestable");
    list($total) = $total_result->fields;
    if ($dbconn->ErrorNo() != 0) {
        return;
    }

    $p = mt_rand(0, ($total - 1));
    $query = "SELECT xar_code,
                     xar_icon,
                     xar_emotion   
                     FROM $smiliestable
                     ORDER by xar_sid";
    $result = $dbconn->SelectLimit($query, 1,$p);
    while(list($code, $icon, $emotion) = $result->fields) {
        $result->MoveNext();
        $content[] = array();
        if (empty($blockinfo['template'])) {
            $template = 'smiley';
        } else {
            $template = $blockinfo['template'];
        }
        $blockinfo['content'] = xarTplBlock('smilies',$template, array('title' => $blockinfo['title'],
                                                                       'code' => $code,
                                                                       'icon' => $icon,
                                                                       'emotion' => $emotion));
    }

    return $blockinfo;
}
?>