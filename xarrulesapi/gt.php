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
 * Group rule 'gt'.
 * Validates a question value is set *greater than* a specified value.
 * Rule parameters ('params'):
 * 1: question name
 * Standard parameters:
 *   sid: survey ID
 *   uid: user ID
 *   usid: user survey ID
 *
 * Rule format:
 *  'eq:{question-name}:{value}:[{value-number}]'
 * The value number is 1, 2 or 3, defaulting to 1. It determines
 * which of the three value fields will be compared.
 */

function surveys_rulesapi_gt($args) {
    // Expand arguments
    extract($args);

    if (count($params) < 2) {
        // Not enough parameters.
        // TODO: error message.
        return -1;
    }

    // Get the response details (do DD).
    $args['name'] = $params[0];
    $args['dd_flag'] = false;
    $response = xarModAPIfunc('surveys', 'user', 'getquestionresponse', $args);

    // Error in response.
    if ($response === NULL) {return -1;}

    // No response - fail condition.
    if ($response === array()) {return false;}

    // The third optional parameter allows the value of any response value (value1 to value3) to be compared.
    if (!isset($params[2]) || !is_numeric($params[2]) || $params[2] < 1 || $params[2] > 3) {
        $params[2] = 1;
    }

    // If the value equals the second parameter then the condition passes (true).
    if ($response['value' . $params[2]] > $params[1]) {
        return true;
    } else {
        return false;
    }
}

?>