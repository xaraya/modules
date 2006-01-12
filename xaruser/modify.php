<?php
/**
 * Modify an ITSP
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author ITSP Module Development Team
 */
/**
 * Modify an ITSP
 *
 * This is a standard function that is called whenever an useristrator
 * wishes to modify a current module item
 *
 * @author ITSP Module Development Team
 * @param  $ 'itspid' the id of the item to be modified
 */
function itsp_user_modify($args)
{
    extract($args);

    if (!xarVarFetch('itspid',    'id',     $itspid)) return;
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
    $item = xarModAPIFunc('itsp',
                          'user',
                          'get',
                          array('itspid' => $itspid));

    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing. However,
     * in this case we had to wait until we could obtain the item name to
     * complete the instance information so this is the first chance we get to
     * do the check
     */
    if (!xarSecurityCheck('EditITSP', 1, 'Item', "$item[name]:All:$exid")) {
        return;
    }
    /* Get menu variables - it helps if all of the module pages have a standard
     * menu at their head to aid in navigation
     * $menu = xarModAPIFunc('itsp','user','menu','modify');
     */
    $item['module'] = 'itsp';
    $item['itemid'] = 2;
    $hooks = xarModCallHooks('item', 'modify', $itspid, $item);

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