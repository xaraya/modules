<?php
// Set a configuration variable
//  in the new site tables
// Reconnect to database in case of different database

function multisites_adminapi_msconfigsetvar($args)
{
    extract($args);
	if (empty($name)) {
        $msg = xarML('Empty name.');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
    return $msg;
    }
   // Connect to master db - and get the config table
   list($dbconn) 	= xarDBGetConn();
   $xartable 		=& xarDBGetTables();
   $configtable     = $xartable['config_vars'];
   $olddbtype       = xarDBGetType();

   // Start connection with the new database
   $dbsiteconn = ADONewConnection($newdbtype);
   $dbsite 	   = $dbsiteconn->Connect(
 					  xarDBGetHost(), // assume same as master for these atm
					  xarCore_getSystemVar('DB.UserName'),
					  xarCore_getSystemVar('DB.Password'),
					  $msdb  // new site database - maybe same as master
					  );
   if (!$dbsite) return;

   //make sure we are getting the table with the prefix we want
   $configtable 		= str_replace($masterprefix,$msprefix,$configtable);
   //check the var exists already
   $query = "SELECT xar_value
              FROM $configtable
              WHERE xar_name = '" . xarVarPrepForStore($name) . "'
              ";
    $result = $dbsiteconn->Execute($query);
    if ((!$result) || ($result->EOF)) {
       $mustInsert = true;
    } else {
       $mustInsert = false;
    }

    //Here we serialize the configuration variables
    //so they can effectively contain more than one value
    $value = serialize($value);

    //Here we insert the value if it's new
    //or update the value if it already exists
   if ($mustInsert == true) {
        //Insert
        $seqId = $dbsiteconn->GenId($configtable);
        $query = "INSERT INTO $configtable
                  (xar_id,
                   xar_name,
                   xar_value)
                  VALUES ('$seqId',
                          '" . xarVarPrepForStore($name) . "',
                          '" . xarVarPrepForStore($value). "')";
    } else {
         //Update
         $query = "UPDATE $configtable
                   SET xar_value='" . xarVarPrepForStore($value) . "'
                   WHERE xar_name='" . xarVarPrepForStore($name) . "'";
     }

    $result =& $dbsiteconn->Execute($query);
    if (!$result) return;

    //force return to master database
    $dbconn = ADONewConnection($olddbtype);
    $dbsite = $dbconn->Connect(
 					  xarDBGetHost(),
					  xarCore_getSystemVar('DB.UserName'),
					  xarCore_getSystemVar('DB.Password'),
					  $masterdb
					  );
    return true;
}
?>
