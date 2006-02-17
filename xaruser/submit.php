<?php
/**
 * Surveys table definitions function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @author Surveys module development team
 */
/*
 * Short Description [REQUIRED one line description]
 *
 * Long Description [OPTIONAL one or more lines]
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * @param int    $arg2  an integer and use description
 *                      Identing long comments               [OPTIONAL A REQURIED]
 *
 * @return int  type and name returned                       [OPTIONAL A REQURIED]
 *
 * @throws      exceptionclass  [description]                [OPTIONAL A REQURIED]
 *
 * @access      public                                       [OPTIONAL A REQURIED]
 * @static                                                   [OPTIONAL]
 * @link       link to a reference                           [OPTIONAL]
 * @see        anothersample(), someotherlinke [reference to other function, class] [OPTIONAL]
 * @since      [Date of first inclusion long date format ]   [REQURIED]
 * @deprecated Deprecated [release version here]             [AS REQUIRED]
 */
/*
 * Submit page for a user survey.
 */

function surveys_user_submit() {
    // User survey ID
    if (!xarVarFetch('usid', 'id', $usid, 0, XARVAR_NOT_REQUIRED)) {return;}

    // Stages are:
    // 0 - display welcome message
    // 1 - user submits survey
    // 2 - user confirms submit of survey
    // 3 - survey has been submitted
    // 4 - survey is not in a status capable of being submitted
    if (!xarVarFetch('stage', 'int:0:4', $stage, 0, XARVAR_NOT_REQUIRED)) {return;}

    // Make sure the current user owns the specified survey.
    // TODO: allow the admin to bypass this check.
    // The admin should set 'current_user' to false, then any
    // user survey can be selected.
    // TODO: *** set current_user to true for NON-admin users ***
    $usersurvey = xarModAPIfunc(
        'surveys', 'user', 'getusersurvey',
        array('current_user' => false, 'usid' => $usid)
    );

    if ($stage <= 2 && $usersurvey['status'] != 'SUBMITTABLE') {
        $stage = 4;
    }

    // Need comment privilege on the survey to submit it (and moderate
    // priv to change its status arbitrarily).
    if (!xarSecurityCheck('CommentAssessment', 0, 'Assessment', $usersurvey['sid'].':'.$usersurvey['system_status'].':'.$usersurvey['status'].':'.$usersurvey['uid'])) {
        // No read access to the survey, so zap it.
        $usersurvey = NULL;
    }

    // If no survey, then stop with an error.
    if (empty($usersurvey)) {
        $msg = xarML('INVALID USER SURVEY ID #(1) OR NO PRIVILEGE', $usid);
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return false;
    }

    switch ($stage) {
        case 0:
        case 1:
            break;
        case 2:
            // User has confirmed the survey is to be submitted.
            // TODO: do workflow lookup here to get the next status.
            // The status will depend upon the type of survey.
            $new_status = xarModAPIfunc(
                'surveys', 'user', 'workflow',
                array('action' => 'SUBMIT-USERSURVEY', 'status' => $usersurvey['status'], 'sid' => $usersurvey['sid'])
            );

            if (!empty($new_status)) {
                // Setup the first few columns we want to update.
                $update_details = array(
                    'usid' => $usersurvey['usid'],
                    'status' => $new_status
                );

                // Update the status
                xarModAPIfunc('surveys', 'admin', 'update', $update_details);

                // Transfer the details to the graphing system.
                // This may pass or fail, but we don't let the user know at this stage.
                // TODO: store the result against the user survey for quick reference.
                // Capture the result of the transfer.
                ob_start();
                xarModAPIfunc('surveys', 'admin', 'transfersurvey', array('usid' => $usid, 'debug' => true));
                $buffer = ob_get_contents();
                ob_end_clean();

                // Flag this as an event.
                xarModAPIfunc(
                    'surveys', 'user', 'event',
                    array(
                        'name' => 'USER_SUBMIT_SURVEY',
                        'usid' => $usersurvey['usid'],
                        'sid' => $usersurvey['sid'],
                        'usersurvey' => $usersurvey,
                        'transfer_log' => $buffer
                    )
                );

                // Flag success to the template.
                $stage = 3;
            }
            break;
        case 3:
        case 4:
            break;
        default:
            break;
    }

    // Check the current status allows submission.

    return array(
        'stage' => $stage,
        'usersurvey' => $usersurvey
    );
}

?>