<?php

function contact_adminapi_delete_titles($args)
{
 // Get arguments from argument array - all arguments to this function
    // should be obtained from the $args array, getting them from other
    // places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    extract($args);
    // Get database setup - note that both xarDBGetConn() and xarDBGetTables()
    // return arrays but we handle them differently.  For xarDBGetConn()
    // we currently just want the first item, which is the official
    // database handle.  For xarDBGetTables() we want to keep the entire
    // tables array together for easy reference later on
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // It's good practice to name the table and column definitions you
    // are getting - $table and $column don't cut it in more complex
    // modules
    $contacttable = $xartable['contact_titles'];
       $deletetitle = $cid;
       for($i = 0; $i < count($deletetitle); $i++) {
           $deletetitleID = $cid[$i];
           $query = "DELETE FROM $contacttable WHERE xar_id = $deletetitleID";
           $result = $dbconn->Execute($query);
            if (!$result) return;
       }
       if (!isset($codel) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back

    // Success
    xarSessionSetVar('statusmsg', xarMLByKey('TITLEDELETED'));
    return true;
   }

?>