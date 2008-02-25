<?php
/**
 * Function to update a plan item
 *
 * @package modules
 * @copyright (C) 2006-2008 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Update an itsp planitem
 *
 * This function is called with the results of the
 * form supplied by xarModFunc('itsp','user','modify') to update an itsp. It sorts out what
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param  int itspid the id of the item to be updated
 * @param  int objectid universal identifier OPTIONAL
 * @param  int pitemid the number of the plan item to be updated
 * @param array invalid
 * @return mixed bool with true for success or false for failure OR
            array with information for the modify function
 * @since 20 feb 2006
 * @todo michelv: <1>why doesn't the sec check in here work?
 *
 */
function itsp_user_update()
{
    //extract($args);

    if (!xarVarFetch('itspid',   'id',    $itspid,   $itspid,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id',    $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('pitemid',  'id',    $pitemid,  $pitemid,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',  'array', $invalid,  array(),   XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $pitemid = $objectid;
    }
    // TODO: include check here for passing variables.

    // The user API function is called to get the ITSP
    $itsp = xarModAPIFunc('itsp',
                          'user',
                          'get',
                          array('itspid' => $itspid));
    $planid = $itsp['planid'];
    $userid = $itsp['userid'];
    // Set the former status id and see if we have an approved ITSP
    $oldstatus = $itsp['itspstatus'];
    $isapproved = false;
    if ($itsp['dateappr'] >0) {
        $isapproved = true;
    }
    /* Security check */
    if (!xarSecurityCheck('EditITSP', 1, 'ITSP', "$itspid:$planid:$userid")) {
        return;
    }
     /* Confirm authorisation code. */
    if (!xarSecConfirmAuthKey('itsp.modify')) return;

    // Check to see if we are already dealing with a planitem
    if (!empty($pitemid) && is_numeric($pitemid)) {
        //get planitem
        $pitem = xarModApiFunc('itsp','user','get_planitem',array('pitemid'=>$pitemid));
        $data['pitemrules'] = $pitem['pitemrules'];
        // Splice the rule
        $rules = xarModApiFunc('itsp','user','splitrules',array('rules'=>$pitem['pitemrules']));
        $data['rule_type'] = $rules['rule_type'];
        $data['rule_level'] = $rules['rule_level'];
        $data['rule_cat'] = $rules['rule_cat'];
        $data['rule_source'] = $rules['rule_source'];

        // See if mix is true, then we need both sources
        if ($rules['mix']) {
            $source = 'mix';
        } else {
            $source = $rules['rule_source'];
        }

        if ((strcmp($source, 'courses') == 0) || (strcmp($source, 'mix') == 0)) {
            // Then we are adding a course, if this id is set
            if (!xarVarFetch('lcourseid', 'id',    $lcourseid, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('courseid', 'id',     $courseid,  '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('dateappr',  'str::', $dateappr,  '', XARVAR_NOT_REQUIRED)) return;
            // Make sure we will not add the empty string as a course
            if (!empty($lcourseid) && $lcourseid > 0) {
                // Create a new linked course
                if (!xarModApiFunc('itsp','admin','create_courselink',
                                    array('itspid' =>$itspid,
                                          'pitemid' => $pitemid,
                                          'lcourseid' => $lcourseid,
                                          'dateappr' => $dateappr)
                                    )) {
                    xarSessionSetVar('statusmsg', xarML('Course Item was NOT added!'));
                    return;
                } else {
                    xarSessionSetVar('statusmsg', xarML('Course Item was successfully added!'));
                }
            } else {
                xarSessionSetVar('statusmsg', xarML('There was nothing to do 1'));
            }
        }
        if ((strcmp($source, 'courses') != 0) || $rules['mix']) {
            // else update the itsp course
            if (!xarVarFetch('icourseid',       'id',           $icourseid, '',   XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('icoursetitle',    'str:1:255',    $icoursetitle, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('icourseloc',      'str:1:255',    $icourseloc, '',  XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('icoursedesc',     'str::',        $icoursedesc, '',   XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('icoursecredits',  'float:0.1:',   $icoursecredits, '',   XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('icourselevel',    'str:1:255',    $icourselevel, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('icourseresult',   'str:1:255',    $icourseresult, '',  XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('icoursedate',     'str::',        $icoursedate, '',   XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('dateappr',        'str::',        $dateappr, '',   XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('displaytitle',    'str:1:255',    $displaytitle, xarML('external course'),  XARVAR_NOT_REQUIRED)) return;
            // if (!xarVarFetch('authid',         'str::',     $authid,         '', XARVAR_NOT_REQUIRED)) return;
            /* Confirm authorisation code.*/
            // if (!xarSecConfirmAuthKey($authid)) return;
            // Return to form if we do not validate this item
            if (empty($icoursetitle) && !empty($icoursecredits)) {
                $invalid['icoursetitle'] = 1;
                xarSessionSetVar('statusmsg', xarML('Please add a title'));
            }
            if (!empty($icoursetitle) && empty($icoursecredits)) {
                $invalid['icoursecredits'] = 1;
                xarSessionSetVar('statusmsg', xarML('Please add credits'));
            }
                // check if we have any errors
            if (count($invalid) > 0) {
                return xarModFunc('itsp', 'user', 'modify',
                                  array('itspid' => $itspid,
                                       'pitemid' => $pitemid,
                                       'icourseid'=>$icourseid,
                                       'icoursetitle'=> $icoursetitle,
                                       'icourseloc'=>  $icourseloc,
                                       'icoursedesc'=> $icoursedesc,
                                       'icoursecredits'=>  $icoursecredits,
                                       'icourselevel'=> $icourselevel,
                                       'icourseresult'=> $icourseresult,
                                       'icoursedate'=> $icoursedate,
                                       'dateappr'=> $dateappr,
                                       'displaytitle' => $displaytitle,
                                       'invalid'      => $invalid));
            }
            // See if we update an item
            if (!empty($icourseid)) {
                if(!xarModApiFunc('itsp',
                                   'admin',
                                   'update_icourse',
                                   array(
                                   'itspid' => $itspid,
                                   'pitemid' => $pitemid,
                                   'icourseid'=>$icourseid,
                                   'icoursetitle'=> $icoursetitle,
                                   'icourseloc'=>  $icourseloc,
                                   'icoursedesc'=> $icoursedesc,
                                   'icoursecredits'=>  (float) $icoursecredits,
                                   'icourselevel'=> $icourselevel,
                                   'icourseresult'=> $icourseresult,
                                   'icoursedate'=> $icoursedate,
                                   'dateappr'=> $dateappr,
                                   'displaytitle' => $displaytitle)
                                   )) {
                    return;
                }
                xarSessionSetVar('statusmsg', xarML('ITSP Item was successfully updated!'));

            } elseif (!empty($icoursetitle) && !empty($icoursecredits)) {

                $icourseid = xarModApiFunc('itsp',
                                   'admin',
                                   'create_icourse',
                                   array(
                                   'itspid' => $itspid,
                                   'pitemid' => $pitemid,
                                   'icourseid'=>$icourseid,
                                   'icoursetitle'=> $icoursetitle,
                                   'icourseloc'=>  $icourseloc,
                                   'icoursedesc'=> $icoursedesc,
                                   'icoursecredits'=> (float) $icoursecredits,
                                   'icourselevel'=> $icourselevel,
                                   'icourseresult'=> $icourseresult,
                                   'icoursedate'=> $icoursedate,
                                   'dateappr'=> $dateappr,
                                   'displaytitle' => $displaytitle)
                                   );
                /* The return value of the function is checked here, and if the function
                 * suceeded then an appropriate message is posted.
                 */
                if (!isset($icourseid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
                    xarSessionSetVar('statusmsg', xarML('The #(1) was NOT created!',$displaytitle));
                    return false; // throw back
                }
                xarSessionSetVar('statusmsg', xarML('ITSP Item was successfully added!'));
            }
        }

        // 3 = updated 1=in progress
        // TODO: move this to later?
        // Set the new status to updated one.
        $newstatus = 1;
        if ($oldstatus == 5 && !$isapproved) {
            $newstatus = 5;
        } elseif ($oldstatus == 5 && $isapproved) {
            $newstatus = 3;
        } elseif ($oldstatus == 3 && $isapproved) {
            $newstatus = 3;
        }
        $updatestatus = xarModApiFunc('itsp','user','update',array('itspid' => $itspid, 'newstatus' => $newstatus));
        if (!$updatestatus) {
            xarSessionSetVar('statusmsg', xarML('The ITSP with id #(1) was NOT found!',$itspid));
            return false; // throw back
        }
    } else {
        xarSessionSetVar('statusmsg', xarML('There was nothing to do'));
    }
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('itsp', 'user', 'modify', array('itspid'=>$itspid, 'pitemid'=> $pitemid)));
    /* Return */
    return true;
}
?>