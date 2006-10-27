<?php
/**
 * Get a specific ITSP
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
 * Standard function of a module to retrieve a specific item
 *
 * @author the ITSP module development team
 * @since 26 Oct 2006
 * @param int $args ['itspid'] id of itsp item to get
 * @return array with item, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function itsp_userapi_getfullitsp($args)
{
    extract($args);
    /* Argument check - make sure that all required arguments are present and
     * in the right format, if not then set an appropriate error message
     * and return
     */
    if (!isset($itspid) || !is_numeric($itspid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'getfullitsp', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // Set vars

    $now = time();

    $item = array();
    $item['courses'] = array();
    // Get ITSP
    $itsp = xarModApiFunc('itsp','user','get',array('itspid' => $itsp));

    /* Create the item array
    $item = array('itspid'        => $itspid,
                  'userid'        => $userid,
                  'planid'        => $planid,
                  'itspstatus'    => $itspstatus,
                  'datesubm'      => $datesubm,
                  'dateappr'      => $dateappr,
                  'datecertreq'   => $datecertreq,
                  'datecertaward' => $datecertaward,
                  'datemodi'      => $datemodi,
                  'modiby'        => $modiby);


    */
    $pitems = xarModApiFunc('itsp','user','get_planitems',array('planid'=>$itsp['planid']));
    /*
    if (!empty($pitems)) {
        $item['pitemnames'] = array();
        // Enter items
        $sumcreditsnow = 0;
        foreach ($pitems as $item) {
            // Add modify link
            $pitemid= $item['pitemid'];
            $pitem = xarModApiFunc('itsp','user','get_planitem',array('pitemid'=>$pitemid));

            // Add credits so we can do calculations
            $item['mincredit'] = $pitem['mincredit'];
            $item['credits'] = $pitem['credits'];
            $item['pitemid']=$pitemid;
            $creditsnow = xarModApiFunc('itsp','user','countcredits',array('uid' => $userid, 'pitemid' => $pitemid, 'itspid' => $itspid));
            $item['creditsnow'] = $creditsnow;
            $sumcreditsnow = $sumcreditsnow + $creditsnow;
            // Format the name
            $item['pitemname'] = xarVarPrepForDisplay($pitem['pitemname']);
            $menu['pitems'][] = $item;
        }
        $menu['sumcreditsnow'] = $sumcreditsnow;
    }

    */

    foreach ($pitems as $fullitem) {
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
            // Check for mix possibility
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
                if (empty($courselinks)) {
                    break;
                }
                // for each linked course get the details
                foreach ($courselinks as $course) {
                    // Add read link
                    $courseid = $course['lcourseid'];

                    $realcourse = xarModApiFunc('courses','user','get', array('courseid'=>$courseid));
                    $course['title'] = xarVarPrepForDisplay($realcourse['name']);
                    $course['credits'] = xarVarPrepForDisplay($realcourse['intendedcredits']);
                    $course['number'] = xarVarPrepForDisplay($realcourse['number']);
                    $course['description'] = xarVarPrepForDisplay($realcourse['shortdesc']);
                    $enrollstatus = xarModApiFunc('courses','user','check_enrollstatus', array('userid' => $userid, 'courseid'=>$courseid));
                    // TODO: this returns an array. We now assume to take the first item, but this may not be correct.
                    // TODO: make sure we only allow credits that are truly obtained.
                    $course['obtcredits'] = '';
                    if (!empty($enrollstatus[0]) && is_numeric($enrollstatus[0]['studstatus'])) {
                        $course['studstatus'] = xarModAPIFunc('courses', 'user', 'getstatus',
                              array('status' => $enrollstatus[0]['studstatus']));
                        $course['startdate'] = $enrollstatus[0]['startdate'];
                        if (($course['startdate'] < $now) && ($course['startdate'] > 0)) {
                            $course['obtcredits'] = $enrollstatus[0]['credits'];
                        }
                    } else {
                        $course['studstatus'] = '';
                        $course['startdate'] = '';
                    }

                    /* Add this item to the list of items to be displayed */
                    // TODO: place at correct place
                    $item['courses'][] = $course;
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
                    //
                    $courseid = $course['icourseid'];
                    /* Clean up the item text before display */
                    $course['title'] = xarVarPrepForDisplay($course['icoursetitle']);
                    $course['credits'] = $course['icoursecredits'];
                    $course['number'] = '';
                    $course['obtcredits'] = $course['icourseresult'];
                    $course['studstatus'] = $course['icourseresult'];
                    $course['description'] = xarVarPrepForDisplay($course['icoursedesc']);
                    $course['icourseloc'] = xarVarPrepForDisplay($course['icourseloc']);
                    $course['startdate'] = $course['icoursedate'];
                    if ($course['icoursedate'] > $now || $course['icoursedate'] == 0) {
                        $course['obtcredits'] = 0;
                    } elseif (is_int($course['dateappr']) && ($course['dateappr'] > 0)) {
                        $course['obtcredits'] = $course['icoursecredits'];
                    }
                    /* Add this item to the list of items to be displayed */
                    $item['courses'][] = $course;
                }

                /*
                'icourseid'      => $icourseid,
                             'pitemid'        => $pitemid,
                             'icoursetitle'   => $icoursetitle,
                             'icourseloc'     => $icourseloc,
                             'icoursedesc'    => $icoursedesc,
                             'icoursecredits' => $icoursecredits,
                             'icourselevel'   => $icourselevel,
                             'icourseresult'  => $icourseresult,
                             'icoursedate'    => $icoursedate,
                             'dateappr'       => $dateappr,
                             'datemodi'       => $datemodi,
                             'modiby'         => $modiby
                */
                break;
            }
        }
    }

    /* Return the item array */
    return $item;
}
?>