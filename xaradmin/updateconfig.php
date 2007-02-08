<?php
/**
 * Update configuration
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 * @return bool true on success of update
 */
function dyn_example_admin_updateconfig()
{
/* we'll let our dynamic module settings be handled by DD here (optional)

    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarFetch(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    if (!xarVarFetch('bold', 'checkbox', $bold, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;

    // Update module variables.  Note that depending on the HTML structure used
    // to obtain the information from the user it is possible that the values
    // might be unset, so it is important to check them all and assign them
    // default values if required
    if (!isset($bold)) {
        $bold = 0;
    }
    xarModSetVar('dyn_example', 'bold', $bold);

    if (!isset($itemsperpage) || !is_numeric($itemsperpage)) {
        $itemsperpage = 10;
    }
    xarModSetVar('dyn_example', 'itemsperpage', $itemsperpage);

    if (!isset($shorturls)) {
        $shorturls = 0;
    }
    xarModSetVar('dyn_example', 'SupportShortURLs', $shorturls);
*/

// TODO: fix xarResponseRedirect so that it doesn't exit anymore,
//       and move this below the update function itself again
    xarModCallHooks('module','updateconfig','dyn_example',
                    array('module' => 'dyn_example'));

    if (!xarModFunc('dynamicdata','admin','update')) return; // throw back

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('dyn_example', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>
