<?php

/**
 * the main user function
 */
function userpoints_user_main()
{
    // Return output
    if (!xarUserIsLoggedIn()) {
        return xarML('This module has no user interface *except* via display hooks');
    } else {
        // get the current score of the user
        $uid = xarUserGetVar('uid');
        $score = xarModAPIFunc('userpoints','user','get',
                               array('uid' => $uid));
        // get all ranks in descending order
        $ranks = xarModAPIFunc('userpoints','user','getallranks');
        // find out what rank matches
        $rank = '-';
        foreach ($ranks as $info) {
            if ($score >= $info['rankminscore']) {
                $rank = $info['rankname'];
                break;
            }
        }
        
        return array('score' => $score,
                     'rank' => $rank);
    }
}

?>
