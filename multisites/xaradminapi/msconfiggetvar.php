<?php
// Get a configuration variable
// in a subsite table
// Reconnect to database in case of different database
function multisites_adminapi_msconfiggetvar($args)
{
	extract($args);
	if (empty($name)) {
        $msg = xarML('Empty name.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
    return;
    }
   // Connect to master db - and get the config table
   $dbconn =& xarDBGetConn();
   $xartable 		=& xarDBGetTables();
   $configtable     = $xartable['config_vars'];
   $olddbtype       = xarDBGetType();
   if (!isset($newdbtype) || ($newdbtype='')) {
     $newdbtype=xarDBGetType();
   }

    // Start connection with the new database - assume same type as master.
    $dbsiteconn = ADONewConnection($newdbtype);
    $dbsite 	   = $dbsiteconn->Connect(
 					  xarDBGetHost(), // assume same as master for these atm
					  xarCore_getSystemVar('DB.UserName'),
					  xarCore_getSystemVar('DB.Password'),
					  $msdb  // new site database - maybe same as master
					  );
    if (!$dbsite) return;
    //make sure we get the table with correct prefix
    $$config_varsTable 		= str_replace($masterprefix,$msprefix,$config_varsTable);
    $query = "SELECT xar_value
              FROM $config_varsTable
              WHERE xar_name='" . xarVarPrepForStore($name) . "'";
    $result =& $dbsiteconn->Execute($query);
    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        return;
    }

    //Get data
    list($value) = $result->fields;
    $result->Close();

    // Unserialize variable value
    $value = unserialize($value);

   //force return to master database
    $dbconn = ADONewConnection($olddbtype);
    $dbsite = $dbconn->Connect(
 					  xarDBGetHost(),
					  xarCore_getSystemVar('DB.UserName'),
					  xarCore_getSystemVar('DB.Password'),
					  $masterdb
					  );
     return $value;
}
?>
