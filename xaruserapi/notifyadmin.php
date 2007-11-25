<?php
/**
 * Notification of new registrations
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ebulletin module
 * @author Andrea Moro from same file in registration module by Jo Dalle Nogare
 */
/**
 * Send new user registration notice to admin
 *
 * Send notificatoni to the nominated admin of a new user Registration
 * @author Andrea Moro <andrea@andreamoro.net> 
 * @author modified from same file in registration module by Jo Dalle Nogare
 * @param notifyemail - the email of administrator to receive the notification
 * @param uid - the uid of the user subscribing
 * @param name- the name of the user subscribing
 * @param email- the email of the user subscribing
 * @return bool true on success
 */
function ebulletin_userapi_notifyadmin ($args)
{
    // Get parameters
    extract ($args);

    if (!xarVarFetch('notifymail',   'str:1:', $notifyemail,    xarModGetVar('mail','adminname'))) return; //user the admin email if it's not set by now
    if (!xarVarFetch('uid',   'int:1:', $uid,  0 , XARVAR_NOT_REQUIRED  )) return; 
	if ($uid == 0) {
        if (!xarVarFetch('name',   'str:1:', $name, NULL, XARVAR_NOT_REQUIRED  )) return; 
        if (!xarVarFetch('email',   'str:1:', $email, NULL, XARVAR_NOT_REQUIRED  )) return; 
    } else {
	    $user = xarModApiFunc('roles','user','get',array('uid'=>$uid));
		$email = $user['email'];
		$name = $user['name']; 
	}

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

    $siteadminemail = xarModGetVar('mail','adminmail');
    $siteadminname  = xarModGetVar('mail','adminname');
    $sitename = xarModGetVar('themes','SiteName');
    $messagetitle = xarML('New ebulletin registration: #(1)',$name);

    $emailvars = array('notifyemail'  => $notifyemail,
                       'userrealname' => $userrealname,
                       'messagetitle' => $messagetitle,
                       'sitename'     => $sitename,
					   'uid' => $uid,
					   'email' => $email,
					   'name' => $name
                      );

    //Prepare the text message
    $textmessage= xarTplModule('ebulletin', 'user', 'adminnotification',$emailvars,'text');
    $htmlmessage= xarTplModule('ebulletin', 'user', 'adminnotification', $emailvars,'html');

    //send the email now
    if (!xarModAPIFunc('mail', 'admin', 'sendmail',
                           array('info'         => $notifyemail,
						         'name'         => '',
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
