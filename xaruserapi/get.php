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
 * Get a question group tree, starting at a group tree root.
 */

function surveys_userapi_get($args) {
    // Expand arguments.
    extract($args);

    // Database stuff.
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $bind = array();
    $where = array();

    // Question.
    if (isset($qid) && is_numeric($qid)) {
        // Get a question.
        $query = 'SELECT xar_qid AS qid, xar_type_id AS type_id, xar_name AS name,'
            . ' xar_desc AS description, xar_mandatory AS mandatory, xar_default AS default_value'
            . ' FROM ' . $xartable['surveys_questions'];
        $where[] = 'xar_qid = ?';
        $bind[] = (int)$qid;
    }

    // User response.
    if (isset($rid) && is_numeric($rid)) {
        // Get a question.
        $query = 'SELECT responses.xar_rid AS rid, responses.xar_user_survey_id AS user_survey_id,'
            . ' responses.xar_question_id AS question_id,'
            . ' qtypes.xar_response_type_id AS type_id,'
            . ' responses.xar_status AS status,'
            . ' responses.xar_value1 AS value1, responses.xar_value2 AS value2, responses.xar_value3 AS value3'
            . ' FROM ' . $xartable['surveys_user_responses'] . ' AS responses'
            . ' INNER JOIN ' . $xartable['surveys_questions'] . ' AS questions'
            . ' ON questions.xar_qid = responses.xar_question_id'
            . ' INNER JOIN ' . $xartable['surveys_types'] . ' AS qtypes'
            . ' ON qtypes.xar_tid = questions.xar_type_id';
        $where[] = 'xar_rid = ?';
        $bind[] = (int)$rid;
    }

    $query .= ' WHERE ' . implode(' AND ', $where);

    $result = $dbconn->execute($query, $bind);
    if (!$result || $result->EOF) {return;}

    $return = $result->GetRowAssoc(0);

    return $return;
}

?>