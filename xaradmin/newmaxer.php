<?php
/**
 * Add new maxer (pager)
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Maxercalls Module Development Team
 */
/**
 * Add new maxer
 *
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 *
 * @author Maxercalls module development team
 * @return array
 */
function maxercalls_admin_newmaxer($args)
{
    extract($args);

    if (!xarVarFetch('personid',    'int:1:', $personid,  $personid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ric',         'int:1:', $ric,  $ric,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxernumber', 'int:1:', $maxernumber,  $maxernumber,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('function',    'int:1:', $function,  $function,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxerstatus', 'int:1:', $maxerstatus,  $maxerstatus,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('remark',      'str:1:', $remark,  $remark,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('program',     'str:1:200', $program,    $program,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',     'array',  $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    /* Initialise the $data variable that will hold the data to be used in
     * the blocklayout template, and get the common menu configuration - it
     * helps if all of the module pages have a standard menu at the top to
     * support easy navigation
     */
    $data = xarModAPIFunc('maxercalls', 'admin', 'menu');
    /* Security check. - Will need some improvements. Extra type?
     */
    if (!xarSecurityCheck('DeleteMaxercalls')) return;

    // Get all personids
    // TODO: This should be a nice API function, or DDProperty
    $persons = xarModAPIFunc('sigmapersonnel','user','getall');


    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();
    $data['invalid'] = $invalid;

    $item = array();
    $item['module'] = 'maxercalls';
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
    if (empty($remark)) {
        $data['remark'] = '';
    } else {
        $data['remark'] = $remark;
    }
    if (empty($ric)) {
        $data['ric'] = '';
    } else {
        $data['ric'] = $ric;
    }
    if (empty($maxernumber)) {
        $data['maxernumber'] = '';
    } else {
        $data['maxernumber'] = $maxernumber;
    }
    if (empty($program)) {
        $data['program'] = '';
    } else {
        $data['program'] = $program;
    }
    if (empty($personid)) {
        $data['personid'] = '';
    } else {
        $data['personid'] = $personid;
    }
    /* Return the template variables defined in this function */
    return $data;
}
?>