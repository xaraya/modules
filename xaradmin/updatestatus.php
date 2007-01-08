<?php
/**
 * Update status for student
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Update participant item status from courses_admin_participants
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @since summer 2005
 * @param array sids Array of student ids with info about status in them
 * @param int statusid ??
 * @return bool true on success of update
 */
function courses_admin_updatestatus()
{
    // Get parameters
    if(!xarVarFetch('sids',      'array', $sids,       NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('statusid',  'int::', $statusid,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('planningid','id',    $planningid, NULL, XARVAR_DONT_SET)) {return;}//Change this?

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    if (!isset($sids) || count($sids) == 0) {
        $msg = xarML('No participants selected');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA',
                       new DefaultUserException($msg));
        return;
    }
    if (!isset($statusid) || !is_numeric($statusid) || $statusid < -1) { //Limit is not really necessary. Controlled by dyn data
        $msg = xarML('Invalid status');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_DATA',
                       new DefaultUserException($msg));
        return;
    }

    foreach ($sids as $sid => $val) {
        if ($val != 1) {
            continue;
        }
        // Get original article information
        $participant = xarModAPIFunc('courses',
                                 'user',
                                 'getparticipant',
                                 array('sid' => $sid));

        if (!isset($participant) || !is_array($participant)) {
            $msg = xarML('Unable to find #(1) item #(2)',
                         $descr, xarVarPrepForDisplay($sid));
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                           new SystemException($msg));
            return;
        }
        // Update the status now
        $participant['statusid'] = $statusid;
        $participant['sid'] = $sid;
        $participant['planningid'] = $planningid;

        // Pass to API
        if (!xarModAPIFunc('courses', 'admin', 'updateparticipant', $participant)) {
            return; // throw back
        }
    }
    unset($participant); //What does this do?

    // Return to the original admin view
    $lastview = xarSessionGetVar('Courses.LastView');
    if (isset($lastview)) {
        $lastviewarray = unserialize($lastview);
        if (!empty($lastviewarray['ptid']) && $lastviewarray['ptid'] == $ptid) {
            extract($lastviewarray);
            xarResponseRedirect(xarModURL('articles', 'admin', 'view',
                                          array('ptid' => $ptid,
                                                'catid' => $catid,
                                                'status' => $status,
                                                'startnum' => $startnum)));
            return true;
        }
    }

    if (empty($catid)) {
        $catid = null;
    }
    xarResponseRedirect(xarModURL('courses', 'admin', 'participants',
                                  array('planningid' => $planningid)));

    return true;
}

?>
