<?php
/**
 * Modify a plan_item
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
 * Modify a plan_item
 *
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 *
 * @author ITSP Module Development Team
 * @param  $ 'pitemid' the id of the item to be modified
 * @param all parts...
 */
function itsp_admin_modify_pitem($args)
{
    extract($args);

    /* Get parameters from whatever input we need.
     */
    if (!xarVarFetch('pitemid',     'id',     $pitemid,    $pitemid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid',    'id',     $objectid,   $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',     'array',  $invalid,    array(),   XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('pitemname',   'str:1:', $pitemname,  '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemdesc',   'str:1:', $pitemdesc,  '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemrules',  'str:1:', $pitemrules, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('credits',     'int:0:', $credits,    '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mincredit',   'int:1:', $mincredit,  '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateopen',    'int:1:', $dateopen,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateclose',   'int:1:', $dateclose,  '', XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('rule_cat',    'int::',                      $rule_cat,    $rule_cat,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rule_type',   'int::',                       $rule_type,   $rule_type,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rule_source', 'enum:internal:external:open:all', $rule_source, $rule_source, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rule_level',  'int::',                      $rule_level,  $rule_level,  XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $pitemid = $objectid;
    }
    /* The user API function is called. */
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

    // Splice the rule
    if (!empty($item['pitemrules'])) {
        list($Rtype, $Rlevel, $Rcat, $Rsource) = explode(";", $item['pitemrules']);

        $rule_parts = explode(':',$Rtype);
        $rule_type = $rule_parts[1];
        $rule_parts = explode(':',$Rlevel);
        $rule_level = $rule_parts[1];
        $rule_parts = explode(':',$Rcat);
        $rule_cat = $rule_parts[1];
        $rule_parts = explode(':',$Rsource);
        $rule_source = $rule_parts[1];
    /*
        $data['rule_type'] = split($rule_type;

        $data['rule_level'] = $rule_parts[1];
        $data['rule_cat'] = $rule_parts[2];
        $data['rule_source'] = $rule_parts[3];
        */
    }

    // get the levels in courses
    $levels = xarModAPIFunc('courses', 'user', 'gets', array('itemtype' => 1003));

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