<?php
/**
 * Modify an item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * Modify an item
 *
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 *
 * @author Example Module Development Team
 * @param array $args An array containing all the arguments to this function.
 * @param int exid The id of the item to be modified
 * @param int objectid The id of the unified object, for use with other modules
 * @param array invalid This array is initialised in the beginning of the function
                        to hold all the errors caught in admin-update
 * @param int number A number for the item, used as an example
 * @param string name A name for the item, used as an example
 * @return array $item containing all elements and variables for the template
 */
function example_admin_modify($args)
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
     * the checking of the input variables as well as setting default
     * values if needed. Getting vars from other places such as the
     * environment is not allowed, as that makes assumptions that will
     * not hold in future versions of Xaraya
     */
    if (!xarVarFetch('exid',     'id',     $exid)) return;
    if (!xarVarFetch('objectid', 'id',     $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',  'array', $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('number',   'int',    $number, $number,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name',     'str:1:', $name, $name, XARVAR_NOT_REQUIRED)) return;

    /* At this stage we check to see if we have been passed $objectid, the
     * generic item identifier. This could have been passed in by a hook or
     * through some other function calling this as part of a larger module, but
     * if it exists it overrides $exid
     *
     * Note that this module could just use $objectid everywhere to avoid all
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
    if (!xarSecurityCheck('EditExample', 1, 'Item', "$item[name]:All:$exid")) {
        return;
    }
    /* Get menu variables - it helps if all of the module pages have a standard
     * menu at their head to aid in navigation. The example here includes a
     * menu for this function, hence the specification of 'modify'
     * $menu = xarModAPIFunc('example','admin','menu','modify');
     */
    /* Call the hooks
     * This example module doesn't use itemtypes
     * We will therefor pass NULL as an itemtype. When you define itemtypes, you should 
     * pass it to the call for the hooks here
     */
    $item['module'] = 'example';
    $item['itemtype'] = NULL;
    $hooks = xarModCallHooks('item', 'modify', $exid, $item);

    /* Return the template variables defined in this function */
    return array('authid'       => xarSecGenAuthKey(),
                 'name'         => $name,
                 'number'       => $number,
                 'invalid'      => $invalid,
                 'hookoutput'   => $hooks,
                 'hooks'        => '',
                 'item'         => $item);
}
?>