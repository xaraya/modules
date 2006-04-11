<?php
/**
 * Subscribe to a forum
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_user_viewforumrss()
{
    if (!xarVarFetch('fid', 'int:1', $fid)) return;

    // The user API function is called.
    $data = xarModAPIFunc('xarbb', 'user', 'getforum', array('fid' => $fid));
    if (empty($data)) return;
    // Security Check
    if(!xarSecurityCheck('ReadxarBB', 1, 'Forum', $data['catid'] . ':' . $data['fid'])) return;

    // The user API function is called
    $topics = xarModAPIFunc('xarbb', 'user', 'getalltopics', array('fid' => $fid));

    $totaltopics=count($topics);

    for ($i = 0; $i < $totaltopics; $i++) {
        $topic = $topics[$i];
        $topics[$i]['tpostrss'] = xarVarPrepForDisplay($topic['tpost']);
        $topics[$i]['tpost'] = xarVarPrepHTMLDisplay($topic['tpost']);
    }

    // Add the array of items to the template variables
    $data['fid'] = $fid;
    $data['items'] = $topics;
    xarTplSetPageTitle(xarVarPrepForDisplay($data['fname']));
    // Return the template variables defined in this function
    return $data;
}

?>