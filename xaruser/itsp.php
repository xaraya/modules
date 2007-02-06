<?php
/**
 * Display the ITSP for one user
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
 * Display the user's ITSP
 *
 * Show the user the full details of the plan chosen, and the status of all items.
 *
 * @author the ITSP module development team
 * @param  array $args an array of arguments (if called by other modules)
 * @param  int $objectid a generic object id (if called by other modules)
 * @param  int $itspid the item id used for this itsp module
 * @param  int showdetails. Works together with a session variable to have a full view of details, or the short overview
 * @param  int pitemid The id of the planitem to show
 * @return array
 */
function itsp_user_itsp($args)
{
    // Quick one
    if(!xarSecurityCheck('ViewITSP')) return;
    extract($args);

    if (!xarVarFetch('itspid',   'id', $itspid,   NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemid',  'id', $pitemid,  NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showdetails', 'int::', $showdetails, $showdetails, XARVAR_NOT_REQUIRED)) return;

    /* At this stage we check to see if we have been passed $objectid, the
     * generic item identifier.
     */
    if (!empty($objectid)) {
        $itspid = $objectid;
    }
    // We have a valid ITSP?
    if (empty($itspid)) {
        $item = xarModAPIFunc('itsp',
                          'user',
                          'get_itspid',
                          array('userid' => xarUserGetVar('uid')));
    } else {

        // The user API function is called to get the ITSP
        $item = xarModAPIFunc('itsp',
                              'user',
                              'get',
                              array('itspid' => $itspid));
    }

    if (empty($item)) {
        xarTplSetPageTitle(xarML('Individual Training and Supervision Plan'));
        $data = xarModAPIFunc('itsp', 'user', 'menu');
        return $data;
    }
    $itspid = $item['itspid'];
    /* Add the ITSP user menu */
    // This also gets already all the planitems...
    $data = xarModAPIFunc('itsp', 'user', 'menu', array('itspid' => $itspid));
    /* Set the type of detail view */
    $details = xarSessionGetVar('itsp.fulldetails');
    if (is_int($showdetails)) {
        xarSessionSetVar('itsp.fulldetails', $showdetails);
    } elseif (empty($showdetails) && $details == 1) {
        xarSessionSetVar('itsp.fulldetails', 1);
    } else {
        xarSessionSetVar('itsp.fulldetails', 0);
    }
    $fulldetails = xarSessionGetVar('itsp.fulldetails');
    $data['fulldetails'] = xarSessionGetVar('itsp.fulldetails');

    // First see if there is an id to get.
    // Check status
    $stati = xarModApiFunc('itsp','user','getstatusinfo');
    $data['stati'] = $stati;
    $itspstatus = $item['itspstatus'];
    $data['statusname'] = $stati[$itspstatus];
    $data['itspstatus'] = $itspstatus;

    $planid = $item['planid'];
    $userid = $item['userid'];
    // Security check
    if (!xarSecurityCheck('ReadITSP',1,'ITSP',"$itspid:$planid:$userid")) {
       return $data;
    }

    // Get all planitems, only if full details are requested
    if ($fulldetails == 1) {
        $items = $data['pitems'];
        foreach ($items as $fullitem) {
            // get id
            $pitemid = $fullitem['pitemid'];
            if (!empty($pitemid) && is_numeric($pitemid)) {
                // get planitem
                $pitem = xarModApiFunc('itsp','user','get_planitem',array('pitemid'=>$pitemid));
                if (!isset($pitem) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
                // Get the rules for the source of this plan item
                $rules = xarModApiFunc('itsp','user','splitrules',array('rules'=>$pitem['pitemrules']));
                $rule_source = $rules['rule_source'];
                $fullitem['rule_source'] = $rule_source;
                // mix possibility
                if ($rules['mix']) {
                    $source = 'mix';
                } else {
                    $source = $rule_source;
                }
                switch ($source) {
                    case 'courses':
                    case 'mix':
                    // get the pitem details for this itsp
                    // get all linked courses that already have been added to the ITSP for this pitemid
                    $courselinks = xarModApiFunc('itsp','user','getall_courselinks',array('itspid'=>$itspid, 'pitemid' => $pitemid));

                    if (!isset($courselinks) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

                    // for each linked course get the details
                    foreach ($courselinks as $course) {
                        // Add read link
                        $courseid = $course['lcourseid'];
                        if (xarSecurityCheck('ReadITSPPlan', 0, 'Plan', "$planid:All")) {
                            $course['link'] = xarModURL('courses',
                                'user',
                                'display',
                                array('courseid' => $courseid));
                        } else {
                            $course['link'] = '';
                        }
                        $realcourse = xarModApiFunc('courses','user','get', array('courseid'=>$courseid));
                        $course['title'] = xarVarPrepForDisplay($realcourse['name']);
                        $course['credits'] = xarVarPrepForDisplay($realcourse['intendedcredits']);
                        $course['number'] = xarVarPrepForDisplay($realcourse['number']);
                        // See if there are any obtained credits
                        $course['obtained'] = xarModApiFunc('itsp','user','countobtained', array('lcourseid'=>$courseid, 'pitemid' => $pitemid, 'userid' =>$userid, 'itspid' => $itspid));

                        $enrollstatus = xarModApiFunc('courses','user','check_enrollstatus', array('userid' => $userid, 'courseid'=>$courseid));
                        // TODO: this returns an array. We now assume to take the first item, but this may not be correct.
                        if (!empty($enrollstatus[0]) && is_numeric($enrollstatus[0]['studstatus'])) {
                            $course['studstatus'] = xarModAPIFunc('courses', 'user', 'getstatus',
                                  array('status' => $enrollstatus[0]['studstatus']));
                            $course['obtcredits'] = $enrollstatus[0]['credits'];
                            $course['startdate'] = $enrollstatus[0]['startdate'];
                        } else {
                            $course['studstatus'] = '';
                            $course['obtcredits'] = '';
                            $course['startdate'] = '';
                        }
                        /* Add this item to the list of items to be displayed */
                        // TODO: place at correct place
                        $fullitem['courses'][] = $course;
                    }
                    if (strcmp($source, 'courses') == 0) {
                        break;
                    }
                // external courses
                case 'external':
                default:
                    // get all linked courses that already have been added to the ITSP for this plan item
                    $courselinks = xarModApiFunc('itsp','user','getall_itspcourses',array('itspid'=>$itspid, 'pitemid' => $pitemid));
                    // for each linked course get the details
                    if (!isset($courselinks) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
                    /*
                     * Loop through each item and display it.
                     */
                    foreach ($courselinks as $course) {
                        // Add read link
                        $courseid = $course['icourseid'];
                        if (xarSecurityCheck('ReadITSP', 0, 'ITSP', "$itspid:$planid:$userid")) {
                            $course['link'] = xarModURL('itsp',
                                'user',
                                'display_icourse',
                                array('icourseid' => $courseid));
                        } else {
                            $course['link'] = '';
                        }
                        /* Clean up the item text before display */
                        $course['title'] = xarVarPrepForDisplay($course['icoursetitle']);
                        $course['credits'] = $course['icoursecredits'];
                        $course['number'] = '';
                        // See if there are any obtained credits
                        $course['obtained'] = xarModApiFunc('itsp','user','countobtained', array('icourseid'=>$courseid, 'pitemid' => $pitemid, 'userid' =>$userid, 'itspid' => $itspid));
                        /* Add this item to the list of items to be displayed */
                        $fullitem['courses'][] = $course;
                    }
                    break;
                }
            $data['fullitems'][] = $fullitem;
            }
        }
    } else {
        $data['fullitems'] = array();
    }

    $item['itemtype'] = 2;
    // Add the ITSP to the array
    $data['item'] = $item;
    // Get the plan
    $plan = xarModApiFunc('itsp','user','get_plan',array('planid' => $planid));
    if (empty($plan) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $data['plan'] = $plan;
    // Security
    $data['authid'] = xarSecGenAuthKey();

    xarVarSetCached('Blocks.itsp', 'itspid', $itspid);
    /* Let any hooks know that we are displaying an item.
     */
    $item['returnurl'] = xarModURL('itsp',
        'user',
        'itsp',
       array('itspid' => $itspid));
    $item['itemtype'] = 99999;
    /* Call hooks */
    $hooks = xarModCallHooks('item',
        'display',
        $itspid,
        $item);
    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }

    /* Once again, we are changing the name of the title for better
     * Search engine capability.
     */
    $canapprove = false;
    if ($itspstatus == 4) {
        if (xarSecurityCheck('DeleteITSP', 0, 'ITSP', "$itspid:$planid:$userid")) {
            $canapprove = true;
        }
    }
    $data['canapprove'] = $canapprove;
    $itspuser = xarUserGetVar('name', $item['userid']);
    xarTplSetPageTitle(xarVarPrepForDisplay($itspuser));
    $data['uid'] = xarUserGetVar('uid');
    $data['itspuser'] = $itspuser;

    /* Return the template variables defined in this function */
    return $data;
}
?>