<?php
/**
 * Add new planitem
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Add a new planitem
 *
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 *
 * @author ITSP module development team
 * @return array
 */
function itsp_admin_new_pitem($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('planid',     'id',     $planid,      $planid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemname',  'str:1:', $pitemname,   $pitemname,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemdesc',  'str:1:', $pitemdesc,   $pitemdesc,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemrules', 'str:1:', $pitemrules,  $pitemrules, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('credits',    'int:0:', $credits,     $credits,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mincredit',  'int:0:', $mincredit,   $mincredit,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateopen',   'int:1:', $dateopen,    $dateopen,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateclose',  'int:1:', $dateclose,   $dateclose,  XARVAR_NOT_REQUIRED)) return;
    // Rules can start with an int of 0, meaning ALL
    if (!xarVarFetch('rule_cat',    'str::',    $rule_cat,     $rule_cat,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rule_type',   'int::',    $rule_type,    $rule_type,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rule_source', 'str:1:25', $rule_source,  $rule_source,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('rule_level',  'int::',    $rule_level,   $rule_level,   XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('invalid', 'array',  $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    // Add the admin menu
    $data = xarModAPIFunc('itsp', 'admin', 'menu');
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AddITSPPlan')) return;
    // get the levels in courses
    $data['levels'] = xarModAPIFunc('courses', 'user', 'gets',
                                      array('itemtype' => 1003));
    // Get the coursetypes for the types rule
    $data['coursetypes'] = xarModAPIFunc('courses', 'user', 'getall_coursetypes');
    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();
    $data['invalid'] = $invalid;
    // Hooks?
    $item = array();
    $item['module'] = 'itsp';
    $item['itemtype'] = '';
    $hooks = xarModCallHooks('item', 'new', '', $item);
    // Hooks?
    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }

    // Add the planid that this planitem can already be attached to
    $data['planid'] = $planid;

    /* For E_ALL purposes, we need to check to make sure the vars are set.
     * If they are not set, then we need to set them empty to surpress errors
     */
    if (empty($pitemname)) {
        $data['pitemname'] = '';
    } else {
        $data['pitemname'] = $pitemname;
    }

    if (empty($pitemdesc)) {
        $data['pitemdesc'] = '';
    } else {
        $data['pitemdesc'] = $pitemdesc;
    }
    if (empty($pitemrules)) {
        $data['pitemrules'] = '';
    } else {
        $data['pitemrules'] = $pitemrules;
    }
    if (empty($credits)) {
        $data['credits'] = '';
    } else {
        $data['credits'] = $credits;
    }
    if (empty($mincredit)) {
        $data['mincredit'] = '';
    } else {
        $data['mincredit'] = $mincredit;
    }
    if (empty($dateopen)) {
        $data['dateopen'] = '';
    } else {
        $data['dateopen'] = $dateopen;
    }
    if (empty($dateclose)) {
        $data['dateclose'] = '';
    } else {
        $data['dateclose'] = $dateclose;
    }

    if (empty($rule_type)) {
        $data['rule_type'] = 0;
    } else {
        $data['rule_type'] = $rule_type;
    }
    if (empty($rule_source)) {
        $data['rule_source'] = '';
    } else {
        $data['rule_source'] = $rule_source;
    }
    if (empty($rule_level)) {
        $data['rule_level'] = 0;
    } else {
        $data['rule_level'] = $rule_level;
    }
    if (empty($rule_cat)) {
        $data['rule_cat'] = '';
    } else {
        $data['rule_cat'] = $rule_cat;
    }

    /* Return the template variables defined in this function */
    return $data;
}
?>