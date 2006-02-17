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
 * qid: question ID
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

function surveys_userapi_getquestionresponse($args) {
    // Cache the responses if asked for by name.
    static $named_responses = array();

    // Is question cached? If so, return it.
    if (isset($args['name']) && isset($args['usid']) && isset($named_responses[$args['usid'] .':'. $args['name']])) {
        return $named_responses[$args['usid'] .':'. $args['name']];
    }

    // Get the responses array.
    $responses = xarModAPIfunc('surveys', 'user', 'getquestionresponses', $args);

    // We are expecting an array.
    if (empty($responses) || !array($responses)) {
        // Return if we don't have the requisite number of elements.
        return array();
    }

    // We are expecting a single row only.
    if (count($responses) > 1) {
        // TODO: error if more than one match.
        return;
    }

    // Return the first element of the responses array.
    $result =& reset($responses);

    if (isset($args['name']) && isset($args['usid'])) {
        $named_responses[$args['usid'] .':'. $args['name']] =& $result;
    }

    return $result;
}

?>
