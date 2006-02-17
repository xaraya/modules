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
 * Review a user survey.
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
 * Review a user survey.
 * Provides a complete (printable) summary of a survey, with some links
 * to the questions so the user can change questions if necessary.
 * This review function does not do any updating.
 * Links to change the questions are only given if the system_status of
 * the user status is 'ACTIVE'.
 * The survey results structure here can get very large. Not sure if it
 * will start to blow any limits on the larger surveys...?
 * TODO: support reporting of specific sub-trees only (rooted by $gid)
 *  these can then be arranged into tabs or some other visual structure.
 */

function surveys_user_review() {
    //$dbconn =& xarDBGetConn();
    //$dbconn->LogSQL(false);

    // Increase the timeout a little.
    set_time_limit(60);

    // User survey ID - can over-ride the 'current' survey.
    if (!xarVarFetch('usid', 'id', $usid, 0, XARVAR_NOT_REQUIRED)) {return;}

    // Get the current survey if none passed in, otherwise check the
    // user survey exists and the user is allowed to look at it.
    if (empty($usid)) {
        $usersurvey = xarModAPIfunc('surveys', 'user', 'getcurrentusersurvey');
        if (!empty($usersurvey)) {
            $usid = $usersurvey['usid'];
        }
    }

    if (!empty($usid)) {
        // Check the user survey exists.
        // TODO: admin would not require 'current_user' to be set, otherwise
        // the returned survey is restricted to surveys owned by the current
        // user.
        $usersurvey = xarModAPIfunc(
            'surveys', 'user', 'getusersurvey',
            array('current_user' => false, 'usid' => $usid)
        );
    }

    // Security check: need at least read privilege.
    if (!xarSecurityCheck('ReadAssessment', 0, 'Assessment', $usersurvey['sid'].':'.$usersurvey['system_status'].':'.$usersurvey['status'].':'.$usersurvey['uid'])) {
        // No read access to the survey, so zap it.
        $usersurvey = NULL;
    }

    if (empty($usersurvey)) {
        // Error message - user survey does not exist (or no privilege to see).
        $msg = xarML('INVALID SURVEY DETAILS OR NO PERMISSION');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Get the map so we can go through each group in turn.
    $map = xarModAPIfunc('surveys', 'user', 'getsurveymap', $usersurvey);
    //var_dump($map);

    $gids = array();

    // Loop for each group.
    $mapitems =& $map['items'];
    foreach($mapitems as $mapitemkey => $mapitem) {
        // Skip over NA groups
        if (!isset($mapitem['status']) || $mapitem['status'] == 'NA' || $mapitem['count'] == 0) {
            // Unset the groups we don't need, to keep the data structures down a bit.
            unset($mapitems[$mapitemkey]);
            continue;
        }

        // Keep the gid and mapkey for later.
        $gids[$mapitem['gid']] = $mapitemkey;
    }

    // Get questions and responses for all groups.
    $questions = xarModAPIfunc(
        'surveys', 'user', 'getusergroupsquestions',
        array('usid' => $usid, 'gids' => array_keys($gids))
    );
    //var_dump($questions);
    foreach($questions as $gid => $group_questions) {
        $mapitems[$gids[$gid]]['questions'] = $group_questions;

        // Loop for each question in the group.
        foreach($group_questions as $questionkey => $question) {
            //var_dump($question);
            // Get the object for the question.
            if (isset($question_object)) {unset($question_object);}
            $question_object = xarModAPIfunc(
                'surveys', 'user', 'newquestionobject',
                array('question' => $question)
            );

            // Save the question object in the map.
            $mapitems[$gids[$gid]]['questions'][$questionkey]['object'] =& $question_object;
            //var_dump($question_object);
        }
    }

    // If the user has comment privilege on the survey, then flag this so links can be
    // provided on the map.
    if (xarSecurityCheck('CommentAssessment', 0, 'Assessment', $usersurvey['sid'].':'.$usersurvey['system_status'].':'.$usersurvey['status'].':'.$usersurvey['uid'])) {
        $editable = true;
    } else {
        $editable = false;
    }

    //$dbconn->LogSQL(false);

    return array(
        'usersurvey' => $usersurvey,
        'map' => $map,
        'editable' => $editable
    );
}

?>