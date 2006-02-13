<?php
/**
 * Subscribe to Legis Hall Update
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
 * Subscribe Legis Users to A Hall Update
 *
 * Allows a Legis Admin or User to subscribe to specific Hall
 * @author jojodee
 */
function legis_userapi_subscribe($args)
{   extract($args);
    // Only Registered users can subscribe or add a subscribe
    if (!xarUserIsLoggedIn()) return;
    // We need to know which hall to subscribe to and which user
    if (!xarVarFetch('hallid', 'int:1:', $hallid, null, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('userid', 'int:1:', $userid, null, XARVAR_NOT_REQUIRED)) {return;}
    // We need to allow admin ONLY to set the subscription via checkbox
    // Or the user to set it in their account
    $uid = (int) xarUserGetVar('uid');

    //if (!xarSecurityCheck('DeleteLegis',1))  return; // fix this up
    if (!isset($userid) && isset($uid)) {
      $userid=$uid;
    } elseif (!isset($userid) && !isset($uid)) {
        return false; //put appropriate error message here
    }
    // If there are already legis subscribers for that hall update it else create the list
    $halllist = xarModGetVar('legis','subscribers_'.$hallid);
    if (isset($subscribers)) {
        $subscribers = unserialize($halllist);
        if (!isset($halllist)) {
            $halllist=array();
            array_push($hallist,$userid);
            $finalhalllist = serialize($halllist);
        } elseif (in_array($userid,$halllist)) {
            // user is already subscribed
            return true;
        }
     } else {
        $halllist = array($userid);
        $finalhalllist = serialize($halllist);
     }
    //reset the hall's modvar subscription list
    xarModSetVar('legis','subscribers_'.$hallid, $finalhalllist);

    return true;
}
?>