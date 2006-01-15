<?php
/**
 * Add new planitem
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
    if (!xarVarFetch('pitemname',   'str:1:', $pitemname,   $pitemname,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemdesc',   'str:1:', $pitemdesc,   $pitemdesc,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemrules',  'str:1:', $pitemrules,  $pitemrules, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('credits',    'int:1:', $credits,    $credits,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mincredit',  'int:1:', $mincredit,  $mincredit, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateopen',   'int:1:', $dateopen,   $dateopen,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateclose',  'int:1:', $dateclose,  $dateclose, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('invalid', 'array',  $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    /* Initialise the $data variable that will hold the data to be used in
     * the blocklayout template, and get the common menu configuration - it
     * helps if all of the module pages have a standard menu at the top to
     * support easy navigation
     */
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
    $item['itemtype'] = 3;
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

    /* Return the template variables defined in this function */
    return $data;
}
?>