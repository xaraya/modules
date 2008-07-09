<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage registration
 * @author Jo Dalle Nogare
 */
/**
 * Send new user notification to admin
 *
 * Send notification to the nominated admin of a new user registration
 *
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @param admin email - the email of administrator to receive the notification
 * @param admin name - the name of the administrator(optional)
 * @param userrealname - the real name of the user (optional) - may not be set, or empty
 * @param username - the username of the user
 * @param useremail - the email of the user
 * @param terms - agreement to user terms (optional) - may not be activated
 * @param id - the users id
 * @return bool true on success
 */
function registration_userapi_notifyadmin ($args)
{
    extract ($args);

    if (!xarVarFetch('adminemail', 'str:1:', $adminemail, xarModVars::get('mail','adminemail'))) return;
    if (!xarVarFetch('adminname',  'str:1:', $adminname,  'Administrator', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('terms',      'str:1:', $terms,      '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;

    //set a title and link to the user role
    $id = $values['id'];
    $state = $values['state'];
    $requireapproval = xarModVars::get('registration','explicitapproval');
    if ($requireapproval) {
        $messagetitle = xarML('A New user - #(1) - has registered and requires approval',$values['user_name']);
    } else {
        $messagetitle = xarML('A new user has registered: #(1) "#(2)"', $values['user_name'], $values['name']);
    }
    $rolelink = xarModURL('roles','admin','modify',array('id'=>$id),false);

    /*
       TODO: can we do this more centrally instead of every function with email
       comments in emails is a problem - set it manually for this module
       let's make it contingent on the mail module var - as that is what
       seems intuitively the correct thing
    */
    $themecomments = xarModVars::get('themes','ShowTemplates');
    $mailcomments = xarModVars::get('mail','ShowTemplates');
    if ($mailcomments == 1) {
        xarModVars::set('themes','ShowTemplates',1);
    } else {
        xarModVars::set('themes','ShowTemplates',0);
    }

    if ($state == 4) {
       $state = xarML('PENDING and requiring approval');
    } elseif ($state == 3) {
       $state = xarML('ACTIVE');
    } elseif ($state == 2) {
       $state = xarML('NON VALIDATED'); // not available in this registration mailing scenario
    }
    $siteadminemail = xarModVars::get('mail','adminmail');
    $siteadminname  = xarModVars::get('mail','adminname');
    $sitename = xarModVars::get('themes','SiteName');

    $emailvars = array('adminemail'  => $adminemail,
                       'adminname'    => $adminname,
                       'terms'        => $terms,
                       'messagetitle' => $messagetitle,
                       'rolelink'     => $rolelink,
                       'sitename'     => $sitename,
                       'values'      => $values
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
        xarModVars::set('themes','ShowTemplates',$themecomments);
        return; //what to do here - we don't want the user affected here
    }

   /* Set the theme comments back */
    xarModVars::set('themes','ShowTemplates',$themecomments);

    // Return
    return true;
}
?>