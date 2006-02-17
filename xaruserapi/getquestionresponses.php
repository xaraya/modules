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
 * Get the details of a user's response to a single question.
 * Arguments:
 * name: question name
 * qid: question ID; or
 * qids: question ID
 * uid: user ID
 * sid: survey ID
 * usid: user survey ID
 * dd_flag: true if DD details to be fetched for the response
 *
 * If uid/sid are passed to getusersurvey() to determine the usid.
 * name or qid must be supplied to determine the question.
 *
 * Returns: NULL on error, array() if no response matches.
 */

function surveys_userapi_getquestionresponses($args) {
    // Expand arguments.
    extract($args);

    // Fetch DD data by default.
    if (!isset($dd_flag)) {$dd_flag = true;}

    // Database stuff.
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    if (!isset($usid)) {
        $user_survey = xarModAPIfunc('surveys', 'user', 'getusersurvey', $args);
        if (empty($user_survey)) {
            // TODO: error message
            return;
        } else {
            $usid = $user_survey['usid'];
        }
    }

    // Bind variables and dynamic where-clause.
    $bind = array((int)$usid);
    $where = array();
    $responses = array();

    if (isset($name)) {
        $bind[] = (string)$name;
        $where[] = 'question.xar_name = ?';
    }

    if (isset($qid)) {
        $bind[] = (int)$qid;
        $where[] = 'question.xar_qid = ?';
    }

    if (isset($qids) && is_array($qids)) {
        $bind = array_merge($bind, $qids);
        $where[] = 'question.xar_qid IN (?' . str_repeat(',?', count($qids)-1) . ')';
    }

    if (isset($rid)) {
        $bind[] = (int)$rid;
        $where[] = 'response.xar_rid = ?';
    }

    // A response of status 'NA' is not applicable, so filter those out.
    // An NA response would normally be for a question group that is
    // not applicable for a user survey. (Hmmm, don't seem to be doing
    // that now.)
    // Join to the question type, and only bring back rows that require
    // a response (i.e. the type has a response ID)
    $query = 'SELECT'
        . ' question.xar_name AS name,'
        . ' response.xar_value1 AS value1, response.xar_value2 AS value2,'
        . ' response.xar_value3 AS value3, response.xar_status AS status,'
        . ' response.xar_user_survey_id AS usid,'
        . ' rtype.xar_response_type_id AS rtid, response.xar_rid AS rid,'
        . ' question.xar_qid AS qid, rtype.xar_tid AS qtid'
        . ' FROM ' . $xartable['surveys_questions'] . ' AS question'
        . ' INNER JOIN ' . $xartable['surveys_user_responses'] . ' AS response'
        . ' ON question.xar_qid = response.xar_question_id'
        . ' INNER JOIN ' . $xartable['surveys_types'] . ' AS rtype'
        . ' ON rtype.xar_tid = question.xar_type_id AND rtype.xar_response_type_id > 0'
        . ' WHERE response.xar_user_survey_id = ?'
        //. ' AND response.xar_status <> \'NA\''
        . (!empty($where) ? ' AND ' . implode(' AND ', $where) : '');

    $result = $dbconn->execute($query, $bind);
    if (!$result) {return;}

    // Loop for rows.
    $qids = array();
    while (!$result->EOF) {
        $response = $result->GetRowAssoc(0);

        // Cast numbers correctly.
        $response['qid'] = (int)$response['qid'];
        $response['rid'] = (int)$response['rid'];
        $response['rtid'] = (int)$response['rtid'];
        $response['usid'] = (int)$response['usid'];
        $response['qtid'] = (int)$response['qtid'];

        $qids[$response['rtid']][$response['rid']] = $response['qid'];

        $responses[$response['qid']] =& $response;
        unset($response);

        // Get the next row.
        $result->MoveNext();
    }

    // Do DD retrieval of responses if requested.
    // The DD item data is fetched for all items (in each group) at once, for efficiency.
    if ($dd_flag && !empty($qids)) {
        // Loop for the unique response itemtypes.
        foreach ($qids as $itemtype => $items) { // Each 'items' is an array of rid=>qid
            if (xarModIsHooked('dynamicdata', 'surveys', $itemtype)) {
                $dd_data = xarModAPIfunc(
                    'dynamicdata', 'user', 'getitems',
                    array('module'=>'surveys', 'itemtype' => $itemtype, 'itemids' => array_keys($items))
                );
                if (empty($dd_data)) {continue;}

                foreach($items as $rid => $qid) {
                    if (!empty($dd_data[$rid])) {
                        $responses[$qid]['dd'] = $dd_data[$rid];
                    }
                }
            }
        }
    }

    return $responses;
}

?>