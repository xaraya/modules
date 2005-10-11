<?php
/**
 * Standard function to update module configuration parameters
 *
*
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Lists Module
 * @link http://xaraya.com/index.php/release/46.html
 * @author Jason Judge
 */

/**
 * Standard function to update module configuration parameters
 *
 * @lists Example module development team
 */
function lists_admin_updateconfig()
{
    /* Get parameters from whatever input we need.  All arguments to this
     * function should be obtained from xarVarFetch(). xarVarFetch allows
     * the checking of the input variables as well as setting default
     * values if needed.  Getting vars from other places such as the
     * environment is not allowed, as that makes assumptions that will
     * not hold in future versions of Xaraya
     */
    if (!xarVarFetch('bold',         'checkbox', $bold, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemsperpage', 'int',      $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    //if (!xarVarFetch('shorturls',    'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;

    /* Confirm authorisation code.  This checks that the form had a valid
     * authorisation code attached to it.  If it did not then the function will
     * proceed no further as it is possible that this is an attempt at sending
     * in false data to the system
     */

    if (!xarSecConfirmAuthKey()) return;
    /* Update module variables.  Note that the default values are set in
     * xarVarFetch when recieving the incoming values, so no extra processing
     * is needed when setting the variables here.
     */
    xarModSetVar('lists', 'bold', $bold);
    xarModSetVar('lists', 'itemsperpage', $itemsperpage);
    //xarModSetVar('lists', 'SupportShortURLs', $shorturls);

    xarModCallHooks('module','updateconfig','lists',
                   array('module' => 'lists'));

    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('lists', 'admin', 'modifyconfig'));

    /* Return */
    return true;
}
?>