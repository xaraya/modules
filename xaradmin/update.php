<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
 *
 * @subpackage shouter
 * @link http://xaraya.com/index.php/release/236.html
 * @author Neil Whittaker
 */

/**
 * Process shout modify form
 */
function shouter_admin_update($args)
{ 
    extract($args);

    if (!xarVarFetch('shoutid', 'id', $shoutid, $shoutid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, $name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shout', 'str:1:', $shout, $shout, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $shoutid = $objectid;
    } 

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return; 

//    $invalid = array();
//    if (empty($name) || !is_string($name)) {
//        $invalid['name'] = 1;
//        $name = '';
//    }
    
//    if (empty($shout) || !is_numeric($shout)) {
//        $invalid['shout'] = 1;
//        $shout = '';
//    }

//    if (count($invalid) > 0) {
//        return xarModFunc('shouter', 'admin', 'modify',
//                          array('shoutid'   => $shoutid,
//                                'name'   => $name,
//                                'shout'  => $shout));
//    }

    if (!xarModAPIFunc('shouter', 'admin', 'update',
                 array('shoutid' => $shoutid,
                       'name'    => $name,
                       'shout'   => $shout))) {
        return;
    } 
    xarSessionSetVar('statusmsg', xarML('Shouter Item was successfully updated!')); 

    xarResponseRedirect(xarModURL('shouter', 'admin', 'view')); 
} 
?>
