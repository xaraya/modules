<?php
/**
 * Send notification to the new user's email of a registration
 *
 * @param user email
 * @param ser name
 * @param terms - agreement to user terms (optional) - may not be activated
 * @param id - the users id
 * @return bool true on success
 */
function registration_userapi_notifyuser ($args)
{
    $messagetitle = xarML('A new user #(1) #(2) has registered on #(3)', $args['uname'], $args['name'], xarModVars::get('themes','SiteName'));

    // Make sure we remove comments from the templates
    $themecomments = xarModVars::get('themes','ShowTemplates');
    $mailcomments =  xarModVars::get('mail','ShowTemplates');
    if ($mailcomments == 1) xarModVars::set('themes','ShowTemplates',1);
    else xarModVars::set('themes','ShowTemplates',0);

    $emailvars = array('adminemail'   => $args['email'],
                       'adminname'    => $args['name'],
                       'messagetitle' => $messagetitle,
                       'sitename'     => xarModVars::get('themes','SiteName'),
                       'values'       => $args
                      );
    //Prepare the message
    switch (xarModItemVars::get('mail','messagetype',xarMod::getRegID('registration')))
    {
        case 'html':
            $message= xarTplModule('registration', 'user', 'newuserwelcome', $emailvars,'html');
        break;

        case 'text':
        default:
            $message= xarTplModule('registration', 'user', 'newuserwelcome', $emailvars,'text');
        break;
    }
    
    //send the email
    try {
        xarModAPIFunc('mail', 'admin', 'sendmail',
                           array('info'         => $args['email'],
                                 'name'         => $args['name'],
                                 'subject'      => $messagetitle,
                                 'message'      => $message,
                                 'from'         => xarModVars::get('mail','adminmail'),
                                 'fromname'     => xarModVars::get('mail','adminname'),
                                 'usetemplates' => 0)); //use templates is set true by default if passed in var is not set
    } catch (Exception $e) {}

   /* Set the template comments back */
    xarModVars::set('themes','ShowTemplates',$themecomments);

    return true;
}
?>