<?php
/**
 * Standard function to Delete and item
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * Standard function to Delete an item
 *
 * This is a standard function that is called whenever an administrator
 * wishes to delete a current module item. Note that this function is
 * the equivalent of both of the modify() and update() functions above as
 * it both creates a form and processes its output. This is fine for
 * simpler functions, but for more complex operations such as creation and
 * modification it is generally easier to separate them into separate
 * functions. There is no requirement in the Xaraya MDG to do one or the
 * other, so either or both can be used as seen appropriate by the module
 * developer
 *
 * @author Example Module Development Team
 * @param  int    $args['exid']    the id of the item to be deleted
 * @param  string $args['confirm'] confirm that this item can be deleted
 */
function example_admin_delete($args)
{
    /* Admin functions of this type can be called by other modules. If this
     * happens then the calling module will be able to pass in arguments to
     * this function through the $args parameter. Hence we extract these
     * arguments *before* we have obtained any form-based input through
     * xarVarFetch(), so that parameters passed by the modules can also be
     * checked by a certain validation.
     */
    extract($args);

    /* Get parameters from whatever input we need. All arguments to this
     * function should be obtained from xarVarFetch(). xarVarFetch allows
     * the checking of the input variables as well as setting default values
     * if needed. Getting vars from other places such as the environment
     * is not allowed, as that makes assumptions that will not hold in
     * future versions of Xaraya
     */
    if (!xarVarFetch('exid',     'id', $exid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',  'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;
    /* At this stage we check to see if we have been passed $objectid, the
     * generic item identifier. This could have been passed in by a hook or
     * through some other function calling this as part of a larger module, but
     * if it exists it overrides $exid
     */

    /* Note that this module could just use $objectid everywhere to avoid all
     * of this munging of variables, but then the resultant code is less
     * descriptive, especially where multiple objects are being used. The
     * decision of which of these ways to go is up to the module developer
     */
    if (!empty($objectid)) {
        $exid = $objectid;
    }
    /* The user API function is called. This takes the item ID which we
     * obtained from the input and gets us the information on the appropriate
     * item. If the item does not exist we post an appropriate message and
     * return
     */
    $item = xarModAPIFunc('example',
        'user',
        'get',
        array('exid' => $exid));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing. However,
     * in this case we had to wait until we could obtain the item name to
     * complete the instance information so this is the first chance we get to
     * do the check
     */
    if (!xarSecurityCheck('DeleteExample', 1, 'Item', "$item[name]:All:$exid")) {
        return;
    }
    /* Check for confirmation. */
    if (empty($confirm)) {
        /* No confirmation yet - display a suitable form to obtain confirmation
         * of this action from the user
         * Initialise the $data variable that will hold the data to be used in
         * the blocklayout template, and get the common menu configuration - it
         * helps if all of the module pages have a standard menu at the top to
         * support easy navigation
         */
        $data = xarModAPIFunc('example', 'admin', 'menu');

        /* Specify for which item you want confirmation */
        $data['exid'] = $exid;

        /* Add some other data you'll want to display in the template */
        $data['itemid'] = xarML('Item ID');
        $data['namevalue'] = xarVarPrepForDisplay($item['name']);

        /* Generate a one-time authorisation code for this operation */
        $data['authid'] = xarSecGenAuthKey();

        /* Return the template variables defined in this function */
        return $data;
    }
    /* If we get here it means that the user has confirmed the action
     * Confirm authorisation code. This checks that the form had a valid
     * authorisation code attached to it. If it did not then the function will
     * proceed no further as it is possible that this is an attempt at sending
     * in false data to the system
     */
    if (!xarSecConfirmAuthKey()) return;
    /* The API function is called. Note that the name of the API function and
     * the name of this function are identical, this helps a lot when
     * programming more complex modules. The arguments to the function are
     * passed in as their own arguments array.
     */

    /* The return value of the function is checked here, and if the function
     * suceeded then an appropriate message is posted. Note that if the
     * function did not succeed then the API function should have already
     * posted a failure message so no action is required
     */
    if (!xarModAPIFunc('example',
            'admin',
            'delete',
            array('exid' => $exid))) {
        return; // throw back
    }
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('example', 'admin', 'view'));

    /* Return */
    return true;
}
?>