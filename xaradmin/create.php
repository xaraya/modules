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
 * Create shout
 *
 * If redirect == '' that means we're posting via xmlhttprequest
 *
 * @todo find a better way to handle messages when posting via xmlhttprequest
 */
function shouter_admin_create($args)
{
    extract($args);

    if (!xarVarFetch('shout', 'str:1:', $shout, '', XARVAR_NOT_REQUIRED,XARVAR_PREP_TRIM)) return;
    if (!xarVarFetch('redirect', 'str:1:', $redirect, '', XARVAR_NOT_REQUIRED)) return;

    if (empty($shout)) {
        if (!empty($redirect)) {
            xarResponseRedirect($redirect);
        } else {
            return xarML('Nothing to shout about');
        }

    }

    $invalid = array();
    if (empty($shout) || !is_string($shout)) {
        $invalid['shout'] = 1;
        return;
    }

    // the Module Developers Guide says to NEVER use 'uname', but rather to use 'name'
    // when it is necessary to display the users screen name.... hence the next line.
    $name = xarUserGetVar('name');

    /* I will add 'please enter text before hitting the button stuff here */
    // check if we have any errors
    if (count($invalid) > 0) {
        return xarModFunc('shouter', 'admin', 'new',
                    array('name' => $name,
                          'shout' => $shout,
                          'invalid'  => $invalid));
    }
    // Confirm authorisation code.
    //if (!xarSecConfirmAuthKey()) return;

    $shoutid = xarModAPIFunc('shouter', 'admin', 'create',
                       array('name' => $name,
                             'date' => time(),
                             'shout' => $shout));

    if (!isset($shoutid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!empty($redirect)) {
        xarResponseRedirect($redirect);
    } else {
        return '';
    }
}
?>
