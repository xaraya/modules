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
    if(!xarVarFetch('emails', 'str:1:128', $emails, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('author', 'str:1:64', $author, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('authoremail', 'str:1:64', $authoremail, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('authid', 'str', $authid, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('returnurl', 'str', $returnurl, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('objectid', 'int', $objectid, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('extrainfo', 'str', $extrainfo, XARVAR_NOT_REQUIRED)) {return;}

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
		$tpldata['fromemail'] = $authoremail;
		$tpldata['objectid'] = $objectid;
		$tpldata['returnurl'] = $returnurl;
		$tpldata['extrainfo'] = $extrainfo;
        $mailmodule = array();
        $mailmodule['message'] = xarTplModule('sharecontent','user','sendbymail',$tpldata,$template);
		$maxemails=xarModGetVar('sharecontent','maxemails');
        $emailsarray = explode(',',$emails);
		$chunkemails = array_chunk($emailsarray,$maxemails);
		$mailmodule['recipients'] = $chunkemails[0];
        $mailmodule['subject'] = xarTplModule('sharecontent','user','sendbymail-subject',$tpldata,$template);
        $mailmodule['fromname'] = $author;
        $mailmodule['from'] = $authoremail;
		if ($bccinfo = xarModGetVar('sharecontent','bcc')) {
		    $mailmodule['bccinfo'] = $bccinfo;
	    }

        if (xarConfigGetVar('sharecontent','htmlmail')) {
            if (!xarModAPIFunc('htmlmail','admin','sendmail',$mailmodule)) {
               return;
            }
        } else {
            if (!xarModAPIFunc('mail','admin','sendmail',$mailmodule)) {
               return;
            }
        }
    }
    xarResponseRedirect($returnurl);

    return true;
}

?>
