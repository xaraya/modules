<?php
/**
 * Get points
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Userpoints Module
 * @link http://xaraya.com/index.php/release/782.html
 * @author Userpoints Module Development Team
 */
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
