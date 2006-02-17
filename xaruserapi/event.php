<?php
/**
 * Surveys user events
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
 * Record a survey event.
 *
 * This is the central wrapper for recording all events.
 * For now, special handling of specific events are hard-coded
 * in this function, but eventually they could be passed on to
 * helper functions.
 * Options defining which events are processed and which are
 * ignored can also be coded here.
 *
 * Sends emails for events
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $name  [REQUIRED]
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
 * @TODO MichelV <1> Clean up code
 *               <2> Replace hard coded mailing system with template system
 *               <3> Store the events in a database for better review possibilities
 */

function surveys_userapi_event($args)
{
    // Cache session details, so we don't need to fetch them too
    // many times in the case of there being many events in a
    // single page.
    static $_session = NULL;

    // Expand arguments.
    //var_dump($args);
    extract($args);

    // Minimum details: $name (event name).
    if (!isset($name)) {return;}

    // Normalize the event name case.
    $name = trim(strtoupper($name));

    // Get user and session details.
    if (!isset($_session)) {
        $_session['_userid'] = xarUserGetVar('uid');
        $_session['_userdisplayname'] = xarUserGetVar('name');
        $_session['_username'] = xarUserGetVar('uname');
        $_session['_useremail'] = xarUserGetVar('email');
        $_session['_sessionid'] = xarSessionGetId();
    }
    extract($_session);

    // TODO: store the event in the database.
    // ...

    $mailbody = array();
    $adminname = xarModGetVar('mail', 'adminname');
    $adminemail = xarModGetVar('mail', 'adminmail');
    $mailbody[] = 'Logged-in user: ' . $_userdisplayname;
    $mailbody[] = 'Logged-in username: ' . $_username;
    $mailbody[] = ' ';

    $SendEventMails = xarModGetVar('surveys', 'SendEventMails');
    if ($SendEventMails==1) {
        // E-mail someone when a group is completed.
        // TODO: may want to suppress this for admins making the changes.
        // Suppress e-mail for EP1 to EP8 sections.
        if ($name == 'GROUP_COMPLETE' && !preg_match('/EP[12345678]/i', $group_name)) {
            $mailbody = surveys_userapi_event_survey($mailbody, $usersurvey);
            $mailbody[] = 'Update page: <' . xarModURL('surveys','user','showgroup',array('usid'=>$usersurvey['usid'], 'gid'=>$gid), false) . '>';
            // TODO: use the mail module with a template for doing the e-mailing.
            xarModAPIFunc('mail', 'admin', 'sendmail',
                array(
                    'info' => $adminemail,
                    'name' => $adminname,
                    'subject' => 'User ' . $_userdisplayname . ' completed section ' . $group_name . ' ('.$group_desc.') of ' . $usersurvey['desc'],
                    'message' => implode("\n", $mailbody)
                )
            );
        }
    }
    // EVENT:
    // Start a new user survey.
    if ($name == 'START_USER_SURVEY') {
    }

    // EVENT:
    // User submits a complete survey.
    if ($name == 'USER_SUBMIT_SURVEY') {
        $mailbody = surveys_userapi_event_survey($mailbody, $usersurvey);

        $mailbody[] = ' ';
        $mailbody[] = xarML('Data transfer result:');
        $mailbody[] = ' ';
        $mailbody[] = strip_tags(str_replace('<br/>', "\n", $transfer_log));
        if ($SendEventMails==1) {
            xarModAPIFunc('mail', 'admin', 'sendmail',
                array(
                    'info' => $adminemail,
                    'name' => $adminname,
                    'subject' => 'User ' . $_userdisplayname . ' submitted complete survey "' . $usersurvey['desc'] . '"',
                    'message' => implode("\n", $mailbody)
                )
            );
        }
    }

    return;
}

function surveys_userapi_event_survey($mailbody, $usersurvey) {
    $mailbody[] = 'Survey ID: ' . $usersurvey['sid'];
    $mailbody[] = 'Survey name: ' . $usersurvey['name'];
    $mailbody[] = 'Survey desc: ' . $usersurvey['desc'];
    $mailbody[] = 'Survey status: ' . $usersurvey['status'];
    $mailbody[] = 'Survey status desc: ' . $usersurvey['status_desc'];
    $mailbody[] = ' ';

    $mailbody[] = 'Survey owner uid: ' . $usersurvey['uid'];
    $mailbody[] = 'Survey owner name: ' . xarUserGetVar('name', $usersurvey['uid']);
    $mailbody[] = 'Survey owner username: ' . xarUserGetVar('uname', $usersurvey['uid']);
    $mailbody[] = 'Survey owner e-mail: <' . xarUserGetVar('email', $usersurvey['uid']) . '>';
    $mailbody[] = ' ';

    $mailbody[] = 'Review page: <' . xarModURL('surveys','user','review',array('usid'=>$usersurvey['usid']), false) . '>';

    return $mailbody;
}

?>