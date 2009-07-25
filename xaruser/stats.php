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
    $uid = xarUserGetVar('uid');
    $tstatus = array(0,1,2); // open, closed, reported

    //get forums
    $forums = xarModAPIFunc('crispbb', 'user', 'getforums',
        array(
            'tstatus' => $tstatus,
            'privcheck' => true,
            'sort' => 'totals',
            'order' => 'DESC',
            ));
    // if the error was no privs, we should have an error message
    if (!empty($forums['error'])) {
            $msg = xarML('You do not have the privileges required for this action');
            $errorMsg['message'] = $msg;
            $errorMsg['return_url'] = xarServerGetBaseURL();
            $errorMsg['type'] = $forums['error'];
            $errorMsg['pageTitle'] = xarML('No Privileges');
            xarTPLSetPageTitle(xarVarPrepForDisplay($errorMsg['pageTitle']));
            return xarTPLModule('crispbb', 'user', 'error', $errorMsg);
    }
    $tracking = xarModAPIFunc('crispbb', 'user', 'tracking', array('now' => $now));

    // get forum categories
    $mastertype = xarModAPIFunc('crispbb', 'user', 'getitemtype',
        array('fid' => 0, 'component' => 'forum'));
    $mastercids = xarModGetVar('crispbb', 'mastercids.'.$mastertype);
    $parentcat = array_shift(explode(';', $mastercids));
    $categories = xarModAPIFunc('categories', 'user', 'getchildren',
        array('cid' => $parentcat));
    if (!empty($categories)) {
        foreach ($categories as $cid => $category) {
            $catLevel = xarModAPIFunc('crispbb', 'user', 'getseclevel',
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
    $data['totaltopics'] = xarModAPIFunc('crispbb', 'user','counttopics', array('fid' => $fids, 'tstatus' => $tstatus));
    $totalposts = xarModAPIFunc('crispbb', 'user', 'countposts', array('fid' => $fids, 'tstatus' => $tstatus));
    $data['totalposts'] = $totalposts - $data['totaltopics'];
    $data['totalunanswered'] = xarModAPIFunc('crispbb', 'user', 'counttopics', array('fid' => $fids, 'tstatus' => $tstatus, 'noreplies' => true));
    $data['totalusers'] = xarModAPIFunc('roles', 'user', 'countall', array('include_anonymous' => false, 'include_myself' => false));
    $allactive = xarModAPIFunc('roles', 'user', 'countallactive', array('include_anonymous' => true, 'include_myself' => false));
    $loggedin = xarModAPIFunc('roles', 'user', 'countallactive', array('include_anonymous' => false, 'include_myself' => false));
    $data['totalonline'] = $loggedin;
    $data['totalguests'] = $allactive - $loggedin;

    $lastuid = xarModGetVar('roles', 'lastuser');
    // Make sure we have a lastuser
    if (!empty($lastuid)) {
        if(!is_numeric($lastuid)) {
        //Remove this further down the line
            $lastuser = xarModAPIFunc(
            'roles', 'user', 'get',
            array('uname' => $lastuid)
            );

        } else {
            $lastuser = xarModAPIFunc(
            'roles', 'user', 'get',
            array('uid' => $lastuid)
            );

        }
         // Check return
         if ($lastuser) {$data['lastuser'] = $lastuser;}
    }

    $lastpost = xarModAPIFunc('crispbb', 'user', 'getposts', array('fid' => $fids, 'tstatus' => $tstatus, 'sort' => 'ptime', 'order' => 'DESC', 'numitems' => 1));
    $data['lastpost'] = !empty($lastpost) ? reset($lastpost) : array();

    $data['topforums'] = array_slice($forums, 0, 10, true);

    $data['topstarters'] = xarModAPIFunc('crispbb', 'user', 'getposters', array('sort' => 'numtopics', 'numitems' => 10, 'fids' => $fids, 'tstatus' => $tstatus));
    $data['topposters'] = xarModAPIFunc('crispbb', 'user', 'getposters', array('sort' => 'numreplies', 'numitems' => 10, 'fids' => $fids, 'tstatus' => $tstatus));
    $data['topreplies'] = xarModAPIFunc('crispbb', 'user', 'gettopics', array('fid' => $fids, 'tstatus' => $tstatus, 'sort' => 'numreplies', 'order' => 'DESC', 'numitems' => 10));

    $data['tophits'] = xarModAPIFunc('crispbb', 'user', 'gettopics', array('fid' => $fids, 'tstatus' => $tstatus, 'sort' => 'numhits', 'order' => 'DESC', 'numitems' => 10));

    $pageTitle = xarML('Forum Statistics');

    $data['pageTitle'] = $pageTitle;

    // End Tracking
    if (!empty($tracking)) {
        $data['lastvisit'] = $tracking['0']['lastvisit'];
        $data['visitstart'] = $tracking[0]['visitstart'];
        $data['totalvisit'] = $tracking[0]['totalvisit'];
        xarVarSetCached('Blocks.crispbb', 'tracking', $tracking);
        xarModSetUserVar('crispbb', 'tracking', serialize($tracking));
    }

    xarTplSetPageTitle(xarVarPrepForDisplay($pageTitle));

    return $data;
}
?>