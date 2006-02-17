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
 * Get a count of all questions for a survey.
 * sid: survey ID; or
 * name: survey name
 */

function surveys_userapi_countquestions($args) {
    // Expand arguments.
    extract($args);

    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $survey = xarModAPIfunc('surveys', 'user', 'getsurvey', $args);
    if (empty($survey)) {return;}

    // Formulate the query.
    // We need to walk the groups tree for the survey as questions
    // are linked to groups rather than directly to a survey.
    // Table aliases:-
    // group1: root question group for the survey
    // group2: tree walk of descendant groups for the survey
    // qgroup: question groups (linking questions to groups)
    // qresponse: questions requiring responses
    // qresponse: questions not requiring responses

    $query = 'SELECT group2.xar_gid, group2.xar_parent, COUNT(qresponse.xar_qid), COUNT(qnoresponse.xar_qid)'
        . ' FROM ' . $xartable['surveys_groups'] . ' AS group1'
        . ' INNER JOIN ' . $xartable['surveys_groups'] . ' AS group2'
        . ' ON group2.xar_left BETWEEN group1.xar_left AND group1.xar_right'
        . ' LEFT JOIN ' . $xartable['surveys_question_groups'] . ' AS qgroup'
        . ' ON qgroup.xar_group_id = group2.xar_gid'
        . ' LEFT JOIN ' . $xartable['surveys_questions'] . ' AS qresponse'
        . ' ON qgroup.xar_question_id = qresponse.xar_qid AND qresponse.xar_response_required = \'Y\''
        . ' LEFT JOIN ' . $xartable['surveys_questions'] . ' AS qnoresponse'
        . ' ON qgroup.xar_question_id = qnoresponse.xar_qid AND qnoresponse.xar_response_required = \'N\''
        . ' WHERE group1.xar_gid = ?'
        . ' GROUP BY group2.xar_gid, group2.xar_parent'
        . ' ORDER BY group2.xar_left';

    $result = $dbconn->execute($query, array((int)$survey['gid']));
    if (!$result) {return;}

    $counts = array();
    while (!$result->EOF) {
        list($gid, $parent, $count_response, $count_noresponse) = $result->fields;

        // Recurse the count up to the root.
        $walk = $parent;
        while (isset($counts[(int)$parent])) {
            $counts[(int)$parent]['response_desc'] += (int)$count_response;
            $counts[(int)$parent]['noresponse_desc'] += (int)$count_noresponse;

            // Break out if we have reached the root node.
            if ($parent == $counts[(int)$parent]['parent']) {break;}

            $parent = $counts[(int)$parent]['parent'];
        }

        $counts[(int)$gid] = array(
            'parent' => (int)$parent,
            'response' => (int)$count_response,
            'noresponse' => (int)$count_noresponse,
            'response_desc' => (int)$count_response,
            'noresponse_desc' => (int)$count_noresponse
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