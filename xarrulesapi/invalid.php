<?php
/**
 * Surveys group rule VALID
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
 * Group rule 'valid'.
 *
 * Validates a question that has been completed and has failed validation.
 * Rule format:
 *  'response:{question-name}'
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * Rule parameters ('params'):
 * 1: question name
 * Standard parameters:
 *   sid: survey ID
 *   uid: user ID
 *   usid: user survey ID
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
 */

function surveys_rulesapi_invalid($args) {
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
        return -1;
    }

    if ($response != array() && $response['status'] == 'INVALID') {
        return true;
    } else {
        return false;
    }
}

?>