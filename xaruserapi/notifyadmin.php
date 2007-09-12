<?php
/**
 * Notification of new user registrations
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @author Jo Dalle Nogare
 */
/**
 * Send new user notification to admin
 *
 * Send notificatoni to the nominated admin of a new user Registration
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @param admin email - the email of administrator to receive the notification
 * @param admin name - the name of the administrator(optional)
 * @param userrealname - the display name of the user (optional) - may not be set, or empty
 * @param username - the username of the user
 * @param useremail - the email of the user
 * @param terms - agreement to user terms (optional) - may not be activated
 * @param uid - the users uid
 * @return bool true on success
 */
function registration_userapi_notifyadmin ($args)
{
    // Get parameters
    extract ($args);

    if (!xarVarFetch('adminemail',   'str:1:', $adminemail,    xarModGetVar('mail','adminname'))) return; //user the admin email if it's not set by now
    if (!xarVarFetch('adminname',    'str:1:', $adminname,    'Administrator', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('userrealname', 'str:1:', $userrealname, 'New User', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('username',     'str:1:', $username,     '', XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('useremail',    'str:1:', $useremail,    '', XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('terms',        'str:1:', $terms,        '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('uid',          'int:1:', $uid )) return;
    if (!xarVarFetch('userstatus',   'int:0:', $userstatus,    0, XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;

    //set a title and link to the user role
    $requireapproval=xarModGetVar('registration','explicitapproval');
    if ($requireapproval) {
        $messagetitle = xarML('A New user - #(1) - has registered and requires approval',$username);
    } else {
        $messagetitle = xarML('A new user has registered: #(1) "#(2)"', $username, $userrealname);
    }
    $rolelink = xarModURL('roles','admin','modifyrole',array('uid'=>$uid),false);
    
    /* 
       TODO: can we do this more centrally instead of every function with email
       comments in emails is a problem - set it manually for this module
       let's make it contingent on the mail module var - as that is what
       seems intuitively the correct thing
    */
    $themecomments = xarModGetVar('themes','ShowTemplates');
    $mailcomments = xarModGetVar('mail','ShowTemplates');
    if ($mailcomments == 1) {
        xarModSetVar('themes','ShowTemplates',1);
    } else {
        xarModSetVar('themes','ShowTemplates',0);
    }

    if ($userstatus == 4) {
       $pending = xarML('PENDING and requiring approval');
    } elseif ($userstatus == 3) {
       $pending=xarML('ACTIVE');
    } elseif ($userstatus == 2) {
       $pending=xarML('NON VALIDATED'); // not available in this registration mailing scenario
    }
    $siteadminemail = xarModGetVar('mail','adminmail');
    $siteadminname  = xarModGetVar('mail','adminname');
    $sitename = xarModGetVar('themes','SiteName');

    $emailvars = array('adminemail'  => $adminemail,
                       'adminname'    => $adminname,
                       'userrealname' => $userrealname,
                       'username'     => $username,
                       'terms'        => $terms,
                       'uid'          => $uid,
                       'useremail'    => $useremail,
                       'messagetitle' => $messagetitle,
                       'rolelink'     => $rolelink,
                       'sitename'     => $sitename,
                       'pending'      => $pending
                      );

    //Prepare the text message
    $textmessage= xarTplModule('registration', 'user', 'newusernotification',$emailvars,'text');

    $htmlmessage= xarTplModule('registration', 'user', 'newusernotification', $emailvars,'html');

    //send the email now
    if (!xarModAPIFunc('mail', 'admin', 'sendmail',
                           array('info'         => $adminemail,
                                 'name'         => $adminname,
                                 'subject'      => $messagetitle,
                                 'htmlmessage'  => $htmlmessage,
                                 'message'      => $textmessage,
                                 'from'         => $siteadminemail,
                                 'fromname'     => $siteadminname,
                                 'usetemplates' => 0))) {//use templates is set true by default if passed in var is not set

        /* Set the theme comments back */
        xarModSetVar('themes','ShowTemplates',$themecomments);
        return; //what to do here - we don't want the user affected here
    }

   /* Set the theme comments back */
    xarModSetVar('themes','ShowTemplates',$themecomments);

    // Return
    return true;
}
?>