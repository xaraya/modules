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
 * This is a function that is called with the results of the
 * form supplied by xarModFunc('multisites','admin','updateconfig') to create a new Master Site
 * This function is run once to setup the Master site, and then to modify settings if required
 */
function multisites_admin_setconfig($args)
{
global $HTTP_SERVER_VARS;

    if (!xarVarFetch('servervar', 'str:9:', $servervar, 'HTTP_HOST')) return;
    if (!xarVarFetch('themepath', 'str:2:', $themepath, 'themes')) return;
    if (!xarVarFetch('varpath', 'str:2:', $varpath, 'var')) return;
    if (!xarVarFetch('masterfolder', 'str:4:', $cWhereIsPerso, 'xarsites')) return;
    if (!xarVarFetch('DNexts', 'str', $DNexts, '.com,.org,.net')) return;


   //check security
   if (!xarSecurityCheck('AdminMultisites')) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
   }
    //Check the site folder exists
    $var = is_dir($cWhereIsPerso);
    if ($var != true) {
        $msg = xarML("You master site directory ".$cWhereIsPerso." does not exist! Please create it first!.");
            xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DIRECTORY', new DefaultUserException($msg));
            return $msg;
    }
    //Check the site data folder is writable
    $var = is_writeable($cWhereIsPerso);
    if ($var == true) {
          // echo "The directory is writable";
    } else {
            $msg = xarML("The Site Data Directory ".$cWhereIsPerso." is not writeable!\n
                          Please chmod 777 and try again.");
            xarExceptionSet(XAR_USER_EXCEPTION, 'FILE_NON-WRITEABLE', new DefaultUserException($msg));
    return $msg;
    }

    //Check the master config data folder is writable
    $var = is_writeable('./var/config.system.php');
    if ($var == true) {
          // echo "The file is writable";
    } else {
            $msg = xarML('The master config.system.php file  /var/config.system.php is not writeable!\n
                          Please chmod 666 and try again.');
            xarExceptionSet(XAR_USER_EXCEPTION, 'FILE_NON-WRITEABLE', new DefaultUserException($msg));
    return false;
    }
    // If this is the first time initializing the master site then we need to do this:
    // Copy the master site existing config file to the data/var directory (for use later and backup)
    // First make the directory tree starting with a 'master' directory and 'var' subdirectory
    //Only do this if this is the first run and setting the Master Site
	$lIsMultisites = xarConfigGetVar('System.MS.MultiSites');
	$lIsMaster=xarConfigGetVar('System.MS.Master');
	$masterurl = $_SERVER[$servervar];
	//set the masterurl
	xarModSetVar('multisites','masterurl',$masterurl);
	//Get the 'cleaned' name for site directory creation
    $sitedir = xarModAPIFunc('multisites','admin','cleandn', array('sitedn' => $masterurl));
        if (!$sitedir) {
            $msg = xarML("Could not clean ".$mssite);
            xarExceptionSet(XAR_USER_EXCEPTION, 'ERROR-CLEANDN', new DefaultUserException($msg));
            return $msg;
        }
    //$sitedir=$sitedir['sitedn']; //TODO: later when other changes complete
    $sitedir='master';

    if (($lIsMultisites==1) and ($lIsMaster==0)) { // first time run through
        $var = is_dir ($cWhereIsPerso.'/'.$sitedir);
        if ($var == false) {
            $oldumask = umask(0);
            if (!mkdir($cWhereIsPerso.'/'.$sitedir,0777)) {
                $msg = xarML('The Site Data Directory '.$cWhereIsPerso.'/'.$sitedir.'/var is not writeable!');
                xarExceptionSet(XAR_USER_EXCEPTION, 'FILE_NON-WRITEABLE', new DefaultUserException($msg));
                return false;
            }
            umask($oldumask);
        }
        $var = is_dir ($cWhereIsPerso.'/'.$sitedir.'/var');
        if ($var == false) {
            $oldumask = umask(0);
            if (!mkdir($cWhereIsPerso.'/'.$sitedir.'/var',0777)) {
                $msg = xarML('The Site Data Directory '.$cWhereIsPerso.'/'.$sitedir.'/var is not writeable!');
                xarExceptionSet(XAR_USER_EXCEPTION, 'FILE_NON-WRITEABLE', new DefaultUserException($msg));
                return false;
            }
            umask($oldumask);
        }
        // copy the master config.system.php file to the new master/var directory
        // only do this the FIRST time thru else the file will hold master config data
        $filenamein = "var/config.system.php";
        $filenameout=$cWhereIsPerso.'/'.$sitedir.'/var/config.system.php';
        if (!copy($filenamein,$filenameout)) {
            $msg = xarML('Unable to copy master config to '.$cWhereIsPerso.'/'.$sitedir.'/var');
            xarExceptionSet(XAR_USER_EXCEPTION, 'CANNOT COPY FILE', new DefaultUserException($msg));
            return false;
        }
     }
    //Update the master config.system.php file for multisite functioning.
    //Read in existing old config file content into the array, strip out existing multisite changes
    //Don't like this much - assumes that the str in filter below will never exist in config.system.php
    //except if a multisite setup
    umask();
    $oldConfig=file('./var/config.system.php');
    $fd = fopen('./var/config.system.php','rb');
    while (list ($line_num, $line) = each ($oldConfig)) {
        if ((strstr($line,"<?php")) ||
            (strstr($line,"?>")) ||
            (strstr($line,"GLOBALS")) ||
            (strstr($line,"file_exists")) ||
            (strstr($line,"else")) ||
            (strstr($line,"}")) ||
            (strstr($line,"siteConfiguration['DB.TablePrefix']")) ||
            (strstr($line,"Multisites")) ||
            (strstr($line,"'MS."))) {
           // do nothing
        }else{
          $holdConfig[$line_num]=$oldConfig[$line_num];
        }
     }
    fclose($fd);
     $siteprefix = xarDBGetSiteTablePrefix();
     //Get all the subdomain and domain extensions required for this site
     //Put in an array til we need them
     $ext_array = explode(',',xarModGetVar('multisites','DNexts'));
     usort($ext_array,'lengthcompare');
     //Create the new config data for the master site
     $newConf=file('./var/config.system.php');
     $oldumask = umask(0);

     $IOk=fopen('./var/config.system.php','wb');
     if ($IOk) {
        fwrite($IOk, "<?php\n");
        fwrite($IOk, "\$GLOBALS['myhostName'] = \$_SERVER['".$servervar."'];\n");
        fwrite($IOk, "\$GLOBALS['myhostName'] = str_replace('www.','',\$GLOBALS['myhostName']);\n");
        foreach ($ext_array as $key => $ext) {
           if ($ext!='') {
                fwrite($IOk, "\$GLOBALS['myhostName'] = str_replace('".$ext."','',\$GLOBALS['myhostName']);\n");
           } 
        }
        fwrite($IOk, "if (file_exists('".$cWhereIsPerso."/'.\$GLOBALS['myhostName'].'/var/config.system.php')) {\n");
        fwrite($IOk, "include_once ('".$cWhereIsPerso."/'.\$GLOBALS['myhostName'].'/var/config.system.php');\n");
        fwrite($IOk, "} else {\n");
        while (list ($line_num, $line) = each($holdConfig)) {
            fwrite($IOk,$line);
        }
        fwrite($IOk,"// Multisites: Set site prefix (same as system).\n");
        fwrite($IOk,"\$siteConfiguration['DB.TablePrefix'] = '".xarDBGetSiteTablePrefix()."';\n");
        fwrite($IOk,"// Multisites: Set this as the Multisite Master.\n");
        fwrite($IOk,"\$systemConfiguration['MS.Master'] = '1';\n");
        fwrite($IOk,"// Multisites: Set multisite flag on.\n");
        fwrite($IOk,"\$systemConfiguration['MS.MultiSites'] = '1';\n");
        fwrite($IOk,"}\n");
        fwrite($IOk, "?>\n");
        fclose($IOk);
     } else {
        //echo "Can't modify the old config file";
        return false;
     }
     umask($oldumask);
     //If this is the first run and setting the Master Site
     //Set the System config to flag this as the Master Site Configured
     //Update the site database with the Master(?)
     if (($lIsMultisites==1) and ($lIsMaster==0)) {
         xarConfigSetVar('System.MS.Master',1);
         $dbconn =& xarDBGetConn();
       // Call Multisites API function is called
        $msid = xarModAPIFunc('multisites',
                              'admin',
                              'create',
                        array('mssite'     => $masterurl,
                              'msprefix'   => xarDBGetSiteTablePrefix(),
                              'msdb'       => xarDBGetName(),
                              'msshare'    => xarDBGetSystemTablePrefix(),
                              'msstatus'   => 1,
                              'sitefolder' => 'var'));

        if (!$msid) return;

     }

return true;
}
function lengthcompare ($a, $b) {
    if (strlen($a) > strlen($b)) return 0;
    return ($a > $b) ? -1 : 1;
}
?>
