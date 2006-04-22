<?php
/**
 * Default user function
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
 */
/**
 * the main user function
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments.  Function decides if user is logged in
 * and returns user to correct location.
 * @author  Marc Lutolf <marcinmilan@xaraya.com>
*/
function registration_user_main()
{

    $allowregistration = xarModGetVar('registration', 'allowregistration');

	if (xarUserIsLoggedIn()) {
	   xarResponseRedirect(xarModURL('registration',
									 'user',
									 'terms'));
	} elseif ($allowregistration != true) {
        $authenticationmod=xarModGetNameFromId(xarModGetVar('roles','defaultauthmodule'));
		xarResponseRedirect(xarModURL($authenticationmod,
									  'user',
									  'showloginform'));
	} else { //allow user to register
        $minage = xarModGetVar('registration', 'minage');
		if (($minage)>0) {
            xarResponseRedirect(xarModURL('registration','user','register', array('phase'=>'checkage')));
        }else{
            xarResponseRedirect(xarModURL('registration','user','register'));
        }
	}
	return true;
}

?>