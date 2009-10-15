<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 *//**
 * Do something
 *
 * Standard function
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 * @throws none
 */
function crispbb_user_stats($args)
{
    extract($args);

    $data = array();
    $now = time();
    $uid = xarUserGetVar('id');
    $tstatus = array(0,1); // open, closed

    //get forums
    $forums = xarMod::apiFunc('crispbb', 'user', 'getforums',
        array(
            'tstatus' => $tstatus,
            'privcheck' => true,
            'sort' => 'totals',
            'order' => 'DESC',
            'ftype' => 0
            ));
    // if the error was no privs, we should have an error message
    if (!empty($forums['error'])) {
            $msg = xarML('You do not have the privileges required for this action');
            $errorMsg['message'] = $msg;
            $errorMsg['return_url'] = xarServer::getBaseURL();
            $errorMsg['type'] = $forums['error'];
            $errorMsg['pageTitle'] = xarML('No Privileges');
            xarTPLSetPageTitle(xarVarPrepForDisplay($errorMsg['pageTitle']));
            return xarTPLModule('crispbb', 'user', 'error', $errorMsg);
    }

    $tracker = unserialize(xarModUserVars::get('crispbb', 'tracker_object'));
    $data['userpanel'] = $tracker->getUserPanelInfo();
    // get forum categories
    $mastertype = xarMod::apiFunc('crispbb', 'user', 'getitemtype',
        array('fid' => 0, 'component' => 'forum'));
    $basecats = xarMod::apiFunc('categories','user','getallcatbases',array('module' => 'crispbb'));
    $parentcat = count($basecats) > 0 ? $basecats[0]['category_id'] : null;
    $categories = xarMod::apiFunc('categories', 'user', 'getchildren',
        array('cid' => $parentcat));
    if (!empty($categories)) {
        foreach ($categories as $cid => $category) {
            $catLevel = xarMod::apiFunc('crispbb', 'user', 'getseclevel',
                array('catid' => $cid));
            if ($catLevel < 200) { // No privs
                unset($categories[$cid]);
                continue;
            }
        }
    }

    $data['totalcats'] = count($categories);
    $data['totalforums'] = count($forums);
    $fids = array_keys($forums);
    $data['totaltopics'] = xarMod::apiFunc('crispbb', 'user','counttopics', array('fid' => $fids, 'tstatus' => $tstatus));
    $totalposts = xarMod::apiFunc('crispbb', 'user', 'countposts', array('fid' => $fids, 'tstatus' => $tstatus, 'pstatus' => 0));
    $data['totalposts'] = $totalposts - $data['totaltopics'];
    $data['totalunanswered'] = xarMod::apiFunc('crispbb', 'user', 'counttopics', array('fid' => $fids, 'tstatus' => $tstatus, 'noreplies' => true));
    $data['totalusers'] = xarMod::apiFunc('roles', 'user', 'countall', array('include_anonymous' => false, 'include_myself' => false));
    $allactive = xarMod::apiFunc('roles', 'user', 'countallactive', array('include_anonymous' => true, 'include_myself' => false));
    $loggedin = xarMod::apiFunc('roles', 'user', 'countallactive', array('include_anonymous' => false, 'include_myself' => false));
    $data['totalonline'] = $loggedin;
    $data['totalguests'] = $allactive - $loggedin;

    $lastuid = xarModVars::get('roles', 'lastuser');
    // Make sure we have a lastuser
    if (!empty($lastuid)) {
        if(!is_numeric($lastuid)) {
        //Remove this further down the line
            $lastuser = xarMod::apiFunc(
            'roles', 'user', 'get',
            array('uname' => $lastuid)
            );

        } else {
            $lastuser = xarMod::apiFunc(
            'roles', 'user', 'get',
            array('uid' => $lastuid)
            );

        }
         // Check return
         if ($lastuser) {$data['lastuser'] = $lastuser;}
    }

    $lastpost = xarMod::apiFunc('crispbb', 'user', 'getposts', array('fid' => $fids, 'tstatus' => $tstatus, 'sort' => 'ptime', 'order' => 'DESC', 'numitems' => 1, 'pstatus' => 0));
    $data['lastpost'] = !empty($lastpost) ? reset($lastpost) : array();

    $data['topforums'] = array_slice($forums, 0, 10, true);

    $data['topstarters'] = xarMod::apiFunc('crispbb', 'user', 'getposters', array('sort' => 'numtopics', 'numitems' => 10));
    $data['topposters'] = xarMod::apiFunc('crispbb', 'user', 'getposters', array('sort' => 'numreplies', 'numitems' => 10, 'fids' => $fids, 'tstatus' => $tstatus));
    $data['topreplies'] = xarMod::apiFunc('crispbb', 'user', 'gettopics', array('fid' => $fids, 'tstatus' => $tstatus, 'sort' => 'numreplies', 'order' => 'DESC', 'numitems' => 10));

    $data['tophits'] = xarMod::apiFunc('crispbb', 'user', 'gettopics', array('fid' => $fids, 'tstatus' => $tstatus, 'sort' => 'numhits', 'order' => 'DESC', 'numitems' => 10));

    $pageTitle = xarML('Forum Statistics');

    $data['pageTitle'] = $pageTitle;


    xarTplSetPageTitle(xarVarPrepForDisplay($pageTitle));

    return $data;
}
?>