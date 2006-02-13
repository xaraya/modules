<?php
/**
 * Unsubscribe to Legis Hall Update
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author jojodee
 */

/**
 * Unsubscribe Legis Users to A Hall Update
 *
 * Allows a Legis Admin or User to subscribe to specific Hall
 * @author jojodee
 */
function legis_userapi_unsubscribe($args)
{ extract($args);
    // Don't allow any non logged in user to unsubscribe
    if (!xarUserIsLoggedIn()) return;

    //if (!xarSecurityCheck('DeleteLegis',1)) return; //fix this for Legis admin and users

     // We need to know which hall to unsubscribe to and which user
    if (!xarVarFetch('hallid', 'int:1:', $hallid, $hallid, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('userid', 'int:1:', $userid, $userid, XARVAR_NOT_REQUIRED)) {return;}

    $uid = (int) xarUserGetVar('uid');
    if (!isset($userid) && isset($uid)) {
      $userid=$uid;
    } elseif (!isset($userid) && !isset($uid)) {
        return false; //put appropriate error message here
    }
    // If there are already subscribers for this hall list we need to update that array
    $halllist = xarModGetVar('legis','subscribers_'.$hallid);
    if (isset($halllist)) {
        $subscribers = unserialize($halllist);
        if (!is_array($subscribers) || !in_array($userid,$subscribers)) {
        // no subscription here
            return true;
        } else {
            // Remove user from the subscribe list
            $uniquesubscribers = array_flip($subscribers);
            unset($uniquesubscribers[$userid]);
            $subscribers = array_keys($uniquesubscribers);
            $finalhalllist= serialize($subscribers);
        }
    } else {
       // no subscription here
            return true;
    }
   //reset the hall's modvar subscription list
    xarModSetVar('legis','subscribers_'.$hallid, $finalhalllist);
    return true;
}
?>
