<?php
/**
 * Surveys overview for user
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
 * Get overview of all surveys for a user.
 *
 * If no user is specified, then the current user is selected.
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * @param int    $arg2  an integer and use description
 *                      Identing long comments               [OPTIONAL A REQURIED]
 *
 * @return array  with surveys for this user
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

function surveys_userapi_overview($args) {
    // Expand arguments.
    extract($args);

    // Initialise data array.
    $data = array();

    // Get all surveys that the current (or specified) user is involved in.
    // This includes open, locked and closed surveys.
    // Pass in the specified user or the 'current user' flag.
    $all_user_surveys = xarModAPIfunc(
        'surveys', 'user', 'getusersurveys',
        (empty($uid) ? array('current_user' => true) : array('uid' => $uid))
    );
    //var_dump($all_user_surveys);

    // Get the current user survey, if there is one.
    // Pass in the user ID if specified.
    $current_survey = xarModAPIfunc(
        'surveys', 'user', 'getcurrentusersurvey',
        (empty($uid) ? array() : array('uid' => $uid))
    );
    //var_dump($current_survey);

    $data['current_survey'] =& $current_survey;

    // Split the user surveys into several groups.
    $zero_counts = array(
        'ACTIVE' => 0,
        'LOCKED' => 0,
        'CLOSED' => 0,
        'TOTAL'  => 0
    );
    $active = array();
    $locked = array();
    $closed = array();
    $counts = array();
    $counts[0] = $zero_counts;

    foreach ($all_user_surveys as $user_survey) {
        // Get a summary for each survey, consisting of a selection of responses,
        // formatted using a template defined at the survey level.
        $user_survey['summary'] = xarModAPIfunc(
            'surveys', 'user', 'usersurveyidentity',
            array(
                // A newline override can be passed in.
                // Not ideal, but does the job for now.
                'newline' => (isset($newline) ? $newline : '<br />'),
                'usid' => $user_survey['usid'],
                'template' => $user_survey['summary_template']
            )
        );

        // Assignment for convenience.
        $sid = $user_survey['sid'];

        // Keep a count of all statuses for each survey type (sid).
        // These are used to determine whether the user can start new surveys.
        if (!isset($counts[$sid])) {
            $counts[$sid] = $zero_counts;
        }

        if ($user_survey['system_status'] == 'ACTIVE') {
            // An active survey - the user is in the process of completing it.
            $active[] = $user_survey;
            $counts[$sid]['ACTIVE'] += 1;
            $counts[$sid]['TOTAL'] += 1;
            $counts[0]['ACTIVE'] += 1;
            $counts[0]['TOTAL'] += 1;
        }
        if ($user_survey['system_status'] == 'LOCKED') {
            // A locked survey - the user has submitted it, and it is being approved.
            $locked[] = $user_survey;
            $counts[$sid]['LOCKED'] += 1;
            $counts[$sid]['TOTAL'] += 1;
            $counts[0]['LOCKED'] += 1;
            $counts[0]['TOTAL'] += 1;
        }
        if ($user_survey['system_status'] == 'CLOSED') {
            // A closed survey - the survey is finally closed, and nothing more will be changed on it.
            $closed[] = $user_survey;
            $counts[$sid]['CLOSED'] += 1;
            $counts[$sid]['TOTAL'] += 1;
            $counts[0]['CLOSED'] += 1;
            $counts[0]['TOTAL'] += 1;
        }
    }

    //var_dump($counts);
    $data['active'] =& $active;
    $data['locked'] =& $locked;
    $data['closed'] =& $closed;
    $data['counts'] =& $counts;

    // Get a list of all surveys available.
    // Start with a list of all surveys, and remove the ones we don't want.
    // A survey may be removed if:
    // - the user does not have permissions to see it (not yet implemented)
    // - the user has already completed the maximum number
    // - the user has already started the maximum number
    // - the survey is not compatible with the logged-in status of the user
    $available = xarModAPIfunc('surveys', 'user', 'getsurveys', array('survey_key' => 'id'));
    $data['available'] =& $available;

    // Loop for each available survey, and put them in the 'new'
    // list if there is no reason not to.
    // This function is called when starting a new survey, to determine
    // whether the user is permitted to start that survey.
    // TODO: allow an admin extra rights to start any survey they like.
    $new = array();
    foreach ($available as $id => $survey) {
        // Check the counts.
        if (isset($counts[$survey['sid']])) {
            // Max in progress - the maximum number of surveys that can be in progress at any time.
            if ($survey['max_in_progress'] >= 0 && $counts[$survey['sid']]['ACTIVE'] >= $survey['max_in_progress']) {
                continue;
            }

            // Max instances - the maximum number of surveys (of this type) that can
            // be started at all.
            // Note: setting this to zero will mean a user cannot start this survey
            // at all. However, surveys can be 'pre-started' by an admin and handed
            // out to users, or some other process can by-pass this check and start
            // a survey for a user. This is likely to be how 'token-based' surveys
            // work, where a user is invited to take part in a survey through some
            // token they are given. The token is then used as a single-use ticket
            // for starting a survey, but not through the overview screen.
            if ($survey['max_instances'] >= 0 && $counts[$survey['sid']]['TOTAL'] >= $survey['max_instances']) {
                continue;
            }
        } else {
            $counts[$survey['sid']] = $zero_counts;
        }

        // Anonymous - can A.N.Other run this survey?
        // This could really be implemented as a generic privilege rule.
        if (!xarUserIsLoggedIn() && !$survey['anonymous']) {
            continue;
        }

        // TODO: Generic privilege rules.

        // We got this far, so the survey should go into the 'new' list.
        $new[$survey['sid']] = $survey;
    }
    $data['new'] =& $new;

    return $data;
}

?>