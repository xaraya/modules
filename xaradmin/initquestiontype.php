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
 *  Initialise a question type.
 *
 * Long Description [OPTIONAL one or more lines]
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $name = question type name
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
function surveys_admin_initquestiontype()
{
    if (!xarSecurityCheck('AdminSurvey', 1, 'Survey', 'All')) {
        // No privilege for editing survey structures.
        return false;
    }

    if (!xarVarFetch('name', 'pre:lower:ftoken', $name, NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (empty($name)) {
        echo "The 'name' of the question type must be supplied.";
        return;
    }

    // These two lines of code will initialise a question type.
    // It may be worth wrapping them into a single API call.
    $question_class = xarModAPIfunc(
        'surveys', 'user', 'newquestionobject',
        array('object_name' => $name)
    );
    if (empty($question_class)) {
        echo "The question class could not be instantiated.";
        return;
    }

    $result = $question_class->init_question_type();

    if (empty($result)) {
        echo "Failed to initialise the question type.";
        return;
    }

    return '';
}

?>