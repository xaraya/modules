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
 * Update any of the following:
 * - a user survey
 * - a question
 * - a user response.
 *
 * This is a multi-purpose table update function, that handles hooks
 * but does not contain much in the way of validation, so handle
 * it with care.
 * Only specified columns will be updated, other columns being left
 * as they are.
 */

function surveys_adminapi_update($args) {
    // Expand arguments.
    extract($args);

    // Database stuff.
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $sets = array();
    $bind = array();
    $where = array();

    // Question.
    if (isset($qid) && is_numeric($qid)) {
        $table = $xartable['surveys_questions'];

        $accepted = array(
            'name' => 'name',
            'desc' => 'desc',
            'default' => 'default',
            'type_id' => 'type_id'
        );
    }

    // User response.
    if (isset($rid) && is_numeric($rid)) {
        $table = $xartable['surveys_user_responses'];

        $accepted = array(
            'status' => 'status',
            'value1' => 'value1',
            'value2' => 'value2',
            'value3' => 'value3'
        );
    }

    // User survey.
    if (isset($usid) && is_numeric($usid)) {
        $table = $xartable['surveys_user_surveys'];

        $accepted = array(
            'status' => 'status',
            'user_id' => 'user_id',
            'survey_id' => 'survey_id',
            'start_date' => 'start_date',
            'submit_date' => 'submit_date',
            'closed_date' => 'closed_date',
            'last_updated' => 'last_updated'
        );
    }

    // String parameters.
    foreach($accepted as $colname => $parameter)
    {
        if (isset($$parameter))
        {
            $sets[] = "xar_$colname = ?";
            $bind[] = $$parameter;
        }
    }

    // Question.
    if (isset($qid) && is_numeric($qid)) {
        if (isset($mandatory)) {
            $sets[] = 'xar_mandatory = ?';
            $bind[] = (strtoupper($mandatory) == 'Y') ? 'Y' : 'N';
        }

        $idname = 'xar_qid';
        $idvalue = $qid;

        // Get existing question.
        $question = xarModAPIFunc(
            'surveys', 'user', 'get',
            array('qid' => $qid)
        );
        if (!$question) {
            // TODO: error message.
            return;
        }
        $itemtype = $question['type_id'];
    }

    // User response.
    if (isset($rid) && is_numeric($rid)) {
        $idname = 'xar_rid';
        $idvalue = $rid;

        // Get existing response.
        $response = xarModAPIFunc(
            'surveys', 'user', 'get',
            array('rid' => $rid)
        );
        if (!$response) {
            // TODO: error message.
            return;
        }
        $itemtype = $response['type_id'];

        // The $status_notna will only update the status if
        // the response is not disabled (i.e. not 'NA').
        // This is useful when importing, so that responses can be
        // imported into disabled questions without enabling them.
        if (isset($status_notna) && !isset($status) && $response['status'] != 'NA') {
            $sets[] = 'xar_status = ?';
            $bind[] = $status_notna;
        }
    }

    // User survey.
    if (isset($usid) && is_numeric($usid)) {
        $idname = 'xar_usid';
        $idvalue = $usid;

        // Get existing user survey.
        $usersurvey = xarModAPIFunc(
            'surveys', 'user', 'getusersurvey',
            array('usid' => $usid)
        );
        if (!$usersurvey) {
            // TODO: error message.
            return;
        }

        // Set dates if the system status is changing.
        if (!empty($status)) {
            // Get the system status for the user status.
            $system_status = xarModAPIfunc(
                'surveys', 'user', 'lookupstatus',
                array('type' => 'SURVEY', 'status' => $status, 'return' => 'system_status')
            );

            if (!empty($system_status)) {
                // Get the time once.
                $date_name = time();

                // Set flags according to the system status.
                // ACTIVE LOCKED CLOSED
                if (($system_status == 'LOCKED' || $system_status == 'CLOSED') && empty($usersurvey['submit_date']) && empty($submit_date)) {
                    // Freshly closed or submitted - set the submit date.
                    $bind[] = $date_name;
                    $sets[] = 'xar_submit_date = ?';
                }

                if ($system_status == 'CLOSED' && empty($usersurvey['closed_date']) && empty($closed_date)) {
                    // Freshly closed - set the closure date.
                    $bind[] = $date_name;
                    $sets[] = 'xar_closed_date = ?';
                }

                if ($system_status == 'LOCKED' && empty($closed_date)) {
                    // If locked, then the closed date should not be set.
                    $bind[] = $date_name;
                    $sets[] = 'xar_closed_date = ?';
                }

                if ($system_status == 'ACTIVE' && empty($submit_date) && empty($closed_date)) {
                    // If active, then the submit and closed date should not be set.
                    $bind[] = 0;
                    $sets[] = 'xar_submit_date = ?';
                    $bind[] = 0;
                    $sets[] = 'xar_closed_date = ?';
                }

                if ($system_status == 'CLOSED' && empty($usersurvey['closed_date']) && empty($closed_date)) {
                    // Freshly closed - set the closure date.
                    $bind[] = $date_name;
                    $sets[] = 'xar_closed_date = ?';
                }
            }
        }
    }

    $where[] = $idname . ' = ?';
    $bind[] = $idvalue;

    // Execute the query.
    $query = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE ' . implode(' AND ', $where);
    $result = $dbconn->execute($query, $bind);
    if (!$result) {return;}

    // Do update hooks.
    if (!empty($itemtype)) {
        xarModCallHooks(
            'item', 'update', $idvalue,
            array(
                'itemtype' => $itemtype,
                'module' => 'surveys'
            )
        );
    }

    return true;
}

?>