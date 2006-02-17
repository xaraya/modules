<?php
/**
 * Surveys Switch to an open user survey
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
 * Switch to an open user survey then take the user there.
 *
 * Long Description [OPTIONAL one or more lines]
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param in     $gid  group ID
 * @param int    $usid  User Survey ID REQUIRED
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

function surveys_user_gotosurvey($args) {
    extract($args);

    // Need the user survey ID.
    if (!xarVarFetch('usid', 'int:1', $usid)) {return;}

    // Optional group ID to jump to, once the switch is done.
    if (!xarVarFetch('gid', 'int:1', $gid, 0, XARVAR_NOT_REQUIRED)) {return;}

    // Fetch the user survey to make sure it is valid for the current user.
    // TODO: admins can skip this check (the current_user part, at least),
    // and switch to any survey.
    // TODO: set to true if NOT an administrator.
    $survey = xarModAPIfunc(
        'surveys', 'user', 'getusersurvey',
        array('usid' => $usid, 'current_user' => false /*true*/)
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

    // Still here. Switch to the survey.
    $result = xarModAPIfunc('surveys', 'user', 'switchtosurvey', array('usid' => $usid));

    if ($result) {
        // Switch was successful - go there.
        xarResponseRedirect(xarModURL('surveys', 'user', 'showgroup', array('gid' => $gid)));
    } else {
        // Failed to switch = go back to the overview page
        xarResponseRedirect(xarModURL('surveys', 'user', 'overview'));
    }

    return true;
}

?>