<?php
/**
 * Standard function to get main menu links
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
 */
/*
 * Standard function to get main menu links
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */
function registration_userapi_getmenulinks()
{

    if (xarModGetVar('registration', 'allowregistration')){
    // Security check
		if (!xarUserIsLoggedIn()){
			$menulinks[] = array('url'   => xarModURL('registration',
													  'user',
													  'register'),
								 'title' => xarML('Register'),
								 'label' => xarML('Register'));
		}
    }
    if (xarModGetVar('registration', 'showprivacy')){
        $menulinks[] = array('url'   => xarModURL('registration',
                                                  'user',
                                                  'privacy'),
                             'title' => xarML('Privacy Policy for this Website'),
                             'label' => xarML('Privacy Policy'));
    }
    if (xarModGetVar('registration', 'showterms')){
        $menulinks[] = array('url'   => xarModURL('registration',
                                                  'user',
                                                  'terms'),
                             'title' => xarML('Terms of Use for this website'),
                             'label' => xarML('Terms of Use'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>