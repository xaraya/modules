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

    $data = [];
    $now = time();
    $uid = xarUser::getVar('id');
    $tstatus = [0,1]; // open, closed

    //get forums
    $forums = xarMod::apiFunc(
        'crispbb',
        'user',
        'getforums',
        [
            'tstatus' => $tstatus,
            'privcheck' => true,
            'sort' => 'totals',
            'order' => 'DESC',
            'ftype' => 0,
            ]
    );
    // if the error was no privs, we should have an error message
    if (!empty($forums['error'])) {
        return xarTpl::module('privileges', 'user', 'errors', ['layout' => 'no_privileges']);
    }

    $tracker = unserialize(xarModUserVars::get('crispbb', 'tracker_object'));
    $data['userpanel'] = $tracker->getUserPanelInfo();
    // get forum categories
    $mastertype = xarMod::apiFunc(
        'crispbb',
        'user',
        'getitemtype',
        ['fid' => 0, 'component' => 'forum']
    );
    $basecats = xarMod::apiFunc('crispbb', 'user', 'getcatbases');
    $parentcat = count($basecats) > 0 ? $basecats[0] : 0;
    $categories = xarMod::apiFunc(
        'categories',
        'user',
        'getchildren',
        ['cid' => $parentcat]
    );
    if (!empty($categories)) {
        foreach ($categories as $cid => $category) {
            $catLevel = xarMod::apiFunc(
                'crispbb',
                'user',
                'getseclevel',
                ['catid' => $cid]
            );
            if ($catLevel < 200) { // No privs
                unset($categories[$cid]);
                continue;
            }
        }
    }

    $data['totalcats'] = count($categories);
    $data['totalforums'] = count($forums);
    $fids = array_keys($forums);
    $data['totaltopics'] = xarMod::apiFunc('crispbb', 'user', 'counttopics', ['fid' => $fids, 'tstatus' => $tstatus]);
    $totalposts = xarMod::apiFunc('crispbb', 'user', 'countposts', ['fid' => $fids, 'tstatus' => $tstatus, 'pstatus' => 0]);
    $data['totalposts'] = $totalposts - $data['totaltopics'];
    $data['totalunanswered'] = xarMod::apiFunc('crispbb', 'user', 'counttopics', ['fid' => $fids, 'tstatus' => $tstatus, 'noreplies' => true]);
    $data['totalusers'] = xarMod::apiFunc('roles', 'user', 'countall', ['include_anonymous' => false, 'include_myself' => false]);
    $allactive = xarMod::apiFunc('roles', 'user', 'countallactive', ['include_anonymous' => true, 'include_myself' => false]);
    $loggedin = xarMod::apiFunc('roles', 'user', 'countallactive', ['include_anonymous' => false, 'include_myself' => false]);
    $data['totalonline'] = $loggedin;
    $data['totalguests'] = $allactive - $loggedin;

    $lastuid = xarModVars::get('roles', 'lastuser');
    // Make sure we have a lastuser
    if (!empty($lastuid)) {
        if (!is_numeric($lastuid)) {
            //Remove this further down the line
            $lastuser = xarMod::apiFunc(
                'roles',
                'user',
                'get',
                ['uname' => $lastuid]
            );
        } else {
            $lastuser = xarMod::apiFunc(
                'roles',
                'user',
                'get',
                ['uid' => $lastuid]
            );
        }
        // Check return
        if ($lastuser) {
            $data['lastuser'] = $lastuser;
        }
    }

    $lastpost = xarMod::apiFunc('crispbb', 'user', 'getposts', ['fid' => $fids, 'tstatus' => $tstatus, 'sort' => 'ptime', 'order' => 'DESC', 'numitems' => 1, 'pstatus' => 0]);
    $data['lastpost'] = !empty($lastpost) ? reset($lastpost) : [];

    $data['topforums'] = array_slice($forums, 0, 10, true);

    $data['topstarters'] = xarMod::apiFunc('crispbb', 'user', 'getposters', ['sort' => 'numtopics', 'numitems' => 10]);
    $data['topposters'] = xarMod::apiFunc('crispbb', 'user', 'getposters', ['sort' => 'numreplies', 'numitems' => 10, 'fids' => $fids, 'tstatus' => $tstatus]);
    $data['topreplies'] = xarMod::apiFunc('crispbb', 'user', 'gettopics', ['fid' => $fids, 'tstatus' => $tstatus, 'sort' => 'numreplies', 'order' => 'DESC', 'numitems' => 10]);

    $data['tophits'] = xarMod::apiFunc('crispbb', 'user', 'gettopics', ['fid' => $fids, 'tstatus' => $tstatus, 'sort' => 'numhits', 'order' => 'DESC', 'numitems' => 10]);

    $pageTitle = xarML('Forum Statistics');

    $data['pageTitle'] = $pageTitle;


    xarTpl::setPageTitle(xarVar::prepForDisplay($pageTitle));

    return $data;
}
