<?php

function dossier_userapi_get($args)
{
    extract($args);
    
    if (!isset($contactid) || !is_numeric($contactid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'Contact ID', 'user', 'get', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
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
                  mailinglocid,
                  billinglocid,
                  notes,
                  datecreated,
                  datemodified
            FROM $contactstable
            WHERE contactid = ?";
    $result = &$dbconn->Execute($query,array($contactid));

    if (!$result) return;

    if ($result->EOF) {
        $item = array('contactid' => $contactid,
                        'userid'      => 0,
                        'agentuid' => 0,
                        'private' => 0,
                        'cat_id' => 0,
                        'prefix' => "",
                        'fname' => "",
                        'lname' => "",
                        'sortname' => "[contact deleted]",
                        'company' => "",
                        'title' => "",
                        'contactcode' => "",
                        'dateofbirth' => "",
                        'contactpref' => "",
                        'phone_work' => "",
                        'phone_cell' => "",
                        'phone_fax' => "",
                        'phone_home' => "",
                        'email_1' => "",
                        'email_2' => "",
                        'chat_AIM' => "",
                        'chat_YIM' => "",
                        'chat_ICQ' => "",
                        'chat_MSNM' => "",
                        'contactpref' => "",
                        'notes'       => "",
                        'img'       => "",
                        'datecreated' => "",
                        'datemodified'=> "");
        return $item;

//        $result->Close();
//        $msg = xarML('This item does not exist');
//        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
//                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
//        return;
    }

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
        $mailinglocid,
        $billinglocid,
        $notes,
        $datecreated,
        $datemodified) = $result->fields;

    $result->Close();
/*
    if (!xarSecurityCheck('PublicAccess', 1, 'Item', "All:All:All")) {
        $msg = xarML('Not authorized to view this contact.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'AUTH_FAILED',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
*/    
    $sortname = trim($sortname);
    if(empty($sortname) || $sortname == ",") $sortname = $fname." ".$lname;
    $sortname = trim($sortname);
    if(empty($sortname) || $sortname == ",") $sortname = "no name supplied";
    
    if($dateofbirth == "0000-00-00") $dateofbirth = "";

    $item = array(
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
        'mailinglocid' => $mailinglocid,
        'billinglocid' => $billinglocid,
        'notes'       => $notes,
        'datecreated' => $datecreated,
        'datemodified'=> $datemodified);

    return $item;
}

?>
