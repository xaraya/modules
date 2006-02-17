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
 * Get the current group question objects, potentially
 * after a form submit.
 * This function will handle reading of the submitted
 * questions, and will validate and store the results.
 * Param: gid and uid/sid/usid
 * Returns: an array of question objects.
 */

function surveys_userapi_getsubmitgroup($args) {
    // Expand the arguments.
    extract($args);

    // Initialise the result array.
    $return = array();

    // Get the group question data.
    // This array includes any responses the user has made so far.
    $questions = xarModAPIfunc('surveys', 'user', 'getusergroupquestions', $args);

    // We should have an array of questions now.
    if (empty($questions)) {
        // TODO: error message.
        return;
    }

    // Get the user group, so we can see if anything has already
    // been submitted to the database.
    $usergroup = xarModAPIfunc(
        'surveys', 'user', 'getusergroup', $args
    );

    if (!empty($usergroup)) {
        $groupstatus = $usergroup['status'];
    } else {
        $groupstatus = 'NORESPONSE';
    }

    // Go through each question in turn to process it.
    $update_flag = false;
    $response_req_flag = false;
    foreach ($questions as $question) {
        // Get the question object.
        $question_object = xarModAPIfunc(
            'surveys', 'user', 'newquestionobject',
            array('question' => $question)
        );

        // Determine whether this question has just been submitted.
        // There will be a hidden field submitted as a flag, if so.
        // Check each question individually, as a question may be
        // submitted outside of the group context (though the group
        // ID would still be needed, so we don't have to check for
        // the presence of every question in the survey).
        // Also exclude read-only questions from this check, as they
        // will have nothing to submit.
        if (isset($question_object->submit_hidden) && !$question_object->readonly) {
            // Must unset before calling xarVarFetch() otherwise it will not get over-writtem.
            unset($submitted);
            xarVarFetch($question_object->submit_hidden, 'int', $submitted, 0, XARVAR_NOT_REQUIRED);
            if ($submitted > 0) {
                // A response has been submitted from a form.

                // Grab the submitted details.
                $submit = $question_object->submit();

                // Validate the submitted details.
                $validate = $question_object->validate();

                // Store the response now (whether valid or not).
                $store = $question_object->store();

                // Flag the fact that changes have been made.
                $update_flag = true;
            } elseif ($groupstatus == 'INVALID') {
                // Not submitted, but is marked as invalid in the database.
                // Run the validation method only to pre-fetch the error message.
                // This means that jumping direct to an invalid group will show
                // the error message, even before the group has been resubmitted.
                $validate = $question_object->validate();
            }
        }

        // If a response is required, then flag it.
        if ($question_object->response_capable && !$question_object->readonly) {
            $response_req_flag = true;
        }

        // Add it to the result array.
        $return[] =& $question_object;
        unset($question_object);
    }

    // If updates have been made, then create or update the user response group record.
    // If there are no response-required questions, then we still need to update the group.
    if (($update_flag || !$response_req_flag) && !empty($gid)) {
        xarModAPIfunc(
            'surveys', 'admin', 'updateusergroupstatus',
            $args // gid, uid and sid
        );
    }

    return $return;
}

?>