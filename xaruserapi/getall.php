<?php

function dossier_userapi_getall($args)
{
    extract($args);
    
    if (!isset($sortby)) {
        $sortby = "sortcompany";
    }
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $invalid = array();
//    if (!isset($agentuid) || !is_numeric($agentuid)) {
//        $invalid[] = 'agentuid';
//    }
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'user', 'getall', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
/*  use individual item checks instead
    if (!xarSecurityCheck('PublicAccess', 0, 'Contact', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
*/
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $contactstable = $xartable['dossier_contacts'];
    
    $logstable = $xartable['dossier_logs'];

    $sql = "SELECT a.contactid,
                  a.cat_id,
                  a.agentuid,
                  a.userid,
                  a.private,
                  a.contactcode,
                  a.prefix,
                  a.lname,
                  a.fname,
                  a.sortname,
                  a.dateofbirth,
                  a.title,
                  a.company,
                  a.sortcompany,
                  a.img,
                  a.phone_work,
                  a.phone_cell,
                  a.phone_fax,
                  a.phone_home,
                  a.email_1,
                  a.email_2,
                  a.chat_AIM,
                  a.chat_YIM,
                  a.chat_MSNM,
                  a.chat_ICQ,
                  a.contactpref,
                  a.notes,
                  a.datecreated,
                  a.datemodified,
                  b.logid,
                  b.ownerid,
                  b.logtype,
                  b.logdate,
                  b.notes
            FROM $contactstable a
            LEFT JOIN $logstable b
            ON b.contactid = a.contactid";
            
    $bindvars = array();
    $whereclause = array();
    if(!empty($contactid)) {
        $whereclause[] = "a.contactid = ?";
        $bindvars[] = $contactid;
    }
    if(!empty($userid)) {
        $whereclause[] = "a.userid = ?";
        $bindvars[] = $userid;
    }
    if(!empty($lname)) {
        $whereclause[] = "a.lname = ?";
        $bindvars[] = $lname;
    }
    if(!empty($fname)) {
        $whereclause[] = "a.fname = ?";
        $bindvars[] = $fname;
    }
    if(!empty($company)) {
        $whereclause[] = "a.company = ?";
        $bindvars[] = $company;
    }
    if(!empty($contactcode)) {
        $whereclause[] = "a.contactcode = ?";
        $bindvars[] = $contactcode;
    }
    if(!empty($agentuid)) {
        $whereclause[] = "a.agentuid = ?";
        $bindvars[] = $agentuid;
    }
    if(!empty($cat_id)) {
        $whereclause[] = "a.cat_id = ?";
        $bindvars[] = $cat_id;
    }
    if(!empty($private)) {
        if($private == "on") {
            $whereclause[] = "a.private = 1";
        } elseif($private == "off") {
            $whereclause[] = "a.private = 0";
        }
    }
    if(!empty($q)) {
        $whereclause[] = "(a.sortcompany LIKE \"%".xarVar_addSlashes($q)."%\" 
                        OR a.sortname LIKE \"%".xarVar_addSlashes($q)."%\"
                        OR a.email_1 LIKE \"%".xarVar_addSlashes($q)."%\"
                        OR a.email_2 LIKE \"%".xarVar_addSlashes($q)."%\")";
    }
    if(!empty($searchphone)) {
        $searcharray = preg_split('//', $searchphone, -1, PREG_SPLIT_NO_EMPTY);
        $searchlist = array();
        foreach($searcharray as $searchval) {
            if(is_numeric($searchval)) {
                $searchlist[] = (int)$searchval;
            } else {
                $searchlist[] = "-";
            } 
        }
        $searchstring = implode("", $searchlist);
        
        if(strlen($searchstring) > 4 && strpos($searchstring, "-") === false) {
            $searchlist = array();
            foreach($searcharray as $searchval) {
                $searchlist[] = (int)$searchval;
            }
            $searchstring = implode("-", $searchlist);
        }
    
        
        $searchphonearray = explode("-", $searchstring);
        
        $searchstring = "(";
        $firststring = true;
        foreach($searchphonearray as $phonestring) {
            if($firststring) {
                $firststring = false;
            } else {
                $searchstring .= ").*(";
            }
            $searchstring .= $phonestring;
        }
        $searchstring .= ")";
        $whereclause[] = "(
                            a.phone_work REGEXP \"".$searchstring."\"
                        OR a.phone_fax REGEXP \"".$searchstring."\"
                        OR a.phone_cell REGEXP \"".$searchstring."\"
                        OR a.phone_home REGEXP \"".$searchstring."\"
                          )";
    }
    if(!empty($ltr) && $ltr != "Other") {
        switch($sortby) {
            case "sortcompany":
                $whereclause[] = "a.sortcompany LIKE ?";
                $bindvars[] = $ltr."%";
                break;
            case "sortname":
            default:
                $whereclause[] = "a.sortname LIKE ?";
                $bindvars[] = $ltr."%";
                break;
        }
    }
    if(count($whereclause) > 0) {
        $sql .= " WHERE ".implode(" AND ", $whereclause);
    }
//    $sql .= " GROUP BY a.contactid";
    switch($sortby) {
        case "sortname":
            $sql .= " ORDER BY b.logdate DESC, a.sortname, a.lname, a.fname, a.company";
            break;
        case "sortcompany": 
        default:
            $sql .= " ORDER BY b.logdate DESC, a.sortcompany, a.company, a.lname, a.fname";
            break;
    }
    $sql .= ", b.logdate DESC";
    
    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1, $bindvars);

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
            $datemodified,
            $logid,
            $logownerid,
            $logtype,
            $logdate,
            $lognotes) = $result->fields;
        if (
              (
                !$private && xarSecurityCheck('PublicDossierAccess', 0, 'Contact', "$cat_id:$userid:$company:$agentuid") 
              )
              ||
              (
                xarSecurityCheck('ClientDossierAccess', 0, 'Contact', "$cat_id:$userid:$company:$agentuid") 
              )
            ) {
    
            $sortname = trim($sortname);
            if(empty($sortname) || $sortname == ",") $sortname = $fname." ".$lname;
            $sortname = trim($sortname);
            if(empty($sortname) || $sortname == ",") $sortname = "no name supplied";
    
            if($dateofbirth == "0000-00-00") $dateofbirth = "";

            $items[$contactid] = array(
                'contactid'     => $contactid,
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
                'datemodified'=> $datemodified,
                'logid'         => $logid,
                'logownerid'    => $logownerid,
                'logtype'       => $logtype,
                'logdate'       => $logdate,
                'lognotes'      => $lognotes);
        }
    }
    
    $result->Close();

    return $items;
}

?>
