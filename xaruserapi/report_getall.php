<?php
/**
 * Get all reports
 *
 */
function reports_userapi_report_getall() 
{
    $dbconn =& xarDBGetConn();
    $xartables =& xarDBGetTables();
    $tab = $xartables['reports'];
    $cols = &$xartables['reports_column'];
    
    $sql = "SELECT $cols[id],$cols[name],$cols[description],$cols[conn_id],$cols[xmlfile] "
        ."FROM $tab ORDER BY $cols[name]";
    $res= $dbconn->Execute($sql);
    if ($res) {
        $ret = array();
        while (!($res->EOF)) {
            $row = $res->fields;
            $ret[] = array (
                            'id'=>$row[0],
                            'name'=>$row[1],
                            'description'=>$row[2],
                            'conn_id'=>$row[3],
                            'xmlfile'=>$row[4]);
            $res->MoveNext();
        }
        return $ret;
    } else {
        return false;
    }
}

?>