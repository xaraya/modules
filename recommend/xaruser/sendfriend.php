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

    $articleinfo=xarModAPIFunc('articles','user','get',array('aid'=>$aid));
    $title=$articleinfo['title'];
    $ptid=$articleinfo['pubtypeid'];
    $articledisplay=xarModURL('articles','user','display',array('aid'=>$aid,'ptid'=>$ptid));
    $textdisplaylink=$articledisplay;
    $htmldisplaylink='<a href="'.$articledisplay.'">'.$articledisplay.'</a>';


    $subject = xarML('Interesting Article at %%sitename%%');
    $message = xarML('Hello %%toname%%, your friend %%name%% considered an article at our site interesting and wanted to send it to you.');
    $message .="\n\n";
    $message .=xarML('
    Site Name: %%sitename%% :: %%siteslogan%%
    Site URL: %%siteurl%%');
    $message .="\n\n";
    $message .=xarML('Article Title: ').$title."\n";
    $message .=xarML('Link: ').$textdisplaylink;

    if (xarModGetVar('recommend', 'usernote')){
        $message .= "\n\n";
        $message .= xarVarPrepForDisplay($usernote);
    }
    $htmlmessage = xarML('Hello %%toname%%, your friend %%name%% considered an article at our site interesting and wanted to send it to you.');
    $htmlmessage .='<br /><br />';
    $htmlmessage .=xarML('Site Name: %%sitename%% :: %%siteslogan%%');
    $htmlmessage .='<br />'.xarML('Site URL: %%siteurl%%');
    $htmlmessage .='<br /><br />';
    $htmlmessage .=xarML('Article Title: ').$title.'<br />';
    $htmlmessage .=xarML('Link: ').$htmldisplaylink;

    if (xarModGetVar('recommend', 'usernote')){
        $htmlmessage .= "<br /><br />";
        $htmlmessage .= xarVarPrepHTMLDisplay($usernote);
    }

    $message = preg_replace('/%%toname%%/',
                            $fname,
                            $message);

    $htmlmessage = preg_replace('/%%toname%%/',
                            $fname,
                            $htmlmessage);

    if (!xarModAPIFunc('mail',
                       'admin',
                       'sendmail',
                       array('info'         => $info,
                             'name'         => $fname,
                             'subject'      => $subject,
                             'htmlmessage'  => $htmlmessage,
                             'message'      => $message,
                             'from'         => $useremail,
                             'fromname'     => $username))) return;


    // lets update status and display updated configuration
    xarResponseRedirect(xarModURL('recommend', 'user', 'sendtofriend', array('message' => '1', 'aid'=>$aid)));

    // Return
    return true;
}
?>