<?php
/**
 * Start a new survey
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @link http://xaraya.com/index.php/release/45.html
 * @author Surveys module development team
 */
/**
 * Start a new survey, and take the user to the start of it.
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     MichelV. <michelv@xaraya.com>
 * @param id    $sid Survey ID?
 *
 * @return array redirect
 *
 * @throws      exceptionclass  [description]                [OPTIONAL A REQURIED]
 *
 * @access      public                                       [OPTIONAL A REQURIED]
 * @static                                                   [OPTIONAL]
 * @link       link to a reference                           [OPTIONAL]
 * @see        anothersample(), someotherlinke [reference to other function, class] [OPTIONAL]
 * @since      [Date of first inclusion long date format ]   [REQURIED]
 *
 */
function surveys_user_startsurvey()
{
    // Need a sid.
    if (!xarVarFetch('sid', 'id', $sid)) {return;}

    // Get the overview of surveys for this user.
    $overview = xarModAPIfunc('surveys', 'user', 'overview');

    // Only allow a survey to be started if it is in the list of
    // allowed 'new' surveys.
    if (!isset($overview['new'][$sid])) {
        $msg = xarML('You are not permitted to start survey type #(1)', $sid);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // Create a new survey.
    $usid = xarModAPIfunc(
        'surveys', 'user', 'startsurvey',
        array('sid' => $sid)
    );

    if (empty($usid)) {
        // Failed to start the survey.
        // TODO: error message here if there are common reasons why this would fail.
        $msg = xarML('Failed to start survey type #(1)', $sid);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Log the event.
    xarModAPIfunc(
        'surveys', 'user', 'event',
        array(
            'name' => 'START_USER_SURVEY',
            'usid' => $usid,
            'sid' => $sid,
            'usersurvey' => $overview['new'][$sid]
        )
    );

    // Now go there.
    xarModFunc('surveys', 'user', 'gotosurvey', array('usid' => $usid));
}
?>