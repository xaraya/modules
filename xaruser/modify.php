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
 * @param  $ 'itspid' the id of the itsp to be modified
 * @param  $ 'pitemid' the id of the plan item to be modified
 */
function itsp_user_modify($args)
{
    extract($args);

    if (!xarVarFetch('itspid',    'id',   $itspid, $itspid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id',    $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemid', 'id',     $pitemid, $pitemid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',  'array', $invalid, array(), XARVAR_NOT_REQUIRED)) return;

    /* At this stage we check to see if we have been passed $objectid
     */
    if (!empty($objectid)) {
        $itspid = $objectid;
    }
    /* Get menu variables - it helps if all of the module pages have a standard
     * menu at their head to aid in navigation*/
    $data = xarModAPIFunc('itsp','user','menu');

    if (empty($itspid)) {
        $itsp = xarModAPIFunc('itsp',
                          'user',
                          'get',
                          array('userid' => xarUserGetVar('uid')));
    } else {

        // The user API function is called to get the ITSP
        $itsp = xarModAPIFunc('itsp',
                              'user',
                              'get',
                              array('itspid' => $itspid));
    }

    /* Check for exceptions */
    if (!isset($itsp) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    $planid = $itsp['planid'];
    $itspid = $itsp['itspid'];
    /* Security check
     */
    if (!xarSecurityCheck('ReadITSP', 1, 'ITSP', "$itspid:$planid:All")) {
        return;
    }

    // Check to see if we are already dealing with a planitem
    if (!empty($pitemid) && is_numeric($pitemid)) {
        //get planitem
        $pitem = xarModApiFunc('itsp','user','get_planitem',array('pitemid'=>$pitemid));
        $data['pitemrules'] = $pitem['pitemrules']; // TODO: split the rules up
        // get the pitem details for this itsp

        $courselinks = xarModApiFunc('itsp','user','getall_courselinks',array('itspid'=>$pitemid));






        $data['pitem'] = $pitem;
        $data['courselinks'] = $courselinks;

    }



    $item['module'] = 'itsp';
    $item['itemid'] = 2;
    $hooks = xarModCallHooks('item', 'modify', $itspid, $item);

    /* Return the template variables defined in this function */
    $data['authid']      = xarSecGenAuthKey();
    $data['hookoutput']  = $hooks;
    $data['item']        = $item;
    return $data;
}
?>