<?php
/**
 * File: $Id: s.xarinit.php 1.11 03/01/18 11:39:31-05:00 John.Cox@mcnabb. $
 * 
 * Xaraya Referers
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 * @subpackage Referer Module
 * @author John Cox et al. 
 */
function referer_admin_filter($args)
{ 
    // Get parameters from whatever input we need.
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return; 
    // Security Check
    if (!xarSecurityCheck('DeleteReferer')) return; 
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return; 
    // The API function is called.

    // Lets check the blacklist first before we process.
    // If the comment does not pass, we will return an exception
    // Perhaps in the future we can store the comment for later 
    // review, but screw it for now...
    if (xarModIsAvailable('comments')){
        if (xarModGetVar('comments', 'useblacklist') == true){
            $items = xarModAPIFunc('comments', 'user', 'get_blacklist');
            $referers = xarModAPIFunc('referer', 'user', 'getall'); 
            // Check for exceptions
            if (!isset($items)) return; // throw back
             
            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];
                foreach ($referers as $referer) {
                    if (preg_match("/$item[domain]/i", $referer['url'])){
                         if (!xarModAPIFunc('referer', 'admin', 'delete_one', array('rid' => $referer['rid']))) return; 
                    }
                }
            }
        }
    }
    xarResponseRedirect(xarModURL('referer', 'admin', 'view')); 
    // Return
    return true;
} 
?>