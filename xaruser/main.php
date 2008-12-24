<?php
/**
 * Default user function
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
 */
/**
 * the main user function
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments. Function decides if user is logged in
 * and returns user to correct location to register.
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 * @return bool
 */
function registration_user_main()
{
    $allowregistration = xarModVars::get('registration', 'allowregistration');

    if (xarUserIsLoggedIn()) {
        xarResponseRedirect(xarModURL('registration', 'user', 'terms'));

    } elseif ($allowregistration != true) {

        //Get default authentication module info for login
        $defaultauthdata     = xarModAPIFunc('roles','user','getdefaultauthdata');
        $defaultloginmodname = $defaultauthdata['defaultloginmodname'];

        xarResponseRedirect(xarModURL($defaultloginmodname, 'user', 'showloginform'));

    } else { //allow user to register
        $minage = xarModVars::get('registration', 'minage');
        if (($minage)>0) {
            xarResponseRedirect(xarModURL('registration','user','register', array('phase'=>'checkage')));
        }else{
            xarResponseRedirect(xarModURL('registration','user','register'));
        }
    }
    return true;
}
?>