<?php

/**
 * show results in display hook
 * @param $args['pid'] poll ID (from displayhook)
 * @param $args['returnurl'] return URL (from displayhook)
 */
function polls_user_resultshook($args)
{
    // Get parameters
    $pid = xarVarCleanFromInput('pid');

    // override with arguments here
    extract($args);

    if (!isset($pid)) {
        $msg = xarML('Missing poll ID');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }
    $canvote = xarModAPIFunc('polls', 'user', 'usercanvote', array('pid' => $pid));

    $data = array();

    // Get item
    $poll = xarModAPIFunc('polls',
                           'user',
                           'get',
                           array('pid' => $pid));

    if (!$poll) {
        $msg = xarML('Error retrieving Poll data');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }

    $data['pid'] = $poll['pid'];
    $data['title'] = $poll['title'];
    $data['private'] = $poll['private'];

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
    $barscale = xarModGetVar('polls', 'barscale');
    $imggraph = xarModGetVar('polls', 'imggraph');
    $data['imggraph'] = ($imggraph >= 2)?1:0;
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
