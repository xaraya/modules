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
    if (!xarVarFetch('pid', 'id', $pid, NULL, XARVAR_NOT_REQUIRED)) {return;}

    // override with arguments here
    extract($args);

    if (!isset($pid)) {
                throw new EmptyParameterException($pid,'Poll id must be set');
      }

    $canvote = xarModAPIFunc('polls', 'user', 'usercanvote', array('pid' => $pid));

    $data = array();

    // Get item
    $poll = xarModAPIFunc('polls',
                           'user',
                           'get',
                           array('pid' => $pid));

    if (!$poll) {
        throw new EmptyParameterException($pid,'Error retrieving Poll data, poll id (#(1)) not found');
    }

    $data['pid'] = $poll['pid'];
    $data['title'] = $poll['title'];
    $data['private'] = $poll['private'];
    $data['open'] = $poll['open'];

    // Number of participants
    $data['totalvotes'] = $poll['votes'];
    $data['options'] = array();
    if (!empty($returnurl)) {
        $data['voteurl'] = $returnurl;
    } elseif ($poll['modid'] != xarModGetIDFromName('polls') && !empty($poll['itemid'])) {
        $modinfo = xarModGetInfo($poll['modid']);
        if (!empty($modinfo)) {
            $itemlinks = xarModAPIFunc($modinfo['name'],'user','getitemlinks',
                                       array('itemtype' => $poll['itemtype'],
                                             'itemids' => array($poll['itemid'])),
                                       // don't throw an exception if this function doesn't exist
                                       0);
            if (!empty($itemlinks) && !empty($itemlinks[$poll['itemid']])) {
                $data['voteurl'] = $itemlinks[$poll['itemid']]['url'];
                $data['itemtitle'] = $itemlinks[$poll['itemid']]['label'];
            }
        }
    }
    if (empty($data['voteurl'])) {
        // fall back to standard display if necessary
        $data['voteurl'] = xarModURL('polls', 'user', 'display',
                                     array('pid' => $pid));
    }

    $data['canvote'] = $canvote;
    $barscale = xarModVars::Get('polls', 'barscale');
    $imggraph = xarModVars::Get('polls', 'imggraph');
    $data['imggraph'] = ($imggraph >= 2)?1:0;
    $data['showtotalvotes'] = xarModVars::Get('polls', 'showtotalvotes');
    $voteinterval = xarModVars::Get('polls', 'voteinterval');

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

    // Poll information
    for ($i=1; $i<=$poll['opts']; $i++) {
        if ($poll['votes'] == 0) {
            $percentage = 0;
        } else {
            $percentage = (int)($poll['options'][$i]['votes']*1000/$poll['votes']);
            $percentage /= 10;
        }

        $row = array();
        $row['name'] = $poll['options'][$i]['name'];
        $row['votes'] = $poll['options'][$i]['votes'];
        $row['percentage'] = $percentage;
        $row['barwidth'] = (int)$percentage * $barscale;
        $data['options'][$i] = $row;
    }

/* no hook calls inside hook calls :-) */

    // Return output
    return $data;
}

?>
