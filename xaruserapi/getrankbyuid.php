<?php
/**
 * Get a rankname for a user
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Userpoints Module
 * @link http://xaraya.com/index.php/release/782.html
 * @author Mark Frawley
 */


/**
 * get a rankname for a user
 *
 * @author Mark Frawley
 * @param uid $uid uid of user to get rankname for
 * @return $user_rank string, or empty string on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function userpoints_userapi_getrankbyuid($args) 
{
    extract($args);

   // Argument check
    if (!isset($uid) || !is_numeric($uid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'rank ID', 'user', 'getrankbyuid', 'userpoints');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    if(isset($uid) && $uid!='') {
        $user_score = xarModAPIFunc('userpoints','user','get', array('uid' => $uid));
        $user_score = $user_score * 100;
    }

    $aRanks = xarModAPIFunc('userpoints','user','getallranks',array($args));
    $user_rank = '';
    $user_rid=0;
    $old_score = 0; //needed because we are doing a search

    foreach($aRanks as $rankRow) {
        $rid       = $rankRow['id'];
        $rankname = $rankRow['rankname'];
        $rankminscore = $rankRow['rankminscore'];
            
        if($user_score >= $rankminscore && $rankminscore >= $old_score) {

            $user_rank = $rankname;
            $user_rid = $rid; //rank id
            $old_score = $rankminscore;
        }
    }

    if (!xarSecurityCheck('ReadUserpointsRank', 1, 'Rank', "$user_rank:$user_rid")) {
        return;
    }
    return($user_rank);
      
}
?>