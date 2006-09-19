<?php
/**
 * xTasks Module - Project ToDo management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xTasks Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
function xtasks_userapi_countitems($args)
{
    extract($args);

    if(empty($parentid)) $parentid = 0;

    if (!isset($parentid)
        && !isset($projectid)
        && (!isset($modid) || !isset($objectid))) {
        $parentid = '0';
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $xtasks_table = $xartable['xtasks'];
    $xtasks_column = &$xartable['xtasks_column'];

    $sql = "SELECT COUNT(1) FROM $xtasks_table";

    $whereclause = array();
    $whereclause[] = "parentid=".$parentid;

    if(isset($mymemberid)) {
        $whereclause[] = "owner=".$mymemberid;
    }
    if(isset($memberid)) {
        $whereclause[] = "(creator=".$memberid." OR assigner=".$memberid.")";
    }

    if (!empty($projectid)) {
        $whereclause[] = "projectid=".$projectid;
    } elseif (!empty($modid)) {
        $hookedsql = "( modid=".$modid;
        if (!empty($objectid)) {
            $hookedsql .= " AND objectid=".$objectid;
        }
        if (!empty($itemtype)) {
            $hookedsql .= " AND itemtype=".$itemtype;
        }
        $hookedsql .= " )";

        if (!empty($parentid)) {
            $hookedsql .= " OR parentid=".$parentid;
        }
        $whereclause[] = $hookedsql;
    } elseif (!empty($parentid)) {
        $whereclause[] = "parentid=".$parentid;
    }

    if (!empty($statusfilter)) {
        $whereclause[] = "status='".$statusfilter."'";
    } else {
        $statusfilter = "";
    }

    if(count($whereclause) > 0) $sql .= " WHERE ".implode(" AND ", $whereclause);


    $result = $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    list($numtasks) = $result->fields;

    $result->Close();

    return $numtasks;
}
?>