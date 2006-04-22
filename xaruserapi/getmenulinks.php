<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_userapi_getmenulinks()
{
    $menulinks = array();

    $forums = xarModAPIFunc('xarbb', 'user', 'getallforums');

    // Get the category assignments for these forums
    $fidlist = array();
    foreach ($forums as $forum) {
        $fidlist[] = $forum['fid'];
    }
    $cids = xarModAPIFunc('categories', 'user', 'getlinks',
        array('iids' => $fidlist, 'reverse' => 1, 'modid' => xarModGetIDFromName('xarbb'))
    );

    foreach ($forums as $forum) {
        $pass = true;
        if (isset($cids[$forum['fid']]) && count($cids[$forum['fid']]) > 0) {
            // Note: if forums are assigned to more than 1 category (= future), then we need read access to all here
            foreach ($cids[$forum['fid']] as $cid) {
                if (!xarSecurityCheck('ReadxarBB', 0, 'Forum', $cid . ':' . $forum['fid'])) {
                    $pass = false;
                    break;
                }
            }
        } elseif (!xarSecurityCheck('ReadxarBB', 0, 'Forum', 'All:' . $forum['fid'])) {
            $pass = false;
        }

        if($pass) {
            $menulinks[] = array(
                'url'   => xarModURL('xarbb', 'user', 'viewforum', array('fid' => $forum['fid'])),
                'title' => $forum['fname'],
                'label' => $forum['fname']
            );
        }
    }

    return $menulinks;
}

?>