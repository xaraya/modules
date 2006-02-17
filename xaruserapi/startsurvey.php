<?php
/**
 * Start a user survey.
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
 * Start a user survey.
 *
 * No checks are made at this point as to whether a user
 * is permitted to start that survey - only that the survey
 * exists.
 * However - a survey of a given type can only be in progress
 * once for each user.
 * It does not matter if other surveys are in progress as
 * a user can have several surveys on the go at a time.
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * @param id    $sid The Survey ID
 *
 * @return id $usid The User Survey ID
 *
 * @throws      exceptionclass  [description]                [OPTIONAL A REQURIED]
 *
 * @access      public                                       [OPTIONAL A REQURIED]
 * @static                                                   [OPTIONAL]
 * @link       link to a reference                           [OPTIONAL]
 * @see        anothersample(), someotherlinke [reference to other function, class] [OPTIONAL]
 * @since      [Date of first inclusion long date format ]   [REQURIED]
 */

function surveys_userapi_startsurvey($args)
{
    // Expand arguments
    extract($args);

    // Get the survey details.
    $survey = xarModAPIfunc('surveys', 'user', 'getsurvey', array('sid' => $sid));
    if (empty($survey)) {
        $msg = xarML('The survey #(1) does not exist', $sid);
        xarExceptionSet(
            XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg)
        );
        return;
    }

    // Get the user ID.
    // If the user is logged in, then use the uid, otherwise the session ID.
    // Allow override of the uid for admins to start a survey by proxy for any user.
    if (empty($uid)) {
        if (xarUserIsLoggedIn()) {
            $uid = xarUserGetVar('uid');
        } else {
            $uid = xarSessionGetId();
        }
    }

    // Check if the user has a survey of this type already in progress.
    $existing_survey = xarModAPIfunc(
        'surveys', 'user', 'getusersurvey',
        array('uid' => $uid, 'sid' => $sid, 'system_status' => 'ACTIVE')
    );

    if (!empty($existing_survey)) {
        // Return the existing survey ID rather than a new one.
        // TODO: this will depend on whether multiple ongoing surveys are
        // allowed for this survey type.
        $usid = $existing_survey['usid'];
    } else {
        // Create the survey database record, i.e. create a new
        // user survey.
        $usid = xarModAPIfunc(
            'surveys', 'admin', 'createusersurvey',
            array('sid' => $sid, 'uid' => $uid)
        );

        // Now we should apply the dependancy rules immediately, so that areas
        // of the survey that are supposed to start disabled can do so.
        xarModAPIfunc('surveys', 'admin', 'applyresponserules', array('usid' => $usid));
    }

    if (empty($usid)) {return;}

    // Return the user survey ID.
    return $usid;
}

?>