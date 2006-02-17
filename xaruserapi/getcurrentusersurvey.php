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
 * Get the details of the current user survey.
 * The user is only allowed to carry out one
 * survey at any time. It is possible to have several
 * surveys 'on the go' at once, but only one of them
 * is 'current' - the user must explicitly switch
 * between them to do bits of different surveys.
 */

function surveys_userapi_getcurrentusersurvey($args) {
    // Structure of the current_survey:
    //
    // 'sid' = survey ID
    // 'usid' = user survey ID
    // 'gid' = group ID
    // 'uid' = user ID

    // Variable name.
    $name = 'surveys.current_survey';

    // Expand arguments.
    extract($args);

    // If the user is logged in, then fetch the details from
    // the user variables.
    if (isset($current_survey)) {unset($current_survey);}
    if (!empty($uid)) {
        // Getting the current survey for a specified user.
        // If we don't find a survey for this user id, then we won't
        // start looking in the session.
        $current_survey = xarModGetUserVar('surveys', $name, $uid);
    } else {
        // Getting the current survey for the current user.
        if (xarUserIsLoggedIn()) {
            $current_survey = xarModGetUserVar('surveys', $name);
            // Having to serialize/unserialise the module var is a real pain.
            if (isset($current_survey)) {
                $current_survey = unserialize($current_survey);
            }
        }

        if (empty($current_survey)) {
            // Not logged in or failed to get a survey from the user vars.
            // See if there is a survey in the session.
            $current_survey = xarSessionGetVar($name);

            if (empty($current_survey)) {
                // TODO: raise error message - no survey anywhere.
                return;
            }

            // If we are logged in, then move the survey details to the user vars.
            if (xarUserIsLoggedIn()) {
                // FIXME: this does not work at the moment (the user seems to have two
                // independant surveys when logged in or anonymous. Perhaps when logged in
                // we need to check for any anonymous surveys under that session and move
                // it in to the user's account (perhaps not if many users share a single
                // machine?)
                // There could be many surveys keyed only on the current session
                // The survey user ID will need to be changed from the session
                // ID to the user ID. That is, if the survey exists. This allows a user
                // to start a survey anonymously, then register to save their survey
                // part-way through.
                // The existing survey will have an ID xarSessionGetId().
                // If the survey with that ID does not exist, then the session has
                // expired or changed, and the survey is lost.

                $usersurvey = xarModAPIfunc(
                    'surveys', 'user', 'getusersurvey',
                    array('uid' => $current_survey['uid'], 'usid' => $current_survey['usid'])
                );

                if (!empty($usersurvey)) {
                    // Set the survey uid to the current user ID (would have been the
                    // session ID).
                    $current_survey['uid'] = xarUserGetVar('uid');

                    // Same for the uid of the survey in the database.
                    xarModAPIfunc(
                        'surveys', 'admin', 'update',
                        array('usid' => $current_survey['usid'], 'uid' => $current_survey['uid'])
                    );
                }

                // Remove the session survey var.
                xarSessionDelVar($name);

                // Save the details in the user vars.
                xarModSetUserVar('surveys', $name, serialize($current_survey));
            }
        }
    }

    return $current_survey;
}

?>