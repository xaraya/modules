<?php
/**
 * Modify a plan
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Modify a plan
 *
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 *
 * @author ITSP Module Development Team
 * @param  $ 'planid' the id of the item to be modified
 */
function itsp_admin_modify($args)
{
    extract($args);

    /* Get parameters from whatever input we need.
     */
    if (!xarVarFetch('planid',     'id',     $planid,     $planid,      XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid',   'id',     $objectid,   $objectid,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',    'array',  $invalid,    array(),      XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('planname',   'str:1:', $planname,   '',    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('plandesc',   'str:1:', $plandesc,   '',    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('planrules',  'str:1:', $planrules,  '',   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('credits',    'int:1:', $credits,    '',     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mincredit',  'int:1:', $mincredit,  '',   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateopen',   'isset', $dateopen,   $dateopen,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateclose',  'isset', $dateclose,  $dateclose,   XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $planid = $objectid;
    }
    /* Security check */
    if (!xarSecurityCheck('EditITSPPlan', 1, 'Item', "$planid:All")) {
        return;
    }
    /* The user API function is called.
     */
    $item = xarModAPIFunc('itsp',
                          'user',
                          'get_plan',
                          array('planid' => $planid));

    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* Get menu variables - it helps if all of the module pages have a standard
     * menu at their head to aid in navigation
     * $menu = xarModAPIFunc('itsp','admin','menu','modify');
     */
    $item['module'] = 'itsp';
    $item['itemtype'] = 1;
    $hooks = xarModCallHooks('item', 'modify', $planid, $item);

    /* Return the template variables defined in this function */
    return array('authid'       => xarSecGenAuthKey(),
                 'planid'       => $planid,
                 'planname'     => $planname,
                 'plandesc'     => $plandesc,
                 'credits'      => $credits,
                 'mincredit'    => $mincredit,
                 'planrules'    => $planrules,
                 'dateopen'     => $dateopen,
                 'dateclose'    => $dateclose,
                 'invalid'      => $invalid,
                 'hookoutput'   => $hooks,
                 'item'         => $item);
}
?>