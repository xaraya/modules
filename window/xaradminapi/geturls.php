<?php
function window_adminapi_geturls() {
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $urltable = $xartable['window'];
    $result = $dbconn->Execute("SELECT * FROM $urltable");
    $rows = array();
    while(list($id, $name, $alias) = $result->fields)
    {
        $currentrow['urlid'] = $id;
        $currentrow['urladdress'] = $name;
        $currentrow['urlalias'] = $alias;
        $currentrow['editlink'] = xarModURL('window','admin','editurl', array('id' => $id, 'bluff'=> $id, 'authid' => xarSecGenAuthKey()));
        $currentrow['deletelink'] = xarModURL('window','admin','deleteurl', array('id' => $id, 'bluff'=> $id, 'authid' => xarSecGenAuthKey()));
        $rows[] = $currentrow;
        $result->MoveNext();
    }
    return $rows;
}
?>