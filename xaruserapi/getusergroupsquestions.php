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
 * Get the question and answer details for a user survey
 * and one or more groups.
 * This function just fetches the data for the questions,
 * and for the current responses. The data can be passed
 * to the objects for rendering later.
 */

function surveys_userapi_getusergroupsquestions($args) {
    // Expand arguments.
    extract($args);

    // Fetch DD data by default.
    if (!isset($dd_flag)) {$dd_flag = true;}

    // Get the user survey.
    $usersurvey = xarModAPIfunc('surveys', 'user', 'getusersurvey', $args);
    if (empty($usersurvey)) {return;}

    $bind = array();
    $where = array();

    // If a survey is specified, then limit groups to that survey.
    // The groups are selected using the "Celko" hierarchic model.
    $bind[] = $usersurvey['gid'];

    // Optionally limit to one or more specific questions.
    if (isset($qid)) {
        $bind[] = (int)$qid;
        $where[] = 'questions.xar_qid = ?';
    }

    // If just one group asked for, then stick it in the gids array.
    if (!isset($gids)) {
        $gids = array();
    }

    if (isset($gid)) {
        // Fix for PHP 5
        //$result = array_merge((array)$beginning, (array)$end);
        //$gids = array_merge($gids, $gid);
        $gids = array_merge((array)$gids, (array)$gid);
    }
    if (!empty($gids)) {
        $bind = array_merge($bind, $gids);
        $where[] = 'question_groups.xar_group_id IN (?' . str_repeat(',?', count($gids)-1) . ')';
    }

    if (!isset($gids) || !is_array($gids) || empty($gids)) {
        // Group IDs are mandatory.
        // No longer mandatory - we may want to select on all quesions in a survey
        // or a specific question in any group.
        // TODO: error message.
        //return;
    }

    // Database stuff.
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    // Query to fetch the questions.
    $query = 'SELECT question_groups.xar_group_id AS gid,'
        . ' question_types.xar_tid AS qtid, question_types.xar_name AS question_type_name,'
        . ' question_types.xar_response_type_id AS rtid, question_types.xar_object_name AS object_name,'
        . ' questions.xar_qid AS qid, questions.xar_name AS question_name, questions.xar_desc AS question_desc,'
        . ' questions.xar_mandatory AS mandatory, questions.xar_default AS default_value,'
        . ' question_groups.xar_template AS template, question_groups.xar_readonly AS readonly,'
        // xar_left is only selected for ordering
        . ' survey_groups.xar_left'
        . ' FROM ' . $xartable['surveys_questions'] . ' AS questions'
        // Every question has a type.
        . ' INNER JOIN ' . $xartable['surveys_types'] . ' AS question_types'
        . ' ON questions.xar_type_id = question_types.xar_tid'
        // Questions are linked to the group.
        . ' INNER JOIN ' . $xartable['surveys_question_groups'] . ' AS question_groups'
        . ' ON question_groups.xar_question_id = questions.xar_qid'
        // Get group details
        . ' INNER JOIN ' . $xartable['surveys_groups'] . ' AS survey_groups'
        . ' ON survey_groups.xar_gid = question_groups.xar_group_id'
        // Limit to the current survey only.
        . ' INNER JOIN ' . $xartable['surveys_groups'] . ' AS survey_groups_top'
        . ' ON survey_groups_top.xar_gid = ?'
        . ' AND survey_groups.xar_left BETWEEN survey_groups_top.xar_left AND survey_groups_top.xar_right'
        // The group is specified by the caller.
        . (!empty($where) ? ' WHERE ' . implode(' AND ', $where) : '')
        // Order the results. Put the groups into the survey hierachy order first.
        . ' ORDER BY survey_groups.xar_left, question_groups.xar_group_id, question_groups.xar_order, questions.xar_name';

    $result = $dbconn->execute($query, $bind);
    if (!$result) {return;}

    $questions = array();
    $qids = array();
    $qids_responses = array();
    while (!$result->EOF) {
        $question = $result->GetRowAssoc(0);
        $question['gid'] = (int)$question['gid'];
        $question['qid'] = (int)$question['qid'];
        $question['qtid'] = (int)$question['qtid'];
        $question['rtid'] = (int)$question['rtid'];

        // Make the readonly and mandatory flags boolean.
        $question['readonly'] = ($question['readonly'] == 'Y' ? true : false);
        $question['mandatory'] = ($question['mandatory'] == 'Y' ? true : false);

        // Keep a count of question types, questions, and the groups they appear in.
        $qids[$question['qtid']][$question['qid']] = $question['gid'];

        // Inject a few other tidbits of info.
        $question['usid'] = $usersurvey['usid'];

        // Get the current response, if this is a response type question.
        // TODO: do these in one go at the end.

        if (!empty($question['rtid'])) {
            // Note a question could sit in several groups.
            $qids_responses[$question['qid']][] = $question['gid'];
        }

        $questions[$question['gid']][$question['qid']] =& $question;
        unset($question);

        // Get next item.
        $result->MoveNext();
    }

    // If there are any responses expected, then fetch them all in one go.
    if (!empty($qids_responses)) {
        $responses = xarModAPIfunc(
            'surveys', 'user', 'getquestionresponses',
            array('usid'=>$usersurvey['usid'], 'qids' => array_keys($qids_responses), 'dd_flag' => $dd_flag)
        );
        //var_dump($responses);

        // A question may appear several times in a survey, so we need to drive this
        // from the groups and not the returned surveys.
        if (!empty($responses)) {
            foreach($qids_responses as $qid => $gids) {
                if (isset($responses[$qid])) {
                    //echo " gid=$gid qid=$qid "; var_dump($responses[$qid]);
                    // Normally a group of one, but occasionally a question will
                    // appear several times in a survey.
                    foreach($gids as $gid) {
                        $questions[$gid][$qid]['response'] = $responses[$qid];
                    }
                }
            }
        }
    }

    // Get DD if required.
    // We have built up arrays of question IDs, organised into groups of question type.
    // We can fetch all required DD data for each of these groups.
    // This is all done in the name of efficiency - keeping the numbers of database queries
    // down.
    if ($dd_flag) {
        foreach($qids as $itemtype => $itemids_groups) {
            $itemids = array_keys($itemids_groups);
            // Fix PHP5
            // if (xarModIsHooked('dynamicdata', 'surveys', $result['qtid'])) {
            if (xarModIsHooked('dynamicdata', 'surveys', $result->fields['qtid'])) {
                $dd_data = xarModAPIfunc(
                    'dynamicdata', 'user', 'getitems',
                    array('module'=>'surveys', 'itemtype' => $itemtype, 'itemids' => $itemids)
                );
                if (empty($dd_data)) {continue;}

                foreach ($itemids_groups as $qid => $gid) {
                    if (!empty($dd_data[$qid])) {
                        $questions[$gid][$qid]['dd'] = $dd_data[$qid];
                    }
                }
            }
        }
    }

    return $questions;
}

?>