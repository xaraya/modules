<?php

function newsgroups_user_post()
{
    // Security Check
    if(!xarSecurityCheck('ReadNewsGroups')) return;

    if (!xarVarFetch('phase','str:1:100',$phase,'new',XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('group','str:1:',$group)) return;
    $group = xarVarPrepForDisplay($group);

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Post to newsgroup')));

    switch(strtolower($phase)) {

        case 'new':
        default:

            $data['name']       = xarUserGetVar('name');
            $data['email']      = xarUserGetVar('email');
            $data['subject']    = '';
            $data['message']    = '';
            $data['reference']  = '';
            $data['authid']     = xarSecGenAuthKey();
            $data['group']      = $group;

            break;

        case 'reply':

            xarVarFetch('article', 'str:1', $article);
            $article = xarVarPrepForDisplay($article);

            include_once 'modules/newsgroups/xarclass/NNTP.php';

            $server     = xarModGetVar('newsgroups', 'server');
            $port       = xarModGetVar('newsgroups', 'port');

            $newsgroups = new Net_NNTP();
            $newsgroups -> connect($server, $port);
            $counts = $newsgroups->selectGroup($group);
            $data               = $newsgroups->splitHeaders($article);
            $data['raw']        = explode("\n", $newsgroups->getBody($article));
            $newsgroups->quit();

            if (preg_match ("/Re: /i", $data['Subject'])) {
                $data['subject']    = xarVarPrepForDisplay($data['Subject']);
            } else {
                $data['subject']    = xarVarPrepForDisplay("Re: " . $data['Subject']);
            }
          
            $data['reference']  = $data['Message-ID'];
            $data['name']       = xarUserGetVar('name');
            $data['email']      = xarUserGetVar('email');
            $data['authid']     = xarSecGenAuthKey();
            $data['group']      = $group;
            $data['message']    = $data['From'];
            $data['message']    .= xarML(' wrote in message');
            $data['message']    .= $data['Message-ID'] ."\n\n";

            $data['format'] = '';
             foreach($data['raw'] as $datatmp) {
                 if (trim($datatmp) != '') {
                 $data['format'] .= '> '.$datatmp."\n";
                 }
             }

            //var_dump($data['format']);
            $data['message']    .= xarVarPrepForDisplay($data['format']);

            break;

        case 'update':

            if (!xarVarFetch('subject','str:1:100',$subject)) return;
            if (!xarVarFetch('email','str:1:200',$email)) return;
            if (!xarVarFetch('name','str:1:200',$name)) return;
            if (!xarVarFetch('body','str::',$body)) return;
            if (!xarVarFetch('reference','str::',$reference, '',XARVAR_NOT_REQUIRED)) return;

            $subject    = xarVarPrepForDisplay($subject);
            $email      = xarVarPrepForDisplay($email);
            $name       = xarVarPrepForDisplay($name);
            $body       = xarVarPrepForDisplay($body);
            $reference  = xarVarPrepForDisplay($reference);

            xarSecConfirmAuthKey();

            include_once 'modules/newsgroups/xarclass/NNTP.php';

            $body       = wordwrap($body, 72, "\n", 1);

            $server     = xarModGetVar('newsgroups', 'server');
            $port       = xarModGetVar('newsgroups', 'port');

            $addheader = "Content-Transfer-Encoding: quoted-printable\r\n".
                         "Content-Type: text/plain; charset=ISO-8859-1;\r\n".
                         "Mime-Version: 1.0\r\n".
                         'X-HTTP-Posting-Host: '.gethostbyaddr(getenv("REMOTE_ADDR"))."\r\n";

            if (!empty($reference)){
                $addheader .= "References: " . $reference . "\r\n";
            }

            $newsgroups = new Net_NNTP();
            $newsgroups -> connect($server, $port);
            $response = $newsgroups->post($subject, $group, $email .'('. $name .')', $body, $addheader);
            $newsgroups -> quit();


            // Redirect
            xarResponseRedirect(xarModURL('newsgroups', 'user', 'group',array('group' => $group)));

            return true;

    }
    return $data;
}
?>