<?php
/**
 * Polls Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * show results in display hook
 * @param $args['pid'] poll ID (from displayhook)
 * @param $args['returnurl'] return URL (from displayhook)
 */
function polls_user_resultshook($args)
{
    // Get parameters
    if (!xarVarFetch('pid', 'id', $pid, NULL, XARVAR_NOT_REQUIRED)) return;

    // override with arguments here
    extract($args);

    if (!isset($pid)) {
        $msg = xarML('Missing poll ID');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
        return;
    }

    // Get item
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    if (!$poll) {
        $msg = xarML('Error retrieving Poll data');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA', new DefaultUserException($msg));
        return;
    }

    $canvote = xarModAPIFunc('polls', 'user', 'usercanvote', array('poll' => $poll));

    $data = $poll;

    // Number of participants
    $data['totalvotes'] = $poll['votes'];

    if (!empty($returnurl)) {
        $data['voteurl'] = $returnurl;
    } elseif ($poll['modid'] != xarModGetIDFromName('polls') && !empty($poll['itemid'])) {
        $modinfo = xarModGetInfo($poll['modid']);
        if (!empty($modinfo)) {
            // don't throw an exception if this function doesn't exist
            $itemlinks = xarModAPIFunc(
                $modinfo['name'], 'user', 'getitemlinks',
                array('itemtype' => $poll['itemtype'], 'itemids' => array($poll['itemid'])), 0
            );

            if (!empty($itemlinks) && !empty($itemlinks[$poll['itemid']])) {
                $data['voteurl'] = $itemlinks[$poll['itemid']]['url'];
                $data['itemtitle'] = $itemlinks[$poll['itemid']]['label'];
            }
        }
    }
    if (empty($data['voteurl'])) {
        // fall back to standard display if necessary
        $data['voteurl'] = xarModURL('polls', 'user', 'display', array('pid' => $pid));
    }

    $data['canvote'] = $canvote;

    $imggraph = xarModGetVar('polls', 'imggraph');
    $data['imggraph'] = ($imggraph >= 2)?1:0;

    $data['showtotalvotes'] = xarModGetVar('polls', 'showtotalvotes');
    $voteinterval = xarModGetVar('polls', 'voteinterval');

    if($voteinterval == 86400){
        $data['votelimit'] = xarML('per day');
    }
    elseif($voteinterval == 604800){
        $data['votelimit'] = xarML('per week');
    }
    elseif($voteinterval == 2592000){
        $data['votelimit'] = xarML('per month');
    }
    else{
        $data['votelimit'] = xarML('per user');
    }


    // no hook calls inside hook calls :-)

    // Return data to template.
    return $data;
}

?>
