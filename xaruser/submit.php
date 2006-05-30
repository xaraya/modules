<?php
/**
 * ITSP submit an ITSP
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Submit the ITSP
 *
 * When a user submits the ITSP, it is sent to the education office for approval
 * Copies are sent to the student and to the supervisor.
 * Other updates to the status can be done from this function as well
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param int itspid
 * @param string return_url
 * @param bool confirm
 * @param string useraction
 * @param int newstatus The new status of the ITSP
 * @since 16 May 2006
 * @return bool true on success of submission
 */
function itsp_user_submit($args)
{
    extract($args);

    /* Get parameters */
    if (!xarVarFetch('itspid',      'id',     $itspid,     0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return_url',  'isset',  $return_url, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('confirm',     'isset',  $confirm,    NULL, XARVAR_DONT_SET)) return;
 //   if (!xarVarFetch('useraction',  'str:1:', $useraction, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('newstatus',   'int:1:8', $newstatus, 0, XARVAR_NOT_REQUIRED)) return;

    $data = array();

    if (($itspid < 1) || (empty($newstatus))) {
        return $data;
    }
    if (!xarSecurityCheck('ReadITSP', 1, 'itsp', "$itspid:All:All")) {
        return;
    }

    /* Confirm authorisation code. */
    if (!xarSecConfirmAuthKey()) return;

    $itsp = xarModApiFunc('itsp','user','get',array('itspid'=>$itspid));
    $data['itsp'] = $itsp;
    /*
    // Only status id < 4 can lead to submit
    if (($itsp['itspstatus'] < 4 ) || ($newstatus == 0)){
        // Show form
        return $data;
    }
*/
    $studentname = xarUserGetVar('name',$itsp['userid']);
    $studentemail = xarUserGetVar('email',$itsp['userid']);
    switch ($newstatus) {
        case 1:
            // In progress
            break;
        case 2:
            // Send request to supervisor
            break;
        case 3:
            break;
        case 4:
            // User submits the ITSP

            // Check status
            $stati = xarModApiFunc('itsp','user','getstatusinfo');
            $newstatusname = $stati[$newstatus];

            if (!xarModApiFunc('itsp','user','update',array('itspid'=>$itspid, 'newstatus' => $newstatus))) {
                // todo: add error
                return $data;
            }
            // Check from

            $officemail = xarModGetVar('itsp', 'officemail');
            if (empty($officemail)) {
                $officemail = xarModGetVar('mail', 'adminmail');
            }
            // Check fromname
            if (empty($fromname)) {
                $fromname = xarModGetVar('mail', 'adminname');
            }
            $itspurl = xarModURL('itsp','user','itsp',array('itspid'=>$itspid));
            // Send emails
            $UseStatusVersions = xarModGetVar('itsp', 'UseStatusVersions') ? true : false;
            if ($UseStatusVersions) {
                 $htmltemplate = 'html-' . $itsp['itspstatus'];
                 $texttemplate = 'text-' . $itsp['itspstatus'];
            } else {
                 $htmltemplate = 'html';
                 $texttemplate = 'text';
            }
            $studenthtmlarray= array(
                                  'studentname'   => $studentname,
                                  'studentemail'  => $studentemail,
                                  'itspurl'       => $itspurl,
                                  'newstatusname' => $newstatusname);

            $studenthtmlmessage= xarTplModule('itsp','user','submitmail-student',$studenthtmlarray,$htmltemplate);
            if (xarCurrentErrorID() == 'TEMPLATE_NOT_EXIST') {
                xarErrorHandled();
                $studenthtmlmessage= xarTplModule('itsp', 'user', 'submitmail-student',$studenthtmlarray,'html');
            }

            $studenttextarray =array(
                                  'studentname'   => $studentname,
                                  'studentemail'  => $studentemail,
                                  'itspurl'       => $itspurl,
                                  'newstatusname' => $newstatusname);

            $studenttextmessage= xarTplModule('itsp','user','submitmail-student', $studenttextarray,$texttemplate);
            if (xarCurrentErrorID() == 'TEMPLATE_NOT_EXIST') {
                xarErrorHandled();
                $usertextmessage= xarTplModule('itsp', 'user', 'submitmail-student',$studenttextarray,'text');
            }

            /* now let's do the html message to the office */
            $subject = xarML('An ITSP has been submitted');

            $officehtmlarray=array('studentname'   => $studentname,
                                  'studentemail'  => $studentemail,
                                  'itspurl'       => $itspurl,
                                  'newstatusname' => $newstatusname,
                                  'todaydate'  => time());

            $officehtmlmessage= xarTplModule('itsp','user','submitmail-office',$officehtmlarray,$htmltemplate);
            if (xarCurrentErrorID() == 'TEMPLATE_NOT_EXIST') {
                xarErrorHandled();
                $officehtmlmessage= xarTplModule('itsp', 'user', 'submitmail-office',$officehtmlarray,'html');
            }
            $officetextarray = array('studentname'   => $studentname,
                                  'studentemail'  => $studentemail,
                                  'itspurl'       => $itspurl,
                                  'newstatusname' => $newstatusname,
                                  'todaydate'  => time());

            /* Let's do office text message */
            $officetextmessage= xarTplModule('itsp','user','submitmail-office',$officetextarray,$texttemplate);
            if (xarCurrentErrorID() == 'TEMPLATE_NOT_EXIST') {
                xarErrorHandled();
                $officetextmessage= xarTplModule('itsp', 'user', 'submitmail-office',$officetextarray,'text');
            }

            /* send email to admin */
            $args = array('info'         => $officemail,
                          'name'         => xarML('ITSP office'),
                     //     'ccrecipients' => $ccrecipients,
                     //     'bccrecipients' => $bccrecipients,
                          'subject'      => $subject,
                          'message'      => $officetextmessage,
                          'htmlmessage'  => $officehtmlmessage,
                          'from'         => $studentemail,
                          'fromname'     => $studentname,
                     //     'attachName'   => $attachname,
                     //     'attachPath'   => $attachpath,
                          'usetemplates' => false);
            if ($usehtmlemail != 1) {
                if (!xarModAPIFunc('mail','admin','sendmail', $args))return;
            } else {
                if (!xarModAPIFunc('mail','admin','sendhtmlmail', $args))return;
            }

            break;
        case 5:
            // Approved
            break;
        case 6:
            // Closed, not open anymore, ready for certificate
            break;
    }

    /* lets update status and display updated configuration */
    if (isset($return_url)) {
        xarResponseRedirect(xarModURL($return_url));
    } else {
        xarResponseRedirect(xarModURL('itsp', 'user', 'itsp'));
    }
    /* Return */
    return true;
}
?>