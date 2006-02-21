<?php
/**
 * Add new item
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
 * Add a new plan
 *
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @return array
 */
function itsp_admin_new($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('planname',   'str:1:', $planname,   $planname,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('plandesc',   'str:1:', $plandesc,   $plandesc,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('planrules',  'str:1:', $planrules,  $planrules, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('credits',    'int:1:', $credits,    $credits,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mincredit',  'int:1:', $mincredit,  $mincredit, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateopen',   'int:1:', $dateopen,   $dateopen,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateclose',  'int:1:', $dateclose,  $dateclose, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('invalid', 'array',  $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    // Call in menu. Non functional yet
    $data = xarModAPIFunc('itsp', 'admin', 'menu');
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AddITSPPlan')) return;

    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();
    $data['invalid'] = $invalid;

    $item = array();
    $item['module'] = 'itsp';
    $item['itemtype'] = 1;
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
    if (empty($planname)) {
        $data['planname'] = '';
    } else {
        $data['planname'] = $planname;
    }

    if (empty($plandesc)) {
        $data['plandesc'] = '';
    } else {
        $data['plandesc'] = $plandesc;
    }
    if (empty($planrules)) {
        $data['planrules'] = '';
    } else {
        $data['planrules'] = $planrules;
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

    /* Return the template variables defined in this function */
    return $data;
}
?>