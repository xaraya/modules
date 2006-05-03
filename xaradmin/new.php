<?php
/**
 * Add new item
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
 * Add new item
 *
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 *
 * @author Example module development team
 * @return array
 */
function example_admin_new($args)
{
    /* Admin functions of this type can be called by other modules. If this
     * happens then the calling module will be able to pass in arguments to
     * this function through the $args parameter. Hence we extract these
     * arguments *before* we have obtained any form-based input through
     * xarVarFetch().
     */
    extract($args);

    /* Get parameters from whatever input we need. All arguments to this
     * function should be obtained from xarVarFetch(). xarVarFetch allows
     * the checking of the input variables as well as setting default
     * values if needed. Getting vars from other places such as the
     * environment is not allowed, as that makes assumptions that will
     * not hold in future versions of Xaraya
     */
    if (!xarVarFetch('number',  'int:1:', $number,  $number,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name',    'str:1:', $name,    $name,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array',  $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    /* Initialise the $data variable that will hold the data to be used in
     * the blocklayout template, and get the common menu configuration - it
     * helps if all of the module pages have a standard menu at the top to
     * support easy navigation
     */
    $data = xarModAPIFunc('example', 'admin', 'menu');
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AddExample')) return;

    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();
    $data['invalid'] = $invalid;

    $item = array();
    $item['module'] = 'example';
    $hooks = xarModCallHooks('item', 'new', '', $item);

    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        /* You can use the output from individual hooks in your template too, e.g. with
         * $hookoutput['categories'], $hookoutput['dynamicdata'], $hookoutput['keywords'] etc.
         */
        $data['hookoutput'] = $hooks;
    }
    $data['hooks'] = '';
    /* For E_ALL purposes, we need to check to make sure the vars are set.
     * If they are not set, then we need to set them empty to surpress errors
     */
    if (empty($name)) {
        $data['name'] = '';
    } else {
        $data['name'] = $name;
    }

    if (empty($number)) {
        $data['number'] = '';
    } else {
        $data['number'] = $number;
    }
    /* Return the template variables defined in this function */
    return $data;
}
?>