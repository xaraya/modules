<?php

function &reports_userapi_getconnectionobject($args)
{
    extract($args);

    if($conn_id == 1) {
        return xarDBGetConn();
    } else {
        // Another connection
        $connection = xarModAPIFunc('reports','user','connection_get',array('conn_id' => $conn_id));
        ADOLoadCode($connection['type']);
        $repconn =& ADONewConnection($connection['type']);
        if($connection['type'] == "access" || 
           $connection['type'] == "odbc" || 
           $connection['type'] == "odbc_mssql"){
            $repconn->PConnect($connection['database'], $connection['user'],$connection['password'],'En');
        } else if($connection['type'] == "ibase") {
            $repconn->PConnect($connection['server'].":".$connection['database'],$connection['user'],$connection['password']);
        }else {
            $repconn->PConnect($connection['server'],$connection['user'],$connection['password'],$connection['database'],'En');
        }
        return $repconn;
    }
}
?>