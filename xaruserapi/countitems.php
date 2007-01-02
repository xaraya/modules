<?php

function xproject_userapi_countitems($args)
{
    extract($args);
    
    $draftstatus = xarModGetVar('xproject', 'draftstatus');
    $activestatus = xarModGetVar('xproject', 'activestatus');
    $archivestatus = xarModGetVar('xproject', 'archivestatus');

    if (!isset($private)) {
        $private = "";
    }
    if (!isset($q)) {
        $q = "";
    }
    if (!isset($sortby)) {
        $sortby = "";
    }
    if (!isset($clientid) || !is_numeric($clientid)) {
        $clientid = 0;
    }
    if (!isset($projecttype)) {
        $projecttype = "";
    }
    if (!isset($max_priority) || !is_numeric($max_priority)) {
        $max_priority = 0;
    }
    if (!isset($max_importance) || !is_numeric($max_importance)) {
        $max_importance = 0;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $xprojecttable = $xartable['xProjects'];

    $sql = "SELECT COUNT(1)
            FROM $xprojecttable
            WHERE 1";

    if($private == "public") $sql .= " AND private != '1'";
    if($status == "New") {
        $sql .= " AND status NOT IN ('".$draftstatus."','Closed Won','Closed Lost', 'R & D','Hold','".$activestatus."','".$archivestatus."')";
    } elseif($status == "Hold") {
        $sql .= " AND status NOT IN ('".$draftstatus."','".$activestatus."','".$archivestatus."')";
    } elseif(!empty($status)) {
        $sql .= " AND status = '".$status."'";
    } else {
        $sql .= " AND status != '".$archivestatus."'";
    }
    if(!empty($projecttype)) $sql .= " AND projecttype = '".$projecttype."'";
    if($clientid > 0) $sql .= " AND clientid = '".$clientid."'";
    if($max_priority > 0) $sql .= " AND priority <= '".$max_priority."'";
    if($max_importance > 0) $sql .= " AND importance <= '".$max_importance."'";
    if(!empty($q)) {
        $sql .= " AND (project_name LIKE '%".$q."%'
                    OR description LIKE '%".$q."%')";
    }
//if(!empty($status)) die("sql: ".$sql);
    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}
?>