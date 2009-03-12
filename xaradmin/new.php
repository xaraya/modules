<?php
/**
 * Add new item
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
 * Add new example item
 *
 * This is a standard function that is called whenever an user
 * wishes to create a new module item. The user needs at least
 * Add privileges in this example.
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
     * not hold in future versions of Xaraya.
     * For E_ALL purposes the vars need to be set at least empty.
     */
    if (!xarVarFetch('number',  'int:1:', $number,  '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name',    'str:1:', $name,    '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array',  $invalid, '', XARVAR_NOT_REQUIRED)) return;

    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AddExample')) return;

    /* The array '$data' will hold all data used in the Blocklayout template
     */
    $data = array();
    $data['name'] = $name;
    $data['number'] = $number;

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

    /* Return the template variables defined in this function */
    return $data;
}
?>