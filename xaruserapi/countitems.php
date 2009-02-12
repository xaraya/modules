<?php

function dossier_userapi_countitems($args)
{
	extract($args);
    
    if (!isset($sortby)) {
        $sortby = "sortcompany";
    }
	
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $contactstable = $xartable['dossier_contacts'];

    $sql = "SELECT COUNT(1)
            FROM $contactstable";
            
    $whereclause = array();
    if(!empty($ownerid)) {
        $whereclause[] = "ownerid = ".$ownerid;
    }
    if(!empty($cat_id)) {
        $whereclause[] = "cat_id = ".$cat_id;
    }
    if(!empty($private) && $private == "on") {
        $whereclause[] = "private = 1";
    }
    if(!empty($q)) {
        $whereclause[] = "sortcompany LIKE \"%".xarVar_addSlashes($q)."%\" OR sortname LIKE \"%".xarVar_addSlashes($q)."%\"";
    }
    if(!empty($ltr) && $ltr != "Other") {
        switch($sortby) {
            case "sortcompany":
                $whereclause[] = "sortcompany LIKE '".$ltr."%'";
                break;
            case "sortname":
            default:
                $whereclause[] = "sortname LIKE '".$ltr."%'";
                break;
        }
    }
    if(count($whereclause) > 0) {
        $sql .= " WHERE ".implode(" AND ", $whereclause);
    }
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}
?>
