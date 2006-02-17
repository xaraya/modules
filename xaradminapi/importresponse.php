<?php
/**
 * Import a response to a question.
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
 * Import a response to a question.
 *
 * The response is imported directly into a user survey,
 * and all relevant flags are updated.
 * Used to import surveys from external data.
 * Just a single question is handled, though importing groups
 * would probably be more efficient.
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @param int $usid - user survey ID
 * @param string $name - question name
 *
 * @return nothing
 *
 * @throws      exceptionclass  [description]                [OPTIONAL A REQURIED]
 *
 * @access      public                                       [OPTIONAL A REQURIED]
 * @since      [Date of first inclusion long date format ]   [REQURIED]
 * @deprecated Deprecated [release version here]             [AS REQUIRED]
 */

function surveys_adminapi_importresponse($args) {
    extract($args);

    // Get the user survey.
    $usersurvey = xarModAPIfunc(
        'surveys', 'user', 'getusersurvey',
        array('usid' => $usid, 'current_user' => false)
    );

    if (empty($usersurvey)) {
        return;
    }

    // Find the question and the group the question is in.
    // The question may be in more than one group, for this
    // survey. The response is applied to both, so that the
    // group statuses are set correctly.

    // Get the question(s) by name.
    $questions = xarModAPIfunc(
        'surveys', 'user', 'getquestions',
        array('name' => $name)
    );
    //var_dump($questions);

    $qid_list = array();
    foreach($questions as $question) {
        $usergroupsquestions = xarModAPIfunc(
            'surveys', 'user', 'getusergroupsquestions',
            array('usid' => $usersurvey['usid'], 'qid' => $question['qid'])
        );

        foreach($usergroupsquestions as $usergroupquestions) {
            // Here we have a questions in a user group.

            foreach($usergroupquestions as $question) {
                // Don't process a question more than once, but do
                // update its groups if it appears in more than one.
                if (!in_array($question['qid'], $qid_list)) {
                    // Get the question object.
                    // This object can be used to manipulate the response.
                    $question_object = xarModAPIfunc(
                        'surveys', 'user', 'newquestionobject',
                        array('question' => $question)
                    );
                    //var_dump($question_object);

                    // Now update the question response.
                    $import = $question_object->import($response);

                    // Validate the submitted details.
                    $validate = $question_object->validate();

                    // Store the response now (whether valid or not).
                    $store = $question_object->store();
                } else {
                    $qid_list[] = $question['qid'];
                }

                // Update the group flags.
                xarModAPIfunc(
                    'surveys', 'admin', 'updateusergroupstatus', $question
                );

                // Apply the response rules to the survey.
                //xarModAPIfunc('surveys', 'admin', 'applyresponserules', $usersurvey);

                // Update the last-update time on the user survey
                //xarModAPIfunc('surveys', 'admin', 'update', array('usid' => $usersurvey['usid'], 'last_updated' => time()));
            }
        }
    }

    // Now filter off just those questions in the given survey.
}

?>