<?php
// File: $Id$
/*
 * Xaraya Multisites
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Multisites Module
 * @author
 */

/**
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('multisites','admin','add') to create a new site
  */

function multisites_admin_createsite($args)
{
//global $HTTP_SERVER_VARS;

    if (!xarVarFetch('siteDN', 'str:2:', $siteDN)) {
                 $msg = xarML('Please enter a name for the new site');
            xarExceptionSet(XAR_USER_EXCEPTION, 'DATA_MISSING', new DefaultUserException($msg));
            return $msg;
    }
    if (!xarVarFetch('msPrefix', 'str:2:', $msPrefix)) {
            $msg = xarML('Please enter a unique Table Prefix for this site.');
            xarExceptionSet(XAR_USER_EXCEPTION, 'DATA_MISSING', new DefaultUserException($msg));
            return $msg;
    }
    if (!xarVarFetch('siteDB', 'str:1:', $siteDB)) return;
    if (!xarVarFetch('sharedTables', 'str:1:', $sharedTables, $msPrefix )) return;
    if (!xarVarFetch('siteStatus', 'int:1:', $siteStatus,'0')) return;
    if (!xarVarFetch('createdb', 'int:1:', $createdb, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $btntxt,'', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $exid = $objectid;
    }

    //Let's make sure the prefix is the table SITE prefix until prefix sharing is working
    //Remove this when table sharing is working as required in xar code
    $sharedTables=$msPrefix;

   // Auth Key
    if (!xarSecConfirmAuthKey()) {return;}

    // Security
    if (!xarSecurityCheck('AddMultisites')) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }
     $sitedir = xarModAPIFunc('multisites','admin','cleandn', array('sitedn' => $siteDN));
        if (!$sitedir) {
            $msg = xarML("Could not clean #(1)", $siteDN);
            xarExceptionSet(XAR_USER_EXCEPTION, 'ERROR-CLEANDN', new DefaultUserException($msg));
            return $msg;
        }
   $sitedir=$sitedir['sitedn'];

   //Check the site does not already exist
   $prefixexists = xarModAPIFunc('multisites',
                                 'user',
                                 'getall',
                             array('where' => 'WHERE xar_mssite = "'.$sitedir.'"'));

    if ($prefixexists) {
            $msg = xarML("Sorry, a site with domain name #(1) already exists. Please use a different site (sub)domain name", $sitedir);
            xarExceptionSet(XAR_USER_EXCEPTION, 'ALREADY_EXISTS', new DefaultUserException($msg));
            return $msg;
    }

   //Check the prefix does not already exist
   $prefixexists = xarModAPIFunc('multisites',
                                     'user',
                                     'getall',
                               array('where'   =>
                                   'WHERE xar_msprefix = "'.$msPrefix.'" AND xar_msdb = "'.$siteDB.'"'));

    if ($prefixexists) {
            $msg = xarML("Sorry, a subsite already exists in database #(1) with database prefix #(2) Please use a different table prefix.", $siteDB, $msPrefix);
            xarExceptionSet(XAR_USER_EXCEPTION, 'ALREADY_EXISTS', new DefaultUserException($msg));
            return $msg;
    }
    $data=array();

    $cWhereIsPerso =xarModGetVar('multisites','masterfolder');

    $masterdb=xarDBGetName();
    $masterprefix=xarDBGetSiteTablePrefix();

    $data['masterfolder']=$cWhereIsPerso;
    $data['masterdb']    =$masterdb;
    $data['msprefix']    =$msPrefix;
    $data['masterprefix']=$masterprefix;
    if (!isset($newdbtype) || ($newdbtype='')) {
     $newdbtype=xarDBGetType();
    }

     //Set the new config vars
    $setmultisitevar = xarModAPIFunc('multisites',
                                    'admin',
                                    'msconfigsetvar',
                                array('name'         => 'System.MS.MultiSites',
                                      'value'        => 1,
                                      'msprefix'     => $msPrefix,
				                      'masterprefix' => $masterprefix,
				                      'msdb'         => $siteDB,
                                      'masterdb'     => $masterdb,
                                      'newdbtype'    => $newdbtype));
    if (!$setmultisitevar) {
       $msg = xarML('Unable to set configuration vars for #(1) Check your database and tables exist and try again.', $sitedn);
       xarExceptionSet(XAR_USER_EXCEPTION, 'UNABLE TO CONNECT TO DATABASE', new DefaultUserException($msg));
    return;
    }

    // Set the site prefix
    $setmultisiteprefix = xarModAPIFunc('multisites',
                                         'admin',
                                         'msconfigsetvar',
                                array('name'         => 'Site.DB.TablePrefix',
                                      'value'        => $msPrefix,
                                      'msprefix'     => $msPrefix,
						              'masterprefix' => $masterprefix,
						              'msdb'         => $siteDB,
                                      'masterdb'     => $masterdb,
                                      'newdbtype'    => $newdbtype));

    if (!$setmultisiteprefix) {
       $msg = xarML('Unable to set configuration vars for #(1) Check your database and tables exist and try again.', $sitedn);
       xarExceptionSet(XAR_USER_EXCEPTION, 'UNABLE TO CONNECT TO DATABASE '.$siteDB, new DefaultUserException($msg));
       return;
    }
    //set the shared tables prefix(system)

    $setmultisiteshare = xarModAPIFunc('multisites',
                                  'admin',
                                  'msconfigsetvar',
                                array('name'         => 'System.DB.TablePrefix',
                                      'value'        => $sharedTables,
                                      'msprefix'     => $msPrefix,
						              'masterprefix' => $masterprefix,
						              'msdb'         => $siteDB,
                                      'masterdb'     => $masterdb,
                                      'newdbtype'    => $newdbtype));
    if (!$setmultisiteshare) {
       $msg = xarML('Unable to set configuration vars for #(1) Check your database and tables exist and try again.', $sitedn);
       xarExceptionSet(XAR_USER_EXCEPTION, 'UNABLE TO CONNECT TO DATABASE '.$siteDB, new DefaultUserException($msg));
       return;
    }

    // TO DO - set subsite mod vars

    //Create new masterfolder data tree
    $var = is_dir($cWhereIsPerso."/".$sitedir);
    if ($var == true) { // Now, we have checked in the database for existance - enough - else also maybe exist due to some error
    //     $msg = xarML("The subsite folder #(1)/#(2) already exists!\nRemove this subsite and recreate, or edit the exising subsite.",
    //            $cWhereIsPerso, $sitedir);
    //            xarExceptionSet(XAR_USER_EXCEPTION, 'EXISTING_DIRECTORY', new DefaultUserException($msg));
    //            return $msg;
    } else {
         $oldumask = umask(0);
         if (!mkdir($cWhereIsPerso."/".$sitedir,0755)) {
            $msg = xarML("The subsite directory #(1)/#(2) was not created!", $cWhereIsPerso, $sitedir);
            xarExceptionSet(XAR_USER_EXCEPTION, 'FILE_NOT_CREATED', new DefaultUserException($msg));
            return $msg;
         }

         umask($oldumask);
    }
    $var = is_dir($cWhereIsPerso."/".$sitedir."/var");
    if ($var == false) { // directory doesn't exist - let's make it
        $oldumask = umask(0);
        if (!mkdir($cWhereIsPerso."/".$sitedir."/var",0755)) {
            $msg = xarML("The subsite var directory #(1)/#(2)/var was not created!", 
                $cWhereIsPerso, $sitedir);
            xarExceptionSet(XAR_USER_EXCEPTION, 'FILE_NOT_CREATED', new DefaultUserException($msg));
            return $msg;
        }
        umask($oldumask);
    }

    //get the master config.system.php file in the correct directory
    $sitedn=xarModGetVar('multisites','masterurl');
    $mastersitedir = xarModAPIFunc('multisites','admin','cleandn', array('sitedn' => $sitedn));
        if (!$mastersitedir) {
            $msg = xarML("Could not clean #(1)", $mastersitedn);
            xarExceptionSet(XAR_USER_EXCEPTION, 'ERROR-CLEANDN', new DefaultUserException($msg));
            return $msg;
        }
    //$masterdir=$mastersitedir['sitedn']; //TODO: Later when changes completed
    $masterdir='master';

    // copy the master config.system.php file to the new master/var directory
    $filenamein = $cWhereIsPerso.'/'.$masterdir.'/var/config.system.php';
    $filenameout= $cWhereIsPerso.'/'.$sitedir.'/var/config.system.php';
    if (!copy($filenamein,$filenameout)) {
        $msg = xarML("Unable to copy master config to #(1)/#(2)/var", $cWhereIsPerso, $sitedir);
        xarExceptionSet(XAR_USER_EXCEPTION, 'CANNOT COPY FILE', new DefaultUserException($msg));
        return;
    }

    // update the new subsite config file
    $configfile =getcwd().'/'.$cWhereIsPerso."/".$sitedir."/var/config.system.php";
    if (!(empty($COMSPEC))) {
        $configfile = str_replace("/","\\",$configfile); //windows
    } else {
        $configfile = str_replace("\\","/",$configfile);
    }

   // Let's play safe in case the wrong master config.system.php file is copied somehow
   // Get rid of any master multisite config lines
   // and also, if an old installation doesn't have site prefix in master config file
    umask();
    $oldConfig=file($configfile);
    $fd = fopen($configfile,'r');
    while (list ($line_num, $line) = each ($oldConfig)) {
        if ((strstr($line,"<?php")) ||
            (strstr($line,"?>")) ||
            (strstr($line,"GLOBALS")) ||
            (strstr($line,"file_exists")) ||
            (strstr($line,"else")) ||
            (strstr($line,"}")) ||
            (strstr($line,"systemConfiguration['DB.Name']")) ||
            (strstr($line,"siteConfiguration['DB.TablePrefix']")) ||
            (strstr($line,"systemConfiguration['DB.TablePrefix']")) ||
            (strstr($line,"Multisites")) ||
            (strstr($line,"'MS."))) {
           // do nothing
        }else{
          $holdConfig[$line_num]=$oldConfig[$line_num];
        }
     }
     $oldumask = umask(0);

     $fd=fopen($configfile,'wb');
     if ($fd) {
        fwrite($fd, "<?php\n");
        while (list ($line_num, $line) = each($holdConfig)) {
            fwrite($fd,$line);
        }
        fwrite($fd,"// Database Name: the name of the database to connect to.\n");
        fwrite($fd,"\$systemConfiguration['DB.Name'] = '".$siteDB."';\n");
        fwrite($fd,"// Database TablePrefix: prefixed to database tables that are part of the core and shared.\n");
        fwrite($fd,"\$systemConfiguration['DB.TablePrefix'] = '".$sharedTables."';\n");
        fwrite($fd,"// Database TablePrefix: prefixed to all site specific tables.\n");
        fwrite($fd,"\$siteConfiguration['DB.TablePrefix'] = '".$msPrefix."';\n");
        fwrite($fd,"// Multisites: Set multisite flag on.\n");
        fwrite($fd,"\$systemConfiguration['MS.MultiSites'] = '1';\n");
        fwrite($fd,"// Multisites: Set this Multisite SubSite Active.\n");
        fwrite($fd,"\$systemConfiguration['MS.Active'] = '1';\n");
        fwrite($fd, "?>\n");
        fclose($fd);
     } else {
        //echo "Can't modify the config file";
        return false;
     }
     umask($oldumask);

     $sitefolder =$cWhereIsPerso."/".$sitedir;
     //Update new site to the Master multisite table
     $msid = xarModAPIFunc('multisites',
                              'admin',
                              'create',
                        array('mssite'     => $siteDN,
                              'msprefix'   => $msPrefix,
                              'msdb'       => $siteDB,
                              'msshare'    => $sharedTables,
                              'msstatus'   => $siteStatus,
                              'sitefolder' => $sitefolder));

     if (!$msid) return;

     // TO DO - option to create database, create tables - then should be enough
     // to give some time for proper multisites planned and implemented

/* TO DO - fix this and put in separate function with table creation when done.
     if ($createdb) {
        xarModAPILoad('installer','admin');
        $dbtype =xarDBGetType();
        if (!xarModAPIFunc('installer', 'admin', 'createdb', array('dbName' => $siteDB, 'dbType' =>$dbtype ))) {
             $msg = 'The database cannot be created. Please create the database '.$siteDB. ' first and return to configure your site';
             xarExceptionSet(XAR_USER_EXCEPTION, 'CANNOT CREATE DATABSE', new DefaultUserException($msg));
          return;
        }
    }
*/
   xarResponseRedirect(xarModURL('multisites', 'admin', 'view'));
   // success

   return true;
}


?>