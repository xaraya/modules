<?php
/**
 * Standard function to Delete an item
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
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
 * This is a standard function that is called whenever someone
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
    /* See modify.php for information on passed in arguments
     */
    extract($args);

    /* See modify.php for information on getting parameters from input
     */
    if (!xarVarFetch('exid',     'id', $exid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',  'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    /* See modify.php for information on the use of $exid and $objectid
     */
    if (!empty($objectid)) {
        $exid = $objectid;
    }
    /* See modify.php for information on this API function call.
     */
    $item = xarModAPIFunc('example',
        'user',
        'get',
        array('exid' => $exid));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* See modify.php for information on this security check
     */
    if (!xarSecurityCheck('DeleteExample', 1, 'Item', "$item[name]:$item[number]:$exid")) {
        return;
    }
    /* Check for confirmation. */
    if (empty($confirm)) {
        /* No confirmation yet - display a suitable form to obtain confirmation
         * of this action from the user*/

        /*
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
    /* If we get here it means that the user has confirmed the action */

    /* See update.php for information on confirming the AuthKey
     */
    if (!xarSecConfirmAuthKey()) return;

    /* See update.php for information on handling the results of this API call
     */
    if (!xarModAPIFunc('example',
            'admin',
            'delete',
            array('exid' => $exid))) {
        return; // throw back
    }
    /* See update.php for information on this redirect
     */
    xarResponseRedirect(xarModURL('example', 'admin', 'view'));

    return true;
}
?>