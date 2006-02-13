<?php
/**
 * Modify an ITSP
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
 * Modify an ITSP
 *
 * This is a standard function that is called whenever a user
 * wishes to modify a current module item
 *
 * @author ITSP Module Development Team
 * @param  $ 'itspid' the id of the itsp to be modified
 * @param  $ 'pitemid' the id of the plan item to be modified
 * @todo add test for already followed courses
 *       add checks for types of planitems
 */
function itsp_user_modify($args)
{
    extract($args);

    if (!xarVarFetch('itspid',   'id',    $itspid, $itspid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id',    $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemid',  'id',    $pitemid, $pitemid, XARVAR_NOT_REQUIRED)) return;
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
        $data['pitemrules'] = $pitem['pitemrules'];

        // Splice the rule
        if (!empty($pitem['pitemrules'])) {
        $rule_parts = explode(';',$pitem['pitemrules']);
            $data['rule_type'] = $rule_parts[0];
            $data['rule_level'] = $rule_parts[1];
            $data['rule_cat'] = $rule_parts[2];
            $data['rule_source'] = $rule_parts[3];
        }
        // get the pitem details for this itsp
        // get all linked courses
        $courselinks = xarModApiFunc('itsp','user','getall_courselinks',array('itspid'=>$pitemid));
        // for each linked course get the details
        if (!isset($courselinks) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

        /* TODO: check for conflicts between transformation hook output and xarVarPrepForDisplay
         * Loop through each item and display it.
         */
        foreach ($courselinks as $lcourse) {
            // Add read link
            $courseid = $lcourse['lcourseid'];
            if (xarSecurityCheck('ReadITSPPlan', 0, 'Plan', "$planid:All:All")) {
                $lcourse['link'] = xarModURL('courses',
                    'user',
                    'display',
                    array('courseid' => $courseid));
                /* Security check 2 - else only display the item name (or whatever is
                 * appropriate for your module)
                 */
            } else {
                $lcourse['link'] = '';
            }
            $course = xarModApiFunc('courses','user','get', array('courseid'=>$courseid));
            /* Clean up the item text before display */
            $lcourse['name'] = xarVarPrepForDisplay($course['name']);
            $lcourse['intendedcredits'] = $course['intendedcredits'];
            /* Add this item to the list of items to be displayed */
            $data['lcourses'][] = $lcourse;
        }





        $data['pitem'] = $pitem;


    }

    $data['pitemid'] = $pitemid;

    $item['module'] = 'itsp';
    $item['itemid'] = 2;
    $hooks = array();
    $hooks = xarModCallHooks('item', 'modify', $itspid, $item);

    /* Return the template variables defined in this function */
    $data['authid']      = xarSecGenAuthKey();
    $data['hookoutput']  = $hooks;
    $data['item']        = $item;
    return $data;
}
?>