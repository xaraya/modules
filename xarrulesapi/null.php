<?php
/**
 * Surveys group rule NULL
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
 * Group rule 'null'.
 *
 * Validates a question that has been completed and has a value set.
 * Note, 'NA' is counted as 'not complete'.
 * 'NA' will include those questions that do not require a response, as
 * well as groups that have been disabled though other rules.
 * Rule format:
 *  'null:{question-name}:[{value-number}]'
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * Rule parameters ('params'):
 * 1: question name
 * Standard parameters:
 *   sid: survey ID
 *   uid: user ID
 *   usid: user survey ID
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

function surveys_rulesapi_null($args) {
    // Expand arguments
    extract($args);

    if (count($params) < 1) {
        // Not enough parameters.
        // TODO: error message.
        return 0;
    }

    // Get the response details.
    $args['name'] = $params[0];
    $args['dd_flag'] = false;
    $response = xarModAPIfunc('surveys', 'user', 'getquestionresponse', $args);

    if (!isset($response)) {
        // Error while fetching the results.
        return false;
    }

    // The third optional parameter allows the value of any response value (value1 to value3) to be compared.
    if (!isset($params[2]) || !is_numeric($params[2]) || $params[2] < 1 || $params[2] > 3) {
        $params[2] = 1;
    }

    if ($response == array() || $response['status'] == 'NA' || !isset($response['value' . $params[2]]) || $response['value' . $params[2]] == '' ) {
        $return = true;
    } else {
        $return = false;
    }

    return $return;
}

?>