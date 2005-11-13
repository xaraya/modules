<?php
/**
 * Update configuration parameters of the module with information passed back by the modification form
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage maxercalls
 * @author Maxercalls module development team 
 */
/**
 * Update the configuration of the module
 *
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form.
 * Main item is the category for the calls
 *
 * @author MichelV
 */
function maxercalls_admin_updateconfig()
{

    if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;
    // Update module variables.
    xarModSetVar('maxercalls', 'itemsperpage', $itemsperpage);
    xarModSetVar('maxercalls', 'SupportShortURLs', $shorturls);

    xarModCallHooks('module','updateconfig','maxercalls',
                   array('module' => 'maxercalls'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('maxercalls', 'admin', 'modifyconfig'));

    // Return
    return true;
}
?>