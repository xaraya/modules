<?php
/**
 * Modify a pitem
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Modify a pitem
 *
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 *
 * @author ITSP Module Development Team
 * @param  $ 'pitemid' the id of the item to be modified
 */
function itsp_admin_modify_pitem($args)
{
    extract($args);

    /* Get parameters from whatever input we need.
     */
    if (!xarVarFetch('pitemid',     'id',     $pitemid,    $pitemid,      XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid',    'id',     $objectid,   $objectid,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',     'array',  $invalid,    array(),      XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('pitemname',   'str:1:', $pitemname,  '',    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemdesc',   'str:1:', $pitemdesc,  '',    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemrules',  'str:1:', $pitemrules, '',   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('credits',     'int:1:', $credits,    '',     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mincredit',   'int:1:', $mincredit,  '',   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateopen',    'int:1:', $dateopen,   '',    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateclose',   'int:1:', $dateclose,  '',   XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('rule_cat',    'int:1:',                      $rule_cat,    $rule_cat,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rule_type',   'str:1:',                      $rule_type,   $rule_type,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rule_source', 'enum:internal:external:open', $rule_source, $rule_source, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rule_level',  'int:1:',                      $rule_level,  $rule_level,  XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $pitemid = $objectid;
    }
    /* The user API function is called.
     */
    $item = xarModAPIFunc('itsp',
                          'user',
                          'get_planitem',
                          array('pitemid' => $pitemid));

    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* Security check */
    if (!xarSecurityCheck('EditITSPPlan', 1, 'Plan', "All:$pitemid:All")) {
        return;
    }

    // get the levels in courses
    $levels = xarModAPIFunc('courses', 'user', 'gets', array('itemtype' => 3));


    /* Call hooks */
    $item['module'] = 'itsp';
    $item['itemtype'] = 3;
    $hooks = xarModCallHooks('item', 'modify', $pitemid, $item);

    /* Return the template variables defined in this function */
    return array('authid'       => xarSecGenAuthKey(),
                 'pitemid'      => $pitemid,
                 'pitemname'    => $pitemname,
                 'pitemdesc'    => $pitemdesc,
                 'credits'      => $credits,
                 'mincredit'    => $mincredit,
                 'rule_cat'     => $rule_cat,
                 'rule_type'    => $rule_type,
                 'rule_source'  => $rule_source,
                 'rule_level'   => $rule_level,
                 'pitemrules'   => $pitemrules,
                 'dateopen'     => $dateopen,
                 'dateclose'    => $dateclose,
                 'invalid'      => $invalid,
                 'hookoutput'   => $hooks,
                 'item'         => $item,
                 'levels'       => $levels
                 );
}
?>