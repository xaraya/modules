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

function recommend_user_send($args)
{
    extract($args);
    // Get parameters
    if (!xarVarFetch('username', 'str:1:', $username)) return;
    if (!xarVarFetch('useremail', 'str:1:', $useremail)) return; 
    if (!xarVarFetch('fname', 'str:1:', $fname)) return;
    if (!xarVarFetch('info', 'str:1:', $info)) return; 
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
    $subject = xarModGetVar('recommend', 'title');
    $message = xarModGetVar('recommend', 'template');
    if (xarModGetVar('recommend', 'usernote')){
        $message .= "\n";
        $message .= xarVarPrepForDisplay($usernote);
    }
    $htmlmessage = xarModGetVar('recommend', 'template');
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
    xarResponseRedirect(xarModURL('recommend', 'user', 'main', array('message' => '1')));
    // Return
    return true;
}
?>