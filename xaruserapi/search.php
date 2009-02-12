<?php

function dossier_userapi_search($args)
{
    extract($args);
    
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    if (!isset($location_cat_id)) {
        $location_cat_id = 0;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $invalid = array();
//    if (!isset($ownerid) || !is_numeric($ownerid)) {
//        $invalid[] = 'ownerid';
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

    if (!xarSecurityCheck('PublicDossierAccess', 0, 'Contact', "All:All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $contactstable = $xartable['dossier_contacts'];
    $locationstable = $xartable['dossier_locations'];
    $locationdatatable = $xartable['dossier_locationdata'];
    
    $sql = "SELECT a.contactid,
                a.cat_id,
                a.agentuid,
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
                a.datemodified";
    if($location_cat_id > 0
        || isset($searchlocation)
        || !empty($searchaddress)
        || !empty($city)
        || !empty($us_state)
        || !empty($postalcode)
        || (!empty($country) && $country != "--") ) {
        // include location tables, otherwise contacts w/o locations do not return
       $sql .= ",    b.cat_id,
                    b.address_1,
                    b.address_2,
                    b.city,
                    b.us_state,
                    b.postalcode,
                    b.country,
                    b.latitude,
                    b.longitude
            FROM $contactstable a, $locationstable b, $locationdatatable c";
    } else {
        $sql .= " FROM $contactstable a";
    }
    $whereclause = array();
    if(!empty($company)) {
        $whereclause[] = "a.company LIKE '%".$company."%'";
    }
    if(!empty($contactcode)) {
        $whereclause[] = "a.contactcode LIKE '%".$contactcode."%'";
    }
    if(!empty($agentuid)) {
        $whereclause[] = "a.agentuid = ".$agentuid;
    }
    if(!empty($cat_id)) {
        $whereclause[] = "a.cat_id = ".$cat_id;
    }
    if(!empty($private) && $private != "on") {
        $whereclause[] = "a.private IS NULL";
    }
    if(!empty($city)) {
        $whereclause[] = "b.city = '".$city."'";
    }
    if(!empty($us_state)) {
        $whereclause[] = "b.us_state = '".$us_state."'";
    }
    if(!empty($postalcode)) {
        $whereclause[] = "b.postalcode = '".$postalcode."'";
    }
    if(!empty($country) && $country != "--") {
        $whereclause[] = "b.country = '".$country."'";
    }
    if(!empty($q)) {
        $whereclause[] = "(a.sortcompany LIKE '%".$q."%' 
                            OR a.sortname LIKE '%".$q."%')";
    }
    if($location_cat_id > 0
        || isset($searchlocation)
        || !empty($searchaddress)
        || !empty($city)
        || !empty($us_state)
        || !empty($postalcode)
        || (!empty($country) && $country != "--") ) {
//if($country == "--") die("check");
//die("test: ".$city.",".$us_state.",".$country);
        $whereclause[] = "a.contactid = c.contactid";
        $whereclause[] = "b.locationid = c.locationid";
    }
    if(count($whereclause) > 0) {
        $sql .= " WHERE ".implode(" AND ", $whereclause);
    }
    $sql .= " ORDER BY a.sortname";
//echo "sql: ".$sql;
    $result = $dbconn->SelectLimit($sql, $numitems, $startnum-1);

    if (!$result) return;
    
    $items = array();

    if($location_cat_id > 0
        || isset($searchlocation)
        || !empty($searchaddress)
        || !empty($city)
        || !empty($us_state)
        || !empty($postalcode)
        || (!empty($country) && $country != "--") ) {
        // include location tables, otherwise contacts w/o locations do not return
        for (; !$result->EOF; $result->MoveNext()) {
            list($contactid,
                $cat_id,
                $agentuid,
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
                $datemodified,
                $location_cat_id,
                $address_1,
                $address_2,
                $city,
                $us_state,
                $postalcode,
                $country,
                $latitude,
                $longitude) = $result->fields;
            if (
                  (
                    xarSecurityCheck('ClientAccess', 0, 'Item', "All:All:All") 
                    && !empty($private) 
                    && $private == 1
                  )
                ||
                  (
                    xarSecurityCheck('PublicAccess', 0, 'Item', "All:All:All") 
                    && $private != 1
                  )
                ) {
                $items[] = array('contactid'    => $contactid,
                                  'cat_id'      => $cat_id,
                                  'agentuid'     => $agentuid,
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
                                  'datemodified'=> $datemodified,
                                  'location_cat_id'=> $location_cat_id,
                                  'address_1'   => $address_1,
                                  'address_2'   => $address_2,
                                  'city'        => $city,
                                  'us_state'    => $us_state,
                                  'postalcode'  => $postalcode,
                                  'country'     => $country,
                                  'latitude'    => $latitude,
                                  'longitude'   => $longitude);
            }
        }
    } else { // just the contact info table, no location data
    
        for (; !$result->EOF; $result->MoveNext()) {
            list($contactid,
                $cat_id,
                $agentuid,
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
            if (
                  (
                    xarSecurityCheck('ClientDossierAccess', 0, 'Contact', "All:All:All:All") 
                    && !empty($private) 
                    && $private == "on"
                  )
                ||
                  (
                    xarSecurityCheck('PublicDossierAccess', 0, 'Contact', "All:All:All:All") 
                  )
                ) {
                $items[] = array('contactid'    => $contactid,
                                  'cat_id'      => $cat_id,
                                  'agentuid'     => $agentuid,
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
        
    }
        
    $result->Close();

    return $items;
    
}

?>
