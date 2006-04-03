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
 * This function will helpt a user with an itsp to update that itsp. It takes the itspid and the plan itemid
   and serves the relevant modify page. The source that is defined in the planitem will determine the include template.
 *
 * @author ITSP Module Development Team
 * @param int itspid The id of the itsp to be modified
 * @param int pitemid The id of the plan item to be modified
 * @todo add test for already followed courses
 *       add checks for types of planitems
 * @return array with data for template. The template will include whatever is needed to add courses
 */
function itsp_user_modify($args)
{
    extract($args);

    if (!xarVarFetch('itspid',   'id',    $itspid,   $itspid,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id',    $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemid',  'id',    $pitemid,  $pitemid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',  'array', $invalid,  array(),   XARVAR_NOT_REQUIRED)) return;

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
    if (!xarSecurityCheck('ReadITSP', 1, 'ITSP', "$itspid:$planid")) {
        return;
    }

    // Check to see if we are already dealing with a planitem
    if (!empty($pitemid) && is_numeric($pitemid)) {
        // get planitem
        $pitem = xarModApiFunc('itsp','user','get_planitem',array('pitemid'=>$pitemid));
        $data['pitem'] = $pitem;
        if (!isset($pitem) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
        $data['pitemrules'] = $pitem['pitemrules'];
        $rules = xarModApiFunc('itsp','user','splitrules',array('rules'=>$pitem['pitemrules']));
        // Splice the rule
        $data['rule_type'] = $rules['rule_type'];
        $data['rule_level'] = $rules['rule_level'];
        $data['rule_cat'] = $rules['rule_cat'];
        $data['rule_source'] = $rules['rule_source'];


  //      }
        switch ($rules['rule_source']) {
            case 'courses':
                // get the pitem details for this itsp
                // get all linked courses that already have been added to the ITSP for this pitemid
                $courselinks = xarModApiFunc('itsp','user','getall_courselinks',array('itspid'=>$pitemid, 'pitemid' => $pitemid));
                // for each linked course get the details
                if (!isset($courselinks) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

                /* TODO: check for conflicts between transformation hook output and xarVarPrepForDisplay
                 * Loop through each item and display it.
                 */
                 $creditsnow = 0;
                foreach ($courselinks as $lcourse) {
                    // Add read link
                    $courseid = $lcourse['lcourseid'];
                    if (xarSecurityCheck('ReadITSPPlan', 0, 'Plan', "$planid:All")) {
                        $lcourse['link'] = xarModURL('courses',
                            'user',
                            'display',
                            array('courseid' => $courseid));
                    } else {
                        $lcourse['link'] = '';
                    }
                    $course = xarModApiFunc('courses','user','get', array('courseid'=>$courseid));
                    /* Clean up the item text before display */
                    $lcourse['name'] = xarVarPrepForDisplay($course['name']);
                    $lcourse['intendedcredits'] = $course['intendedcredits'];
                    /* Add this item to the list of items to be displayed */
                    $data['lcourses'][] = $lcourse;
                    $creditsnow = $creditsnow + $course['intendedcredits'];
                }
                break;
            // The default will pull all linked courses. These can hold any type of courses
            // The source here is the template name that will be used.
            default:
                // get all linked courses that already have been added to the ITSP for this pitemid
                $courselinks = xarModApiFunc('itsp','user','getall_itspcourses',array('itspid'=>$itspid, 'pitemid' => $pitemid));
                // for each linked course get the details
                if (!isset($courselinks) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
                /* TODO: check for conflicts between transformation hook output and xarVarPrepForDisplay
                 * Loop through each item and display it.
                 */
                foreach ($courselinks as $icourse) {
                    // Add read link
                    $icourseid = $icourse['icourseid'];
                    if (xarSecurityCheck('ReadITSP', 0, 'ITSP', "$itspid:All")) {
                        $icourse['link'] = xarModURL('itsp',
                            'user',
                            'display_icourse',
                            array('icourseid' => $icourseid));
                    } else {
                        $icourse['link'] = '';
                    }
                    /* Clean up the item text before display */
                    $icourse['title'] = xarVarPrepForDisplay($icourse['icoursetitle']);
                    $icourse['credits'] = $icourse['icoursecredits'];
                    /* Add this item to the list of items to be displayed */
                    $data['icourses'][] = $icourse;
                }
                // Set data for a new item
                if (!xarVarFetch('icourseid',      'id',        $icourseid,      $icourseid,      XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('icoursetitle',   'str:1:255', $icoursetitle,   $icoursetitle,   XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('icourseloc',     'str:1:255', $icourseloc,     $icourseloc,     XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('icoursedesc',    'str::',     $icoursedesc,    $icoursedesc,    XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('icoursecredits', 'int::',     $icoursecredits, $icoursecredits, XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('icourselevel',   'str:1:255', $icourselevel,   $icourselevel,   XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('icourseresult',  'str:1:255', $icourseresult,  $icourseresult,  XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('icoursedate',    'str::',     $icoursedate,    $icoursedate,    XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('dateappr',       'str::',     $dateappr,       $dateappr,       XARVAR_NOT_REQUIRED)) return;
                if (!xarVarFetch('invalid',        'array',     $invalid,        array(),         XARVAR_NOT_REQUIRED)) return;

                //if (!xarSecurityCheck('AddITSPPlan')) return;
                // get the levels in courses
                $data['levels'] = xarModAPIFunc('courses', 'user', 'gets',
                                                  array('itemtype' => 1003));
                // Get the coursetypes for the types rule
                $data['coursetypes'] = xarModAPIFunc('courses', 'user', 'getall_coursetypes');
                $data['invalid'] = $invalid;

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
                if (empty($icoursetitle)) {
                    $data['icoursetitle'] = '';
                } else {
                    $data['icoursetitle'] = $icoursetitle;
                }

                if (empty($icourseloc)) {
                    $data['icourseloc'] = '';
                } else {
                    $data['icourseloc'] = $icourseloc;
                }
                if (empty($icoursedesc)) {
                    $data['icoursedesc'] = '';
                } else {
                    $data['icoursedesc'] = $icoursedesc;
                }
                if (empty($icoursecredits)) {
                    $data['icoursecredits'] = '';
                } else {
                    $data['icoursecredits'] = $icoursecredits;
                }
                if (empty($icourselevel)) {
                    $data['icourselevel'] = '';
                } else {
                    $data['icourselevel'] = $icourselevel;
                }
                if (empty($icourseresult)) {
                    $data['icourseresult'] = '';
                } else {
                    $data['icourseresult'] = $icourseresult;
                }
                if (empty($icoursedate)) {
                    $data['icoursedate'] = '';
                } else {
                    $data['icoursedate'] = $icoursedate;
                }
                if (empty($dateappr)) {
                    $data['dateappr'] = '';
                } else {
                    $data['dateappr'] = $dateappr;
                }
                $creditsnow = xarModApiFunc('itsp','user','countcredits',array('uid' => xarUserGetVar('uid'), 'pitemid' => $pitemid));
        }
        $data['pitem'] = $pitem;
        $data['creditsnow'] = $creditsnow;
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