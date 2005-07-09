<?php
/**
 * File: $Id: xarinit.php,v 1.3 2003/12/17 05:37:54 roger Exp $
 *
 * The authemail module lets you login with your email address
 *
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
    /* remove from authentication list */
    $authModules = array_filter(
            xarConfigGetVar('Site.User.AuthenticationModules'),
            create_function('$a', 'return $a != "authemail";')); 
    xarConfigSetVar('Site.User.AuthenticationModules', $authModules);

    return true;
}
?>
