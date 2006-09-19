<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_userapi_countmemberprojects($args)
{
    extract($args);

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
    $teamtable = $xartable['xProject_team'];

    $sql = "SELECT COUNT(1)
            FROM $xprojecttable a, $teamtable b
            WHERE b.projectid = a.projectid
            AND b.memberid = $memberid";

    if($private == "public") $sql .= " AND private != '1'";
    if(!empty($status)) $sql .= " AND status = '".$status."'";
    if(!empty($projecttype)) $sql .= " AND projecttype = '".$projecttype."'";
    if($clientid > 0) $sql .= " AND clientid = '".$clientid."'";
    if($max_priority > 0) $sql .= " AND priority <= '".$max_priority."'";
    if($max_importance > 0) $sql .= " AND importance <= '".$max_importance."'";
    if(!empty($q)) {
        $sql .= " AND (project_name LIKE '%".$q."%'
                    OR description LIKE '%".$q."%')";
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