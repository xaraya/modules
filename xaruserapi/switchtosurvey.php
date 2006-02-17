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
 * Switch to a specified user survey.
 * The user may have just one survey 'current' at any
 * time, and this switches the current survey to one
 * that is specified.
 */

function surveys_userapi_switchtosurvey($args) {
    // Expand arguments.
    extract($args);

    // Get the specified user survey details (to check it is valid).
    $usersurvey = xarModAPIfunc('surveys', 'user', 'getusersurvey', $args);

    if (empty($usersurvey)) {
        // TODO: error message
        return;
    }

    $details = array(
        'sid' => $usersurvey['sid'],
        'usid' => $usersurvey['usid'],
        'uid' => $usersurvey['uid'],
        'gid' => (empty($gid) ? 0 : $gid)
    );

    // Set the group in the user surveys variables.
    xarModAPIfunc('surveys', 'user', 'setcurrentsurveyvars', $details);

    return true;
}

?>