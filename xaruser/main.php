<?php
/**
 * Default user function
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Authentication module
 */
/**
 * the main user function
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments.  Function decides if user is logged in
 * and returns user to correct location.
 * @author  Marc Lutolf <marcinmilan@xaraya.com>
*/
function authentication_user_main()
{

    $allowregistration = xarModGetVar('authentication', 'allowregistration');

	if (xarUserIsLoggedIn()) {
	   xarResponseRedirect(xarModURL('authentication',
									 'user',
									 'terms'));
	} elseif ($allowregistration != true) {
		xarResponseRedirect(xarModURL('authentication',
									  'user',
									  'showloginform'));
	} else {
		xarResponseRedirect(xarModURL('authentication',
									  'user',
									  'register'));

	}
	return true;
}

?>