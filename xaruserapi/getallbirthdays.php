<?php

function dossier_userapi_getallbirthdays($args)
{
    extract($args);
    
    if (!isset($startdate)) {
        $startdate = date("Y-m-d");
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $invalid = array();
//    if (!isset($ownerid) || !is_numeric($ownerid)) {
//        $invalid[] = 'ownerid';
//    }
    if (!isset($startdate) || !is_string($startdate)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'getallbirthdays', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (!xarSecurityCheck('PublicAccess', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $contactstable = $xartable['dossier_contacts'];

    $sql = "SELECT contactid,
                  cat_id,
                  ownerid,
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
                  datemodified
            FROM $contactstable
            WHERE dateofbirth >= '".$startdate."'
            ORDER BY dateofbirth";

    $result = $dbconn->SelectLimit($sql, $numitems);

    if (!$result) return;
    
    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($contactid,
            $cat_id,
            $ownerid,
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
            $datemodified) = $result->fields;
        if (xarSecurityCheck('ClientAccess', 0, 'Item', "All:All:All")) {
    
            if($dateofbirth == "0000-00-00") $dateofbirth = "";
            
            $items[] = array('contactid'    => $contactid,
                              'cat_id'      => $cat_id,
                              'ownerid'     => $ownerid,
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
                              'datemodified'=> $datemodified);
        }
    }

    $result->Close();

    return $items;
}

?>
