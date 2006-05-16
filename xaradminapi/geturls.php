<?php
function window_adminapi_geturls() 
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $urltable = $xartable['window'];
    $result = $dbconn->Execute("SELECT * FROM $urltable");
    $rows = array();
    while(list($itemid, $name, $alias) = $result->fields)
    {
        $currentrow['urlid'] = $itemid;
        $currentrow['urladdress'] = $name;
        $currentrow['urlalias'] = $alias;
        $currentrow['editlink'] = xarModURL('window','admin','editurl', array('id' => $itemid, 'bluff'=> $itemid, 'authid' => xarSecGenAuthKey()));
        $currentrow['deletelink'] = xarModURL('window','admin','delete', array('itemid' => $itemid, 'bluff'=> $itemid, 'authid' => xarSecGenAuthKey()));
        $rows[] = $currentrow;
        $result->MoveNext();
    }
    return $rows;
}
?>