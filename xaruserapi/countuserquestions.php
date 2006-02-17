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
 * Get a count of all available questions for a user survey.
 * usid/sid/uid/etc.: identify a user user.
 * sid: survey ID; or
 * name: survey name
 */

function surveys_userapi_countuserquestions($args) {
    // Expand arguments.
    extract($args);

    // Database stuff.
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    // Get the user survey.
    $usersurvey = xarModAPIfunc('surveys', 'user', 'getusersurvey', $args);
    if (empty($usersurvey)) {return;}

    // Formulate the query.
    // We need to walk the groups tree for the survey as questions
    // are linked to groups rather than directly to a survey.
    // Table aliases:-
    // group1: root question group for the survey
    // group2: tree walk of descendant groups for the survey
    // qgroup: question groups (linking questions to groups)
    // qresponse: questions requiring responses joined to qtypes
    // qnoresponse: questions not requiring responses jojned to qnotypes
    // A survey requiring a response has a response ID assocaited with its question type
    // and it also is not set to 'readonly' in the question group.

    $query = 'SELECT group2.xar_gid, group2.xar_parent, usergroups.xar_status,'
        . ' COUNT(qtypes.xar_tid), COUNT(qnotypes.xar_tid)'
        . ' FROM ' . $xartable['surveys_groups'] . ' AS group1'
        . ' INNER JOIN ' . $xartable['surveys_groups'] . ' AS group2'
        . ' ON group2.xar_left BETWEEN group1.xar_left AND group1.xar_right'
        . ' LEFT OUTER JOIN ' . $xartable['surveys_user_groups'] . ' AS usergroups'
        . ' ON usergroups.xar_group_id = group2.xar_gid AND usergroups.xar_user_survey_id = ?'
        . ' LEFT OUTER JOIN ' . $xartable['surveys_question_groups'] . ' AS qgroup'
        . ' ON qgroup.xar_group_id = group2.xar_gid'
        . ' LEFT OUTER JOIN ' . $xartable['surveys_questions'] . ' AS qresponse'
        . ' ON qgroup.xar_question_id = qresponse.xar_qid'

        . ' LEFT OUTER JOIN ' . $xartable['surveys_types'] . ' AS qtypes'
        . ' ON qtypes.xar_tid = qresponse.xar_type_id'
        . ' AND qtypes.xar_response_type_id > 0 AND qgroup.xar_readonly <> \'Y\''

        . ' LEFT OUTER JOIN ' . $xartable['surveys_questions'] . ' AS qnoresponse'
        . ' ON qgroup.xar_question_id = qnoresponse.xar_qid'

        . ' LEFT OUTER JOIN ' . $xartable['surveys_types'] . ' AS qnotypes'
        . ' ON qnotypes.xar_tid = qnoresponse.xar_type_id'
        . ' AND (qnotypes.xar_response_type_id = 0 OR qnotypes.xar_response_type_id IS NULL OR qgroup.xar_readonly = \'Y\')'
        //. ' AND qgroup.xar_readonly = \'Y\''

        . ' WHERE group1.xar_gid = ?'
        . ' GROUP BY group2.xar_gid, group2.xar_parent, usergroups.xar_status'
        . ' ORDER BY group2.xar_left';

    $result = $dbconn->execute($query, array((int)$usersurvey['usid'], (int)$usersurvey['gid']));
    if (!$result) {return;}

    $counts = array();
    while (!$result->EOF) {
        list($gid, $parent, $status, $count_response, $count_noresponse) = $result->fields;
        $gid = (int)$gid;
        $parent = (int)$parent;
        $count_response = (int)$count_response;
        $count_noresponse = (int)$count_noresponse;


        // If the group is NA (disabled) for the user survey, then there are no relevant questions;
        // set the counts to zero.
        if ($status == 'NA') {
            $count_response = 0;
            $count_noresponse = 0;
        }

        // Recurse the count up to the root.
        $walk = $parent;
        while (isset($counts[$parent])) {
            $counts[$parent]['response_desc'] += $count_response;
            $counts[$parent]['noresponse_desc'] += $count_noresponse;

            // Break out if we have reached the root node.
            if ($parent == $counts[$parent]['parent']) {break;}

            $parent = $counts[$parent]['parent'];
        }

        $counts[$gid] = array(
            'parent' => $parent,
            'response' => $count_response,
            'noresponse' => $count_noresponse,
            'response_desc' => $count_response,
            'noresponse_desc' => $count_noresponse
        );

        // Get next item.
        $result->MoveNext();
    }

    // Now we have a tree of question groups for a survey,
    // with counts of response-required and no-response-required questions
    // both at each node, and recursively for a node's descendants.
    // This can be used to prune the question group tree of empty groups.

    return $counts;
}

?>