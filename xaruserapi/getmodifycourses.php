<?php
/**
 * Get courses for the modify
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Get a specific ITSP
 *
 * Get all courses to modify
 *
 * @author the ITSP module development team
 * @since 16 Sept 2006
 * @param  $args ['itspid'] id of itsp item to get
 * @param  $args ['userid'] id of the user to get the itsp for
 * @return array with item, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function itsp_userapi_getmodifycourses($args)
{
    extract($args);
    /* Argument check - make sure that all required arguments are present and
     * in the right format, if not then set an appropriate error message
     * and return
     */
                 //itspid
            //pitemid
            //planid
    if ((!isset($itspid) || !is_numeric($itspid)) && (!isset($pitemid) || !is_numeric($pitemid))) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'get', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    $data = array();
    // get the pitem details for this itsp
    // get all linked courses that already have been added to the ITSP for this pitemid
    $courselinks = xarModApiFunc('itsp','user','getall_courselinks',array('itspid'=>$itspid, 'pitemid' => $pitemid));
    // for each linked course get the details
    if (!isset($courselinks) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

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
        $lcourse['intendedcredits'] = xarVarPrepForDisplay($course['intendedcredits']);
        // Add a delete link
        $lcourse['deletelink'] = xarModURL('itsp','admin','delete_courselink',array('courselinkid' => $lcourse['courselinkid'], 'authid' => xarSecGenAuthKey('itsp'), 'pitemid' => $pitemid, 'itspid' => $itspid));
        $enrollstatus = xarModApiFunc('courses','user','check_enrollstatus', array('userid' => $userid, 'courseid'=>$courseid));
        if (!empty($enrollstatus[0])  && is_numeric($enrollstatus[0]['studstatus'])){
            $lcourse['studstatus'] = xarModAPIFunc('courses', 'user', 'getstatus',
                  array('status' => $enrollstatus[0]['studstatus']));
            $lcourse['credits'] = $enrollstatus[0]['credits'];
            $lcourse['startdate'] = $enrollstatus[0]['startdate'];
            $creditsnow = $creditsnow + $enrollstatus[0]['credits'];
        } else {
            $lcourse['studstatus'] = '';
            $lcourse['credits'] = '';
            $lcourse['startdate'] = '';
            $creditsnow = $creditsnow + $course['intendedcredits'];
        }
        /* Add this item to the list of items to be displayed */
        $data['lcourses'][] = $lcourse;
        $data['creditsnow'] = $creditsnow;
    }
    /* Return the item array */
    return $data;
}
?>