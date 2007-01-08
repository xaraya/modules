<?php
/**
 * Add new maxer (pager)
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
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

    // Will we use a menu here?
    $data = xarModAPIFunc('maxercalls', 'admin', 'menu');
    /* Security check. TODO: Will need some improvements. Extra type?
     */
    if (!xarSecurityCheck('DeleteMaxercalls')) return;
    // Get the maxerstatus that we can use
    $data['statusses'] = xarModAPIFunc('maxercalls', 'user', 'gets',
                                      array('itemtype' => 6));
    // Get the maxerfunctions that we can use
    $data['functions'] = xarModAPIFunc('maxercalls', 'user', 'gets',
                                      array('itemtype' => 7));

//DynProperty!    $persons = xarModAPIFunc('sigmapersonnel', 'user', 'getall',array('status' =>NULL));//TODO

    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();
    $data['invalid'] = $invalid;

    $item = array();
    $item['module'] = 'maxercalls';
    $item['itemtype'] = 2;
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
    if (empty($function)) {
        $data['function'] = '';
    } else {
        $data['function'] = $function;
    }
    if (empty($maxerstatus)) {
        $data['maxerstatus'] = '';
    } else {
        $data['maxerstatus'] = $maxerstatus;
    }
    /* Return the template variables defined in this function */
    return $data;
}
?>