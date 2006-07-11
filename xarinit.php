<?php
/**
 * Email authentication module. Allows you to login with your email address
 * instead of username.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Authemail Module
 * @link http://xaraya.com/index.php/release/10513.html
 * @author Roger Keays <r.keays@ninthave.net>
 * Jan 02 2004
 */
/**
 * Initialize the authemail module
 */
function authemail_init()
{
    /* only ever use authemail authentication */
    $authmodules = xarConfigGetVar('Site.User.AuthenticationModules');
    array_unshift($authmodules, "authemail");
    xarConfigSetVar('Site.User.AuthenticationModules', $authmodules);

    return true;
} /* init */

/**
 * Delete the authemail module
 */
function authemail_delete()
{
    /* Cleanup - set the default authentication module back to authsystem, if it is set to authemail */
    $authemailid = xarModGetIDFromName('authemail');
    $authsystemid= xarModGetIDFromName('authsystem');
    if (xarModGetVar('roles','defaultauthmodule') == $authemailid) {
        xarModSetVar('roles','defaultauthmodule',$authsystemid);
    }

    /* remove from authentication list */
    $authModules = array_filter(
            xarConfigGetVar('Site.User.AuthenticationModules'),
            create_function('$a', 'return $a != "authemail";'));
    xarConfigSetVar('Site.User.AuthenticationModules', $authModules);

    return true;
}
?>