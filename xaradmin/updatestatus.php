<?php

/**
 * update participant item from courses_admin_modify
 */
function courses_admin_updatestatus()
{
    // Get parameters
    if(!xarVarFetch('sids',   'isset', $sids,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('statusid', 'int::', $statusid,  NULL, XARVAR_DONT_SET)) {return;}
   // if(!xarVarFetch('catid',  'isset', $catid,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('planningid',   'isset', $planningid,    NULL, XARVAR_DONT_SET)) {return;}//Change this?

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
/* We don't need this here
    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');
    if (!empty($ptid)) {
        $descr = $pubtypes[$ptid]['descr'];
    } else {
        $descr = xarML('Articles');
        $ptid = null;
    }
*/
    // We need to tell some hooks that we are coming from the update status screen
    // and not the update the actual article screen.  Right now, the keywords vanish
    // into thin air.  Bug 1960 and 3161
    xarVarSetCached('Hooks.all','noupdate',1);

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
        
        // $article['ptid'] = $article['pubtypeid'];// Need equivalent?
        // Security check
        
/* Needs rewrite
        $input = array();
        $input['article'] = $article;
        if ($status < 0) {
            $input['mask'] = 'DeleteArticles';
        } else {
            $input['mask'] = 'EditArticles';
        }
        if (!xarModAPIFunc('articles','user','checksecurity',$input)) {
            $msg = xarML('You have no permission to modify #(1) item #(2)',
                         $descr, xarVarPrepForDisplay($aid));
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                           new SystemException($msg));
            return;
        }
*/
/*
        if ($statusid < 0) {
            // Pass to API
            if (!xarModAPIFunc('articles', 'admin', 'delete', $article)) {
                return; // throw back
            }
        } else {
*/
            // Update the status now
            $participant['statusid'] = $statusid;
            $participant['sid'] = $sid;
            $participant['planningid'] = $planningid;

            // Pass to API
            if (!xarModAPIFunc('courses', 'admin', 'updateparticipant', $participant)) {
                return; // throw back
            //}
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
