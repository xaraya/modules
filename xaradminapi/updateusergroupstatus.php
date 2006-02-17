<?php
/**
 * Surveys Update the status of a user response group
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @author Surveys module development team
 */
/**
 * Update the status of a user response group
 *
 * Update the status of a user response group, according
 * to the statuses of the questions that it contains.

 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param  args: usid/sid/uid and gid
 *
 * @return bool
 *
 * @throws      exceptionclass  [description]                [OPTIONAL A REQURIED]
 *
 * @access      public                                       [OPTIONAL A REQURIED]
 * @static                                                   [OPTIONAL]
 * @link       link to a reference                           [OPTIONAL]
 * @see        anothersample(), someotherlinke [reference to other function, class] [OPTIONAL]
 * @since      [Date of first inclusion long date format ]   [REQURIED]
 */

function surveys_adminapi_updateusergroupstatus($args) {
    // Expand arguments.
    extract($args);

    // Get the user survey details.
    $usersurvey = xarModAPIfunc(
        'surveys', 'user', 'getusersurvey', $args
    );

    if (empty($usersurvey)) {
        // TODO: raise error - survey does not exist
        echo "INVALID SURVEY DETAILS";
        return;
    }

    if (empty($gid)) {
        // TODO: error message - the group ID must exist.
        echo "MISSING GID";
        return;
    }

    // Database stuff.
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    // There are four states for the user response group:
    // 1. NORESPONSE - there are questions requiring a response,
    //    but none have been provided.
    // 2. INVALID - there is at least one question INVALID in this group
    // 3. COMPLETE - all questions in the group are COMPLETE
    // 4. NA - the group is not applicable in the current context

    // 2. and 3. are the easiest to check for first.

    // Get a count of invalid and complete responses.
    $query = 'SELECT COUNT(responses_complete.xar_status), COUNT(responses_invalid.xar_status)'

        . ' FROM ' . $xartable['surveys_question_groups'] . ' AS qgroups'

        . ' LEFT OUTER JOIN ' . $xartable['surveys_user_responses'] . ' AS responses_complete'
        . ' ON responses_complete.xar_question_id = qgroups.xar_question_id'
        . ' AND (responses_complete.xar_status = \'COMPLETE\' OR responses_complete.xar_status = \'NA\')'
        . ' AND responses_complete.xar_user_survey_id = ?'

        . ' LEFT OUTER JOIN ' . $xartable['surveys_user_responses'] . ' AS responses_invalid'
        . ' ON responses_invalid.xar_question_id = qgroups.xar_question_id'
        . ' AND responses_invalid.xar_status = \'INVALID\''
        . ' AND responses_invalid.xar_user_survey_id = ?'

        . ' WHERE qgroups.xar_group_id = ?';

    $result = $dbconn->execute($query, array((int)$usersurvey['usid'], (int)$usersurvey['usid'], (int)$gid));
    if (!$result) {return;}
    list($complete, $invalid) = $result->fields;

    $complete = (int)$complete;
    $invalid = (int)$invalid;

    if ($invalid > 0) {
        // A single invalid response makes the whole group invalid.
        $new_status = 'INVALID';
    } elseif ($complete > 0) {
        // At least one complete response means the group has been responded to.
        $new_status = 'COMPLETE';
    } else {
        // No responses at all for this group.
        // We will set it to 'NORESPONSE' for now, but we should check whether
        // the group contains any questions that require a response.
        // TODO: move this to an API function of its own (and cache the results).
        $query = 'SELECT COUNT(questions.xar_qid)'
            . ' FROM ' . $xartable['surveys_question_groups'] . ' AS qgroups'
            // Join the groups to the questions.
            . ' INNER JOIN ' . $xartable['surveys_questions'] . ' AS questions'
            . ' ON questions.xar_qid = qgroups.xar_question_id'
            // Now look at the question types to see if a response is needed.
            . ' INNER JOIN ' . $xartable['surveys_types'] . ' AS qtypes'
            . ' ON qtypes.xar_tid = questions.xar_type_id'
            . ' WHERE qgroups.xar_group_id = ?'
            . ' AND (qtypes.xar_response_type_id IS NOT NULL AND qtypes.xar_response_type_id > 0)';
        $result = $dbconn->execute($query, array((int)$gid));
        if (!$result) {return;}
        list($response_req_count) = $result->fields;
        if ($response_req_count > 0) {
            // One or more questions do require a response.
            $new_status = 'NORESPONSE';
        } else {
            // No responses required, so just visiting this group is enough
            // to set it to complete.
            $new_status = 'COMPLETE';
        }
    }

    // Now we have a status for the group. Get the current status.
    $query = 'SELECT xar_ugid, xar_status'
        . ' FROM ' . $xartable['surveys_user_groups']
        . ' WHERE xar_user_survey_id = ? AND xar_group_id = ?';

    $result = $dbconn->execute($query, array((int)$usersurvey['usid'], (int)$gid));
    if (!$result) {return;}
    if (!$result->EOF) {
        list($ugid, $status) = $result->fields;
        if ($status != $new_status) {
            // Status is different - update the database.
            // TODO: put this into an API function. It is used in at least two places.
            $query = 'UPDATE ' . $xartable['surveys_user_groups']
                . ' SET xar_status = ?'
                . ' WHERE xar_ugid = ?';
            $result = $dbconn->execute($query, array($new_status, (int)$ugid));

            // Capture this also as an event: group changed from status A to status B.
            xarModAPIfunc(
                'surveys', 'user', 'event',
                array(
                    'name' => 'GROUP_STATUS',
                    'ugid' => $ugid,
                    'gid' => $gid,
                    'new_status' => $new_status,
                    'old_status' => $status,
                    'usersurvey' => $usersurvey
                )
            );

            // If the new status is 'COMPLETE' then check if this is the last of its
            // siblings to be completed.
            if ($new_status == 'COMPLETE') {
                $parent_details = xarModAPIfunc(
                    'surveys', 'user', 'checkparentgroupcomplete',
                    array('ugid' => $ugid)
                );
                if (!empty($parent_details)) {
                    xarModAPIfunc(
                        'surveys', 'user', 'event',
                        array(
                            'name' => 'GROUP_COMPLETE',
                            'ugid' => $ugid,
                            'gid' => $parent_details['gid'],
                            'child_gid' => $gid,
                            'group_name' => $parent_details['name'],
                            'group_desc' => $parent_details['desc'],
                            'old_status' => $status,
                            'usersurvey' => $usersurvey
                        )
                    );
                }
            }
        }
    } else {
        // There is no user response group - create one now.
        // TODO: put this into an API function. It is used in at least two places.
        $ugid = $dbconn->GenId($xartable['surveys_user_groups']);
        $query = 'INSERT INTO ' . $xartable['surveys_user_groups']
            . ' (xar_ugid, xar_user_survey_id, xar_group_id, xar_status)'
            . ' VALUES(?, ?, ?, ?)';
        $result = $dbconn->execute($query, array((int)$ugid, (int)$usersurvey['usid'], (int)$gid, $new_status));
        $ugid = (int)$dbconn->PO_Insert_ID($xartable['surveys_user_groups'], 'xar_ugid');
    }

    return true;
}
?>