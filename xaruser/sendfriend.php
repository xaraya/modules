<?php
/**
 * File: $Id: s.xarinit.php 1.11 03/01/18 11:39:31-05:00 John.Cox@mcnabb. $
 * 
 * Xaraya Recommend
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Recommend Module
 * @author John Cox
*/
/**
 * @ Function: used by user_sendtofriend to forward email to friend with recommended article
 * @ Author jojodee
 * @ parameters Takes parameters passed by user_sendtofriend to generate info used by email mod
 */
function recommend_user_sendfriend()
{
    // Get parameters
    if (!xarVarFetch('username', 'str:1:', $username)) return;
    if (!xarVarFetch('useremail', 'str:1:', $useremail)) return;
    if (!xarVarFetch('fname', 'str:1:', $fname)) return;
    if (!xarVarFetch('info', 'str:1:', $info)) return;
    if (!xarVarFetch('aid', 'isset', $aid)) return;
    if (!xarVarFetch('usernote', 'str:1:', $usernote, '', XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // Security Check
    if(!xarSecurityCheck('OverviewRecommend')) return;

    // Statistics
    $date = date('Y-m-d G:i:s');
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

    //Prepare to process entities in email message
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
    }
    $htmltitle=strtr(xarVarPrepHTMLDisplay($title), $trans);


// startnew
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
                                          'siteurl'    => $siteurl),
                                    'html');
    //let's send the email now
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




//endnew
    // lets update status and display updated configuration
    xarResponseRedirect(xarModURL('recommend', 'user', 'sendtofriend', array('message' => '1', 'aid'=>$aid)));

    // Return
    return true;
}
?>
