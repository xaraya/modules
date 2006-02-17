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
 * Return a new question object, based on the question array passed in.
 * $args is an element from userapi/getusergroupquesions()
 */

function surveys_userapi_newquestionobject($args) {
    // Check arguments.
    if (!is_array($args)) {
        return;
    }

    // Get the object name.
    // TODO: nicer validation - if the element is not set?

    if (isset($args['question'])) {
        // A question element has been passed in.
        $name = $args['question']['object_name'];
    } else {
        // No question has been passsed in - fetch one now.
        // We need the question_type_object name anyway.
        if (!isset($args['object_name'])) {
            // TODO: error message.
            return;
        }
        //
        $name = $args['object_name'];

        // TODO: fetch the question based on the object name - or do
        // this in the default question object?
    }

    return xarModAPIfunc('surveys', 'questions', $name, $args);
}

?>