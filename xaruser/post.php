<?php

function newsgroups_user_post()
{
    // Security Check
    if(!xarSecurityCheck('SendNewsGroups')) return;

    if (!xarVarFetch('phase','str:1:100',$phase,'new',XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('group','str:1:',$group)) return;
    $group = xarVarPrepForDisplay($group);

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Post to newsgroup')));

    switch(strtolower($phase)) {

        case 'new':
        default:

            $data['name']       = xarUserGetVar('name');
            if (xarUserIsLoggedIn()) {
                $data['email']  = xarUserGetVar('email');
            } else {
                $data['email']  = '';
            }
            $data['subject']    = '';
            $data['message']    = '';
            $data['reference']  = '';
            $data['authid']     = xarSecGenAuthKey();
            $data['group']      = $group;

            break;

        case 'reply':

            xarVarFetch('article', 'str:1', $article);
            $article = xarVarPrepForDisplay($article);

            $message = xarModAPIFunc('newsgroups','user','getarticle',
                                     array('group'   => $group,
                                           'article' => $article));
            if (!isset($message)) return;

            // re-shuffle variables for the template
            $data = $message['headers'];
            $data['raw'] = explode("\n", $message['body']);

            if (preg_match ("/Re: /i", $data['Subject'])) {
                $data['subject']    = xarVarPrepForDisplay($data['Subject']);
            } else {
                $data['subject']    = xarVarPrepForDisplay("Re: " . $data['Subject']);
            }
          
            $data['reference']  = $data['Message-ID'];
            $data['name']       = xarUserGetVar('name');
            if (xarUserIsLoggedIn()) {
                $data['email']  = xarUserGetVar('email');
            } else {
                $data['email']  = '';
            }
            $data['authid']     = xarSecGenAuthKey();
            $data['group']      = $group;
            $data['message']    = xarML('#(1) wrote in message #(2)', $data['From'], $data['Message-ID']);
            $data['message']    .= "\n\n";

            $data['format'] = '';
             foreach($data['raw'] as $datatmp) {
                 if (trim($datatmp) != '') {
                 $data['format'] .= '> '.$datatmp."\n";
                 }
             }

            //var_dump($data['format']);
            $data['message']    .= $data['format'];

            break;

        case 'update':

            if (!xarVarFetch('subject','str:1:100',$subject)) return;
            if (!xarVarFetch('email','str:1:200',$email)) return;
            if (!xarVarFetch('name','str:1:200',$name)) return;
            if (!xarVarFetch('body','str::',$body)) return;
            if (!xarVarFetch('reference','str::',$reference, '',XARVAR_NOT_REQUIRED)) return;

            if (!xarSecConfirmAuthKey()) return;

            if (!xarModAPIFunc('newsgroups','admin','postarticle',
                               array('group'     => $group,
                                     'subject'   => $subject,
                                     'body'      => $body,
                                     'name'      => $name,
                                     'email'     => $email,
                                     'reference' => $reference))) return;

            // Redirect
            xarResponseRedirect(xarModURL('newsgroups', 'user', 'group',array('group' => $group)));

            return true;
    }
    return $data;
}
?>
