<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
/**
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('messages','admin','new') to create a new item
 * @param 'name' the name of the item to be created
 * @param 'number' the number of the item to be created
 */
function messages_admin_create($args)
{
    extract($args);
    if (!xarVarFetch('number',   'int:1:', $number,   $number, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name',     'str:1:', $name,     '', XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;

    $msg_id = xarModAPIFunc('messages', 'admin','create',
                        array('name' => $name,
                              'number' => $number));

    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required
    if (!isset($msg_id) && xarCurrentErrorType() != xar_NO_EXCEPTION) return; // throw back

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarRedirect(xarModURL('messages', 'admin', 'view'));

    // Return
    return true;
}

?>
