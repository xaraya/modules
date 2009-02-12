<?php

function dossier_userapi_mycontactid($args)
{
    extract($args);
    
    $mycontactid = xarSessionGetVar('mycontactid');
    
    if($mycontactid) return $mycontactid;
    
    $userid = xarSessionGetVar('uid');
    
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $contactstable = $xartable['dossier_contacts'];

    $query = "SELECT contactid,
                  cat_id,
                  agentuid,
                  userid,
                  private,
                  contactcode,
                  prefix,
                  lname,
                  fname,
                  sortname,
                  dateofbirth,
                  title,
                  company,
                  sortcompany,
                  img,
                  phone_work,
                  phone_cell,
                  phone_fax,
                  phone_home,
                  email_1,
                  email_2,
                  chat_AIM,
                  chat_YIM,
                  chat_MSNM,
                  chat_ICQ,
                  contactpref,
                  notes,
                  datecreated,
                  datemodified
            FROM $contactstable
            WHERE userid = ?
            ORDER BY private";
    $result = &$dbconn->Execute($query,array($userid));

    if (!$result) return;
    
    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {

        list($contactid,
            $cat_id,
            $agentuid,
            $userid,
            $private,
            $contactcode,
            $prefix,
            $lname,
            $fname,
            $sortname,
            $dateofbirth,
            $title,
            $company,
            $sortcompany,
            $img,
            $phone_work,
            $phone_cell,
            $phone_fax,
            $phone_home,
            $email_1,
            $email_2,
            $chat_AIM,
            $chat_YIM,
            $chat_MSNM,
            $chat_ICQ,
            $contactpref,
            $notes,
            $datecreated,
            $datemodified) = $result->fields;

        if (!xarSecurityCheck('PublicDossierAccess', 1, 'Contact', "All:All:All:All")) {
            $msg = xarML('Not authorized to view this contact.');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'AUTH_FAILED',
                           new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
            return;
        }
        
        $sortname = trim($sortname);
        if(empty($sortname) || $sortname == ",") $sortname = $fname." ".$lname;
        $sortname = trim($sortname);
        if(empty($sortname) || $sortname == ",") $sortname = "no name supplied";
    
        $items[] = array(
            'contactid'   => $contactid,
            'cat_id'      => $cat_id,
            'agentuid'     => $agentuid,
            'userid'      => $userid,
            'private'     => $private,
            'contactcode' => $contactcode,
            'prefix'      => $prefix,
            'lname'       => $lname,
            'fname'       => $fname,
            'sortname'    => $sortname,
            'dateofbirth' => $dateofbirth,
            'title'       => $title,
            'company'     => $company,
            'sortcompany' => $sortcompany,
            'img'         => $img,
            'phone_work'  => $phone_work,
            'phone_cell'  => $phone_cell,
            'phone_fax'   => $phone_fax,
            'phone_home'  => $phone_home,
            'email_1'     => $email_1,
            'email_2'     => $email_2,
            'chat_AIM'    => $chat_AIM,
            'chat_YIM'    => $chat_YIM,
            'chat_MSNM'   => $chat_MSNM,
            'chat_ICQ'    => $chat_ICQ,
            'contactpref' => $contactpref,
            'notes'       => $notes,
            'datecreated' => $datecreated,
            'datemodified'=> $datemodified);
    }

    $result->Close();

    if(count($items) <= 0) {
        xarResponseRedirect(xarModURL('roles','user','account',array('moduleload'=>'dossier')));
        return true;
    }
    
    return $items[0]['contactid'];
}

?>
