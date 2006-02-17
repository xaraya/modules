<?php
/**
 * Surveys Redirect to graphing module
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
 * Redirect to the graphing module.
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

function surveys_user_graph_redirect() {
    // Get the user survey ID
    if (!xarVarFetch('usid', 'int:1', $usid)) {return;}

    // Get the survey.
    $survey = xarModAPIfunc(
        'surveys', 'user', 'getusersurvey',
        array('usid' => $usid, 'current_user' => false)
    );

    // Security check - need at least read privilege.
    if (!xarSecurityCheck('ReadAssessment', 0, 'Assessment', $survey['sid'].':'.$survey['system_status'].':'.$survey['status'].':'.$survey['uid'])) {
        // No read access to the survey, so zap it.
        $survey = NULL;
    }

    if (empty($survey)) {
        $msg = xarML('The user survey #(1) does not exist or you do not have permission to access it', $usid);
        xarExceptionSet(
            XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg)
        );
        return;
    }

    // Get the remote username and password.
    $username = xarUserGetVar('uname', $survey['uid']) . '-' . $survey['usid'];
    $password = md5(xarUserGetVar('uname', $survey['uid']) . '-' . $survey['uid']);

    // Pass the details to the redirect form.
    return array(
        'username' => $username,
        'password' => $password
    );
}
?>