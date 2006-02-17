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
 * Display a survey page.
 * This displays a 'group' page for the *current* user.
 * It is called up the same way, whether jumping direct to
 * a group or on submitting a page.
 *
 *  Note:
 *  For now, the admin has a separate script for handling this stuff. That
 *  could be revised if the two can be merged.
 *
 * If the survey is locked, then the questions will be set
 * to 'readonly'.
 */

function surveys_user_showgroup() {
    //$dbconn =& xarDBGetConn(); $dbconn->LogSQL(false);

    // If a group ID is supplied, then the user is jumping direct to a group.
    if (!xarVarFetch('gid', 'int:0', $nextgid, 0, XARVAR_NOT_REQUIRED)) {return;}

    // The showmap parameter determines how much detail in the survey map is shown.
    if (!xarVarFetch('showmap', 'int:0:3', $showmap, 1, XARVAR_NOT_REQUIRED)) {return;}

    // Get the current survey details for the user.
    $current_survey = xarModAPIfunc(
        'surveys', 'user', 'getcurrentusersurvey'
    );

    if (empty($current_survey)) {
        // Error message - there is no current survey selected.
        $msg = xarML('NO CURRENT SURVEY OR NO PERMISSION TO VIEW');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return false;
    }

    // Check if the user is an admin for this survey
    $survey_editor = (xarSecurityCheck('EditSurvey', 0, 'Survey', $current_survey['sid']) ? true : false);

    // Get the current group ID.
    $current_gid = $current_survey['gid'];

    // If we have come fresh into a survey with a gid, then set that now.
    if ($current_gid == 0 && $nextgid > 0) {
        $current_gid = $nextgid;
    }

    //echo " current_gid=$current_gid ";
    // Get the user survey to ensure it exists.
    // TODO: should we replace current_survey everywhere below with usersurvey?
    $usersurvey = xarModAPIfunc(
        'surveys', 'user', 'getusersurvey',
        $current_survey
    );

    // Need at least read permission.
    if (!xarSecurityCheck('ReadAssessment', 1, 'Assessment', $usersurvey['sid'].':'.$usersurvey['system_status'].':'.$usersurvey['status'].':'.$usersurvey['uid'])) {
        // No read access to the survey, so zap it.
        $usersurvey = NULL;
    }

    if (empty($usersurvey)) {
        // Raise error - survey does not exist
        $msg = xarML('INVALID SURVEY DETAILS');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return false;
    }

    // If the survey is not open to the current user, then set
    // the 'readonly' flag.
    // The admin can override this, so include a security check here.
    // For normal users, they should only be able to comment on:
    // - their own surveys
    // - surveys with a system status of 'ACTIVE'
    if (!xarSecurityCheck('CommentAssessment', 0, 'Assessment', $usersurvey['sid'].':'.$usersurvey['system_status'].':'.$usersurvey['status'].':'.$usersurvey['uid'])) {
        $readonly = true;
    } else {
        $readonly = false;
    }

    // Now we have details of the current user survey, and
    // the last position of the user within that survey.

    // Indicates there are errors in the current group.
    $error_flag = false;

    // Indicates that a response was updated, and so the group
    // rules need to be reapplied.
    $updated_flag = false;

    // Direction we want to move from the current group (provided there are
    // no errors in the group). 1=next, -1=previous, 0=no move
    $direction = 0;

    // If the group is zero, then we must jump in at the start of the survey.
    // That involves finding the first group in the survey.

    // If the group is set, and we are not jumping direct to a new group,
    // then treat this as a form submission.
    if (!empty($current_gid) && empty($nextgid)) {
        // Get the current submit group question objects.
        $objects =& xarModAPIfunc(
            'surveys', 'user', 'getsubmitgroup', $current_survey // gid, uid and sid
        );
        if (empty($objects)) {
            // There are no questions in this group.
            // Set the group ID to zero, so it will be treated as a new entry to the
            // survey, i.e. user will be taken to the first group.
            $current_gid = 0;
        } else {
            // Check whether there are any errors in this group of questions.
            foreach ($objects as $object) {
                if (!$object->valid) {
                    $error_flag = true;
                }
                if ($object->updated) {
                    $updated_flag = true;
                }
            }
            //echo " updated="; var_dump($updated_flag);
            // If there are no errors, then we can go to the next or previous groups.
            // Find the direction we want to move in, according to the submit button
            // pressed.
            if (!$error_flag) {
                xarVarFetch('submit_next', 'isset', $submit_button, NULL, XARVAR_NOT_REQUIRED);
                if (isset($submit_button)) {
                    // Next.
                    $direction = 1;
                } else {
                    xarVarFetch('submit_prev', 'isset', $submit_button, NULL, XARVAR_NOT_REQUIRED);
                    if (isset($submit_button)) {
                        // Previous.
                        $direction = -1;
                    }
                }
            }
        }
    }

    // If changes have been made, update the groups statuses according
    // to the group rules (i.e. enable and disable groups according to
    // the dependancy rules).
    if ($updated_flag) {
        xarModAPIfunc('surveys', 'admin', 'applyresponserules', $usersurvey);
        // Update the last-update time on the user survey
        xarModAPIfunc('surveys', 'admin', 'update', array('usid' => $usersurvey['usid'], 'last_updated' => time()));
    }

    // Now we can get the current survey map.
    $map = xarModAPIfunc('surveys', 'user', 'getsurveymap', $usersurvey);

    // If we have somehow got stuck in a group that is disabled, then
    // jump to the start of the survey. This can happen if dependancy rules
    // are changed mid-survey.
    if (!isset($map['items'][$current_gid]['next'])) {
        // Set the next gid to the current invalid group, and it will be
        // processed in the next step.
        $nextgid = $current_gid;
    }

    // If the passed-in gid points to an invalid group then reset to the
    // start of the survey.
    // An invalid group is one with no questions, i.e. not part of the
    // normal prev/next sequence.
    // If the user has pointed to an empty group, it may be better
    // to move up to the next valid group. (done)
    if ($nextgid > 0 && !isset($map['items'][$nextgid]['next'])) {
        //$nextgid = $map['first'];
        reset($map['items']);
        $found = 0;
        while($item = next($map['items'])) {
            if ($item['gid'] == $nextgid) {
                $found = 1;
            }
            if ($found && isset($item['next'])) {
                $nextgid = $item['gid'];
                $found = 2;
                break;
            }
        }
        if ($found < 2) {
            // No groups following the requested group - go to the start of
            // the survey.
            $nextgid = $map['first'];
        }
    }

    if (empty($nextgid)) {
        // If the current group is zero, then default it to the first active group.
        if (empty($current_gid) || !isset($map['items'][$current_gid]['next'])) {
            $current_gid = $map['first'];
            // Don't move from there, as there is some error.
            $direction = 0;
        }

        if ($direction == 0) {
            $nextgid = $current_gid;
        }

        if ($direction == 1) {
            if (!empty($map['items'][$current_gid]['next'])) {
                $nextgid = $map['items'][$current_gid]['next'];
            } else {
                // Stay where we are: we are at the end.
                $nextgid = $current_gid;
            }
        }

        if ($direction == -1) {
            if (!empty($map['items'][$current_gid]['prev'])) {
                $nextgid = $map['items'][$current_gid]['prev'];
            } else {
                // Stay where we are: we are at the start.
                $nextgid = $current_gid;
            }
        }
    }

    // If we are now in a different group, discard the current question/response
    // object array, and get the new array for the new group.
    if ($nextgid != $current_gid || !isset($objects)) {
        // Set the next gid in the current survey array.
        // echo " nextgid=$nextgid current_gid=$current_gid ";
        $current_survey['gid'] = $nextgid;

        // Get the new questions and responses.
        unset($objects);
        $objects =& xarModAPIfunc(
            'surveys', 'user', 'getsubmitgroup', $current_survey
        );

        // Set the group in the user surveys variables.
        // i.e. write the current details back to the session or user variable.
        xarModAPIfunc('surveys', 'user', 'setcurrentsurveyvars', $current_survey);

        $current_gid = $nextgid;
    }


    // Change the status of the current user survey if necessary.
    // The status will flip between PROGRESS and SUBMITTABLE depending
    // upon whether all question responses are VALID or not.
    // This gives us a global flag to check, to see if the survey is
    // ready for submitting or not. The flag can only be calculated upon
    // building of the full survey map.
    switch ($usersurvey['status']) {
        case 'PROGRESS':
        if ($map['group_counts']['ACTIVE'] == $map['group_counts']['COMPLETE']) {
            // Count of active groups equals count of complete groups:
            // survey can be submitted now.
            xarModAPIfunc(
                'surveys', 'admin', 'update',
                array('usid' => $usersurvey['usid'], 'status' => 'SUBMITTABLE')
            );
            $usersurvey['status'] = 'SUBMITTABLE';
        }
        break;

        case 'SUBMITTABLE':
        if ($map['group_counts']['ACTIVE'] <> $map['group_counts']['COMPLETE']) {
            // Count of active groups not equal count of complete groups:
            // survey can not yet be submitted.
            xarModAPIfunc(
                'surveys', 'admin', 'update',
                array('usid' => $usersurvey['usid'], 'status' => 'PROGRESS')
            );
            $usersurvey['status'] = 'PROGRESS';
        }
        break;
    }


    // Get an array of ancestors for the current group.
    // This is useful when rendering the map to help show context.
    // Get the route by walking the tree, up through parent links.
    // Should normally only be two or three elements.
    $current_ancestors = array();
    $follow = $map['items'][$nextgid]['parent'];
    while ($follow > 0) {
        $current_ancestors[] = $follow;
        $follow = $map['items'][$follow]['parent'];
    }

    // Do some calculations for the master progress bar.
    // The total groups must always be greater than zero.
    // TODO: move this to a 'prepare_progress_bar' function?
    $total_groups = $map['group_counts']['ACTIVE'];
    $total_complete = $map['group_counts']['COMPLETE'];
    $total_invalid = $map['group_counts']['INVALID'];
    if ($total_complete == $total_groups) {
        // Just to avoid any rounding errors.
        $complete_percent = 100;
        $invalid_percent = 0;
    } else {
        $complete_percent = floor(($total_complete/$total_groups) * 100);
        if ($total_complete > 0 && $complete_percent == 0) {$complete_percent = 1;}
        // Again - to avoid rounding errors giving a total not equal to 100.
        if ($total_complete + $total_invalid == $total_groups) {
            $invalid_percent = 100 - $complete_percent;
        } else {
            $invalid_percent = floor(($total_invalid/$total_groups) * 100);
        }
    }
    $progress = array(
        'total' => $total_groups,
        'complete' => $total_complete,
        'invalid' => $total_invalid,
        'pc_complete' => $complete_percent,
        'pc_invalid' => $invalid_percent
    );

    // Add a few of the other variables to the map, so the map
    // data structure is completely self-contained.
    $map['showmap'] = $showmap;
    $map['current_gid'] = $current_gid;
    $map['current_ancestors'] = $current_ancestors;

    //$dbconn->LogSQL(false);
    // Display the questions, responses, map etc.
    return array(
        'map' => &$map,
        'showmap' => $showmap,
        'current_gid' => $nextgid,
        'current_ancestors' => $current_ancestors,
        'questions' => &$objects,
        'readonly' => $readonly,
        'progress' => &$progress,
        'usersurvey' => $usersurvey,
        'survey_editor' => $survey_editor
    );
}

?>