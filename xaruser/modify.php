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
 * This function will help a user with an itsp to update that itsp. It takes the itspid and the plan itemid
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

    /* At this stage we check to see if we have been passed $objectid */
    if (!empty($objectid)) {
        $itspid = $objectid;
    }
    /* Get menu variables and set data as array */
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
    $userid = $itsp['userid']; // User for MySelf

    /* Security check */
    if (!xarSecurityCheck('EditITSP', 1, 'ITSP', "$itspid:$planid:$userid")) {
        return;
    }
    // See if the user can edit
    $canedit = false;
    $itspstatus = $itsp['itspstatus'];
    if ($itspstatus < 4 || $itspstatus == 5) {
        $canedit = true;
    }
    $data['canedit'] = $canedit;
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
        // See if mix is true, then we need both sources

        if ($rules['mix']) {
            // get the courses to modify
            $lcourses = xarModApiFunc('itsp','user','getmodifycourses',
                                                array('itspid'  => $itspid,
                                                      'pitemid' => $pitemid,
                                                      'planid'  => $planid,
                                                      'userid'  => $userid));
            // Only add the credits when there are none found.
            if (!empty($lcourses)) {
                $creditsnow = $lcourses['creditsnow'];
                $data['lcourses'] = $lcourses['lcourses'];
            }
        }

        switch ($rules['rule_source']) {
            case 'courses':
                // get the courses to modify
                $lcourses = xarModApiFunc('itsp','user','getmodifycourses',
                                                    array('itspid'  => $itspid,
                                                          'pitemid' => $pitemid,
                                                          'planid'  => $planid,
                                                          'userid'  => $userid));
                $creditsnow = $lcourses['creditsnow'];
                $data['lcourses'] = $lcourses['lcourses'];
                break;
            // The default will pull all linked courses. These can hold any type of courses
            // The source here is the template name that will be used.
            case 'external':
            default:
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
              //  if (!xarVarFetch('invalid',        'array',     $invalid,        array(),         XARVAR_NOT_REQUIRED)) return;

                //if (!xarSecurityCheck('AddITSPPlan')) return;
                // get the levels in courses
                $data['levels'] = xarModAPIFunc('courses', 'user', 'gets',
                                                  array('itemtype' => 1003));
                // Get the coursetypes for the types rule
                $data['coursetypes'] = xarModAPIFunc('courses', 'user', 'getall_coursetypes');
                $data['invalid'] = $invalid;
                $data['icourseid'] ='';
                if (!empty($icourseid)) {
                    $icourse = xarModApiFunc('itsp','user','get_itspcourse',array('icourseid' => $icourseid));
                    $icoursetitle = $icourse['icoursetitle'];
                    $icourseloc =$icourse['icourseloc'];
                    $icoursedesc =$icourse['icoursedesc'];
                    $icoursecredits =$icourse['icoursecredits'];
                    $icourselevel = $icourse['icourselevel'];
                    $icourseresult = $icourse['icourseresult'];
                    $icoursedate =$icourse['icoursedate'];
                    $data['icourseid'] = $icourseid;
                }

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

                // get all linked courses that already have been added to the ITSP for this plan item
                $courselinks = xarModApiFunc('itsp','user','getall_itspcourses',array('itspid'=>$itspid, 'pitemid' => $pitemid));
                // for each linked course get the details
                if (!isset($courselinks) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
                //echo count($courselinks);
                /*
                 * Loop through each item and display it.
                 */
                foreach ($courselinks as $icourse) {
                    // Add read link
                    $icourseid = $icourse['icourseid'];
                    if (xarSecurityCheck('ReadITSP', 0, 'ITSP', "$itspid:$planid:$userid")) {
                        $icourse['link'] = xarModURL('itsp',
                            'user',
                            'display_icourse',
                            array('icourseid' => $icourseid));
                    } else {
                        $icourse['link'] = '';
                    }
                    if ($canedit && xarSecurityCheck('EditITSP', 0, 'ITSP', "$itspid:$planid:$userid")) {
                        $icourse['deletelink'] = xarModURL('itsp','admin','delete_courselink',array('icourseid' => $icourseid,
                                                                                                    'authid' => xarSecGenAuthKey('itsp'),
                                                                                                    'pitemid' => $pitemid,
                                                                                                    'itspid' => $itspid));

                        $icourse['editlink'] = xarModURL('itsp','user','modify',array('itspid'=>$itspid,
                                                                                      'pitemid'=>$pitemid,
                                                                                      'icourseid' => $icourseid));
                    } else {
                        $icourse['deletelink'] = '';
                        $icourse['editlink'] = '';
                    }


                    /* Clean up the item text before display */
                    $icourse['title'] = xarVarPrepForDisplay($icourse['icoursetitle']);
                    $icourse['credits'] = $icourse['icoursecredits'];
                    // Add a delete link
                    $icourse['deletelink'] = xarModURL('itsp','admin','delete_courselink',array('icourseid' => $icourse['icourseid'], 'authid' => xarSecGenAuthKey('itsp'), 'pitemid' => $pitemid, 'itspid' => $itspid));

                    /* Add this item to the list of items to be displayed */
                    $data['icourses'][] = $icourse;
                }


                $creditsnow = xarModApiFunc('itsp','user','countcredits',array('uid' => xarUserGetVar('uid'), 'pitemid' => $pitemid,'itspid'=>$itspid));
        }
        $data['pitem'] = $pitem;
        $data['creditsnow'] = $creditsnow;
    }

    $data['pitemid'] = $pitemid;


    // Call hooks
    $item['module'] = 'itsp';
    $item['itemtype'] = 2;
    $hooks = array();
    $hooks = xarModCallHooks('item', 'modify', $itspid, $item);

    /* Return the template variables defined in this function */
    $data['authid']      = xarSecGenAuthKey('itsp.modify');
    $data['hookoutput']  = $hooks;
    $data['item']        = $item;

    xarTplSetPageTitle(xarVarPrepForDisplay($pitem['pitemname']));
    return $data;
}
?>