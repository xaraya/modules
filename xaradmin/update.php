<?php

function dossier_admin_update($args)
{
    if (!xarVarFetch('contactid', 'id', $contactid, $contactid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cat_id', 'id', $cat_id, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('agentuid', 'id', $agentuid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('userid', 'id', $userid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('private', 'str:1:', $private, $private, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactcode', 'str:1:', $contactcode, $contactcode, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('prefix', 'str:1:', $prefix, $prefix, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('lname', 'str::', $lname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('fname', 'html:basic', $fname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortname', 'str:1:', $sortname, $sortname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dateofbirth', 'str::', $dateofbirth, $dateofbirth, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('title', 'str:1:', $title, $title, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('company', 'str::', $company, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortcompany', 'str::', $sortcompany, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('img', 'isset::', $img, $img, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('delete_img', 'isset::', $delete_img, $delete_img, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phone_work', 'isset::', $phone_work, $phone_work, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phone_cell', 'isset::', $phone_cell, $phone_cell, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phone_fax', 'isset::', $phone_fax, $phone_fax, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phone_home', 'isset::', $phone_home, $phone_home, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('email_1', 'str::', $email_1, $email_1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('email_2', 'str::', $email_2, $email_2, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('chat_AIM', 'str::', $chat_AIM, $chat_AIM, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('chat_YIM', 'str::', $chat_YIM, $chat_YIM, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('chat_MSNM', 'str::', $chat_MSNM, $chat_MSNM, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('chat_ICQ', 'str::', $chat_ICQ, $chat_ICQ, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactpref', 'str::', $contactpref, $contactpref, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, $notes, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, $returnurl, XARVAR_NOT_REQUIRED)) return;
    
    if(isset($_FILES['img_attach_upload']) && !empty($_FILES['img_attach_upload']['name'])) {
        $fileinfo = xarModApiFunc('uploads','user', 'prepare_uploads', array('fileInfo' =>$_FILES['img_attach_upload']) );
        $myimageinfo = current($fileinfo);
        if(isset($myimageinfo['errors'])) {
            $msg = $myimageinfo['errors'][0]['errorMesg'];
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new SystemException($msg));
            return;
        }
        $imageinfo = xarModApiFunc('uploads','user', 'file_store', array('fileInfo' => $myimageinfo) );
        $img = $imageinfo['fileId'];
    }

    if (is_array($phone_work)) {
        if (!empty($phone_work['NPA']) && !empty($phone_work['NXX']) && !empty($phone_work['phone'])) {
            if (is_numeric($phone_work['NPA']) && is_numeric($phone_work['NXX']) && is_numeric($phone_work['phone']) &&
                strlen($phone_work['NXX']) == 3 && strlen($phone_work['NXX']) == 3 && strlen($phone_work['phone']) == 4) {
                
                if(is_numeric($phone_work['extension']) && strlen($phone_work['extension']) > 0) {
                    $phone_work = sprintf('%03d-%03d-%04d x%04d',$phone_work['NPA'],$phone_work['NXX'],$phone_work['phone'],$phone_work['extension']);
                } else {
                    $phone_work = sprintf('%03d-%03d-%04d',$phone_work['NPA'],$phone_work['NXX'],$phone_work['phone']);
                }
                
            } else {
                $phone_work = $phone_work['NPA']."-".$phone_work['NXX']."-".$phone_work['phone'];
            }
        } else {
            $phone_work = xarML('invalid');
        }
    }

    if (is_array($phone_cell)) {
        if (!empty($phone_cell['NPA']) && !empty($phone_cell['NXX']) && !empty($phone_cell['phone'])) {
            if (is_numeric($phone_work['NPA']) && is_numeric($phone_cell['NXX']) && is_numeric($phone_cell['phone']) &&
                strlen($phone_cell['NXX']) == 3 && strlen($phone_cell['NXX']) == 3 && strlen($phone_cell['phone']) == 4) {
                
                if(is_numeric($phone_cell['extension']) && strlen($phone_cell['extension']) > 0) {
                    $phone_cell = sprintf('%03d-%03d-%04d x%04d',$phone_cell['NPA'],$phone_cell['NXX'],$phone_cell['phone'],$phone_cell['extension']);
                } else {
                    $phone_cell = sprintf('%03d-%03d-%04d',$phone_cell['NPA'],$phone_cell['NXX'],$phone_cell['phone']);
                }
                
            } else {
                $phone_cell = $phone_cell['NPA']."-".$phone_cell['NXX']."-".$phone_cell['phone'];
            }
        } else {
            $phone_cell = xarML('invalid');
        }
    }

    if (is_array($phone_fax)) {
        if (!empty($phone_fax['NPA']) && !empty($phone_fax['NXX']) && !empty($phone_fax['phone'])) {
            if (is_numeric($phone_work['NPA']) && is_numeric($phone_fax['NXX']) && is_numeric($phone_fax['phone']) &&
                strlen($phone_fax['NXX']) == 3 && strlen($phone_fax['NXX']) == 3 && strlen($phone_fax['phone']) == 4) {
                
                if(is_numeric($phone_fax['extension']) && strlen($phone_fax['extension']) > 0) {
                    $phone_fax = sprintf('%03d-%03d-%04d x%04d',$phone_fax['NPA'],$phone_fax['NXX'],$phone_fax['phone'],$phone_fax['extension']);
                } else {
                    $phone_fax = sprintf('%03d-%03d-%04d',$phone_fax['NPA'],$phone_fax['NXX'],$phone_fax['phone']);
                }
                
            } else {
                $phone_fax = $phone_fax['NPA']."-".$phone_fax['NXX']."-".$phone_fax['phone'];
            }
        } else {
            $phone_fax = xarML('invalid');
        }
    }

    if (is_array($phone_home)) {
        if (!empty($phone_home['NPA']) && !empty($phone_home['NXX']) && !empty($phone_home['phone'])) {
            if (is_numeric($phone_work['NPA']) && is_numeric($phone_home['NXX']) && is_numeric($phone_home['phone']) &&
                strlen($phone_home['NXX']) == 3 && strlen($phone_home['NXX']) == 3 && strlen($phone_home['phone']) == 4) {
                
                if(is_numeric($phone_home['extension']) && strlen($phone_home['extension']) > 0) {
                    $phone_home = sprintf('%03d-%03d-%04d x%04d',$phone_home['NPA'],$phone_home['NXX'],$phone_home['phone'],$phone_home['extension']);
                } else {
                    $phone_home = sprintf('%03d-%03d-%04d',$phone_home['NPA'],$phone_home['NXX'],$phone_home['phone']);
                }
                
            } else {
                $phone_home = $phone_home['NPA']."-".$phone_home['NXX']."-".$phone_home['phone'];
            }
        } else {
            $phone_home = xarML('invalid');
        }
    }
    
    $contactinfo = xarModAPIFunc('dossier', 'user', 'get', array('contactid' => $contactid));
    
    if($contactinfo == false) return;
            
    if(empty($img)) $img = $contactinfo['img'];
    if($delete_img) $img = "";
    
    if(empty($returnurl)) $returnurl = xarModURL('dossier', 'admin', 'view');
    
    extract($args);
    if (!xarSecConfirmAuthKey()) return;
    if(!xarModAPIFunc('dossier',
					'admin',
					'update',
					array('contactid'	=> $contactid,
						'cat_id' 	    => $cat_id,
                        'agentuid'	    => $agentuid,
                        'userid'	    => $userid,
                        'private'	    => $private,
                        'contactcode'	=> $contactcode,
                        'prefix'	    => $prefix,
                        'lname'	        => $lname,
                        'fname'	        => $fname,
                        'sortname'	    => $sortname,
                        'dateofbirth'   => $dateofbirth,
                        'title'		    => $title,
                        'company'	    => $company,
                        'sortcompany'	=> $sortcompany,
                        'img'	        => $img,
                        'phone_work'	=> $phone_work,
                        'phone_cell'	=> $phone_cell,
                        'phone_fax'	    => $phone_fax,
                        'phone_home'	=> $phone_home,
                        'email_1'	    => $email_1,
                        'email_2'	    => $email_2,
                        'chat_AIM'	    => $chat_AIM,
                        'chat_YIM'	    => $chat_YIM,
                        'chat_MSNM'	    => $chat_MSNM,
                        'chat_ICQ'	    => $chat_ICQ,
                        'contactpref'	=> $contactpref,
                        'notes'	        => $notes))) {
		return;
	}


	xarSessionSetVar('statusmsg', xarML('Contact Updated'));

    xarResponseRedirect($returnurl);

    return true;
}

?>
