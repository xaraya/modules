<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Recommend Module - Send to Friend
*/
/* Sendfriend prepares the text or html email to send
 *
 * Used by user_sendtofriend to forward email to friend with recommended article
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @parameters Takes parameters passed by user_sendtofriend to generate info used by email mod
 */
function recommend_user_sendfriend()
{
    /* Get parameters */
    if (!xarVarFetch('username', 'str:1:', $username)) return;
    if (!xarVarFetch('useremail', 'str:1:', $useremail)) return;
    if (!xarVarFetch('fname', 'str:1:', $fname)) return;
    if (!xarVarFetch('info', 'str:1:', $info)) return;
    if (!xarVarFetch('aid', 'isset', $aid)) return;
    if (!xarVarFetch('usernote', 'str:1:', $usernote, '', XARVAR_NOT_REQUIRED)) return;

    /*  Confirm authorisation code. */
    if (!xarSecConfirmAuthKey()) return;

    /* Security Check */
    if(!xarSecurityCheck('OverviewRecommend')) return;

    /* Statistics */
    /* $date = date('Y-m-d G:i:s'); */
    $date = time();
    $numbersentprev = xarModGetVar('recommend', 'numbersent');
    $numbersent = $numbersentprev + 1;
    xarModSetVar('recommend', 'numbersent', $numbersent);
    xarModSetVar('recommend', 'lastsentemail', $info);
    xarModSetVar('recommend', 'lastsentname', $fname);
    xarModSetVar('recommend', 'date', $date);
    xarModSetVar('recommend', 'username', $username);

    $sitename = xarModGetVar('themes','SiteName');
    $siteurl = xarServerGetBaseURL();

    $articleinfo=xarModAPIFunc('articles','user','get',array('aid'=>$aid));
    $title=$articleinfo['title'];
    $ptid=$articleinfo['pubtypeid'];
    $articledisplay=xarModURL('articles','user','display',array('aid'=>$aid,'ptid'=>$ptid));
    $textdisplaylink=$articledisplay;
    $htmldisplaylink='<a href="'.$articledisplay.'">'.$articledisplay.'</a>';

    $subject = xarML('Interesting Article at %%sitename%%');

    /* Prepare to process entities in email message */
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);

    if (xarModGetVar('recommend', 'usernote')){
         $usernote=strtr($usernote, $trans);
    }else{
         $usernote='';
    }
    $title=strtr($title, $trans);


    if (xarModGetVar('recommend', 'usernote')){
        $htmlusernote = strtr(xarVarPrepHTMLDisplay($usernote), $trans);
    } else {
        $htmlusernote ='';
    }
    $htmltitle=strtr(xarVarPrepHTMLDisplay($title), $trans);


   /* startnew message */
    $textmessage= xarTplModule('recommend',
                                   'user',
                                   'usersendfriend',
                                    array('username'   => $username,
                                          'friendname' => $fname,
                                          'useremail'  => $useremail,
                                          'articletitle' => $title,
                                          'articlelink' => $textdisplaylink,
                                          'usermessage'=> $usernote,
                                          'sitename'   => $sitename,
                                          'aid'        => $aid,
                                          'siteurl'    => $siteurl),
                                    'text');

     $htmlmessage= xarTplModule('recommend',
                                   'user',
                                   'usersendfriend',
                                    array('username'   => $username,
                                          'friendname' => $fname,
                                          'useremail'  => $useremail,
                                          'articletitle' => $htmltitle,
                                          'articlelink' => $htmldisplaylink,
                                          'usermessage'=> $htmlusernote,
                                          'sitename'   => $sitename,
                                          'aid'        => $aid,                                          
                                          'siteurl'    => $siteurl),
                                    'html');
    /* let's send the email now */
    if (!xarModAPIFunc('mail',
                       'admin',
                       'sendmail',
                       array('info'         => $info,
                             'name'         => $fname,
                             'subject'      => $subject,
                             'htmlmessage'  => $htmlmessage,
                             'message'      => $textmessage,
                             'from'         => $useremail,
                             'fromname'     => $username))) return;




    /* endnew */
    /* lets update status and display updated configuration */
    xarResponseRedirect(xarModURL('recommend', 'user', 'sendtofriend', array('message' => '1', 'aid'=>$aid)));

    /* Return true if successful */
    return true;
}
?>