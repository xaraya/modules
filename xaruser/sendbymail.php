<?php
/**
 * Sharecontent Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sharecontent Module
 * @link http://xaraya.com/index.php/release/894.html
 * @author Andrea Moro
 */
/**
 * Update configuration
 */
function sharecontent_user_sendbymail($args)
{
    extract($args);
    // Get parameters
    if(!xarVarFetch('emails', 'str:1:128', $emails,NULL, XARVAR_POST_ONLY)) {return;}
    if(!xarVarFetch('author', 'str:1:64', $author,NULL, XARVAR_POST_ONLY)) {return;}
    if(!xarVarFetch('senderemail', 'email', $senderemail,NULL, XARVAR_POST_ONLY)) {return;}
    if(!xarVarFetch('authid', 'str', $authid, NULL, XARVAR_POST_ONLY)) {return;}
    if(!xarVarFetch('returnurl', 'str', $returnurl,NULL, XARVAR_POST_ONLY)) {return;}
    if(!xarVarFetch('objectid', 'int', $objectid, NULL,XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('extrainfo', 'str', $extrainfo, NULL, XARVAR_NOT_REQUIRED)) {return;}

    // Confirm authorisation code; need to add modname to match 
    if (!xarSecConfirmAuthKey('sharecontent')) return;

    // Security Check

    if (xarSecurityCheck('SendSharecontentMail', 0, 'Mail', "All")) {
    // prep mail variables
	    $info = unserialize($extrainfo);
	    if (isset($info['module'])) {
		    $template = $info['module'];
	    } else {
		    $template = '';
	    }
	    $extrainfo = unserialize($extrainfo);
		$tpldata['fromname'] = $author;
		$tpldata['fromemail'] = $senderemail;
		$tpldata['objectid'] = $objectid;
		$tpldata['returnurl'] = rawurldecode($returnurl);
		$tpldata['extrainfo'] = $extrainfo;
        $mailmodule = array();
        $mailmodule['message'] = xarTplModule('sharecontent','user','sendbymail',$tpldata,$template);
        $mailmodule['htmlmessage'] = xarTplModule('sharecontent','user','sendbymail',$tpldata,$template.'-html');
		$maxemails=xarModGetVar('sharecontent','maxemails');
        $emailsarray = explode(',',$emails);
		
		// get recepients and check check for valid email address
		$chunkemails = array_chunk($emailsarray,$maxemails);
		foreach ($chunkemails[0] as $key=>$email) {
		   if (!xarVarValidate('email',$email,true)) unset($chunkemails[0][$key]);
		}
		$mailmodule['recipients'] = $chunkemails[0];
        $mailmodule['subject'] = xarTplModule('sharecontent','user','sendbymail-subject',$tpldata,$template);
        $mailmodule['fromname'] = $author;
        $mailmodule['from'] = $senderemail;
		if ($bccinfo = xarModGetVar('sharecontent','bcc')) {
		    $mailmodule['bccinfo'] = $bccinfo;
	    }

        if (xarConfigGetVar('sharecontent','htmlmail')) {
            if (!xarModAPIFunc('mail','admin','sendhtmlmail',$mailmodule)) {
               return;
            }
        } else {
            if (!xarModAPIFunc('mail','admin','sendmail',$mailmodule)) {
               return;
            }
        }
    }

    return xarResponseRedirect(xarModURL('sharecontent','user','mailsentmsg',
	                      array('returnurl'=>rawurldecode($returnurl),
						        'sentto'=>$mailmodule['recipients'])));
    //xarResponseRedirect($returnurl);

}

?>
