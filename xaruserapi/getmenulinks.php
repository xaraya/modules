<?php
/**
 * Standard function to get main menu links
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Authentication module
 */
/**
 * Standard function to get main menu links
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */
function authentication_userapi_getmenulinks()
{

    if (xarModGetVar('authentication', 'allowregistration')){
    // Security check
        if (!xarUserIsLoggedIn()){
            $menulinks[] = array('url'   => xarModURL('authentication',
                                                      'user',
                                                      'register'),
                                 'title' => xarML('Log in'),
                                 'label' => xarML('Log in'));
        }
    }
    if (xarModGetVar('authentication', 'showprivacy')){
        $menulinks[] = array('url'   => xarModURL('authentication',
                                                  'user',
                                                  'privacy'),
                             'title' => xarML('Privacy Policy for this Website'),
                             'label' => xarML('Privacy Policy'));
    }
    if (xarModGetVar('authentication', 'showterms')){
        $menulinks[] = array('url'   => xarModURL('authentication',
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