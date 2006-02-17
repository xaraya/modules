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
 * Get a formatted block of text detailing some responses
 * to a user survey.
 */

function surveys_userapi_usersurveyidentity($args) {
    // Expand arguments.
    extract($args);

    // The format string contains the following substitution
    // string:
    // {question_name:N;default_if_null}
    // N is the question response number (1 to 3) and defaults to 1 if not specified.

    if (!isset($template)) {
        return;
    }

    $replace = preg_replace(
        array('/\{([^\}:;]+):([123]);([^\}]+)\}/e', '/\{([^\}:;]+):([123])\}/e', '/\{([^\}]+)\}/e'),
        'surveys_userapi_usersurveyidentity_lookup('.$usid.',"$1","$2","$3")',
        $template
    );

    // Remove blank lines. Perhaps make this optional in some way.
    $replace = preg_replace('/[\n\r]+/', "\n", $replace);

    // Return the substituted template, with newlines replaced with appropriate
    // markup where requested, or as an array of lines.
    if (isset($newline)) {
        return trim(str_replace("\n", $newline, $replace));
    } elseif (!empty($return_array)) {
        // Return an array of lines - markup can be applied in a template loop if required.
        return explode("\n", $replace);
    } else {
        return $replace;
    }
}

function surveys_userapi_usersurveyidentity_lookup($usid, $name, $valueno = 1, $default = '') {
    if (empty($valueno)) {$valueno = 1;}
    if (!isset($default)) {$default = '';}

    $response = xarModAPIfunc(
        'surveys', 'user', 'getquestionresponse',
        array('usid' => $usid, 'name' => $name)
    );

    if (!isset($response['value'.$valueno])) {
        return $default;
    } else {
        return $response['value'.$valueno];
    }
}

?>