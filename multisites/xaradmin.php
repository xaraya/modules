<?php
// File: $Id: s.xaradmin.php 1.19 02/12/22 12:40:04-05:00 John.Cox@mcnabb. $
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Sebastien Bernard
// Purpose of file:  Administration of the multisites system.
// based on the templates developped by Jim McDee.
// ----------------------------------------------------------------------


// all my functions to copy files / folders
global $COMSPEC;
if (!(empty($COMSPEC))) {
	// windows
	define("_FOLDER_SEPARATOR","\\");
	}
else {
	define("_FOLDER_SEPARATOR","/");
	}	

/**
 * The standard overview screen on entry to the Multisite module.
 * @ returns output
 * @return output with Multisite Overview and Menu information
 */
function multisites_admin_main()
{
    // Security check
    if (!xarSecurityCheck('AdminMultisites')) {
        return;
    }
   // we only really need to show the default view (overview in this case)
 	if (xarModGetVar('adminpanels', 'overview') == 0) {
		// Return the output
		return array();
	} else {
		xarResponseRedirect(xarModURL('multisites', 'admin', 'modifyconfig'));
	}
   // success
    return true;
}

function multisites_admin_modifyconfig()
{
    global $HTTP_SERVER_VARS;

    // Security check
    if (!xarSecurityCheck('AdminMultisites')) {
           return;
    }

	$lIsMultisites = xarConfigGetVar('System.MS.MultiSites');
	$lIsMaster=xarConfigGetVar('System.MS.Master');
    $masterurl=xarModGetVar('multisites','masterurl');
    $servervar=xarModGetVar('multisites','servervar');
    $currenthost=$_SERVER[$servervar];
 	if (($lIsMultisites==1) and ($lIsMaster==1) and ($currenthost==$masterurl)){
    // The master multisite has been configured and this is the master - continue

        $data['modifysite']=1;

        //Submit button
        $data['btnSetConfig'] = xarML('Change Multisite Configuration');

	} else {
        $data['modifysite']=0;
      //Submit button
        $data['btnSetConfig'] = xarML('Set Multisite Configuration');
    }
        $data['authid'] = xarSecGenAuthKey();

        $data['SERVER_NAME'] = $HTTP_SERVER_VARS['SERVER_NAME'];
        $data['HTTP_HOST']   = $HTTP_SERVER_VARS['HTTP_HOST'];
        $data['masterfolder']  = xarModGetVar('multisites','masterfolder');
        $data['DNexts']      = xarModGetVar('multisites','DNexts');

    // Return the template variables defined in this function
    return $data;
}

function multisites_admin_updateconfig($args)
{
	global $HTTP_SERVER_VARS;

    extract($args);

    if (!xarVarFetch('servervar', 'str:9:', $servervar, 'HTTP_HOST')) return;
    if (!xarVarFetch('themepath', 'str:2:', $themepath, 'themes')) return;
    if (!xarVarFetch('varpath', 'str:2:', $varpath, 'var')) return;
    if (!xarVarFetch('masterfolder', 'str:4:', $masterfolder, 'xarsites')) return;
    if (!xarVarFetch('DNexts', 'str', $DNexts, '.com,.org,.net')) return;
 
    // Auth Key
    if (!xarSecConfirmAuthKey()) return;

    // Security
    if (!xarSecurityCheck('AdminMultisites')) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // If Master created, we don't want to set the system Master and $masterurl again
    // Just update mod vars
    xarModSetVar('multisites', 'servervar', $servervar);
    // Hmmm, needs more thought.
    //<jojodee> setting anyway for now, but don't think we want this $themepath and $varpath to be changeable
    xarModSetVar('multisites', 'themepath', $themepath);
    xarModSetVar('multisites', 'varpath', $varpath);
    xarModSetVar('multisites', 'masterfolder', $masterfolder);
    xarModSetVar('multisites', 'DNexts', $DNexts);

    $setconfig = xarModFunc('multisites',
                            'admin',
                            'setconfig',
                            array('masterfolder' => $masterfolder,
                                  'servervar'  => $servervar,
                                  'DNexts'     => $DNexts));

    if (!$setconfig) {
        $msg = xarML('Unable to configure Master Multisite');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
       return false;
    }

   xarResponseRedirect(xarModURL('multisites', 'admin', 'view'));

    return true;
}


function multisites_admin_adminconfig()
{
      // Security check
    if (!xarSecurityCheck('AdminMultisites')) {
           return;
    }
   $data['authid'] = xarSecGenAuthKey();
     //Submit button
    $data['btnUpdateAdmin'] = xarML('Update Admin Configuration');

    // Return the template variables defined in this function
    return $data;

}


function multisites_admin_updateadminconfig()
{
    if (!xarVarFetch('itemsperpage', 'int:1:', $itemsperpage, '1')) return;    


    // Auth Key
    if (!xarSecConfirmAuthKey()) return;

    // Security
    if (!xarSecurityCheck('AdminMultisites')) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

     if (!isset($itemsperpage)) {
        $itemsperpage=10;
    }

    xarModSetVar('multisites', 'itemsperpage', $itemsperpage);


    xarResponseRedirect(xarModURL('multisites', 'admin', 'adminconfig'));

    return true;
}

/**
 * Standard function to list and view existing subsites
 * Options to inactivate, delete and edit subsites
 * Multisite configuration in _admin_modifyconfig() must be set to continue here
 */
function multisites_admin_view()
{
global $HTTP_SERVER_VARS;

    // Get parameters from whatever input we need
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;

    // Set up an array for item data
    $data['items'] = array();

    // Check if the Master site has been set up
	$lIsMultisites = xarConfigGetVar('System.MS.MultiSites');
	$lIsMaster=xarConfigGetVar('System.MS.Master');
    $masterurl=xarModGetVar('multisites','masterurl');
    $servervar=xarModGetVar('multisites','servervar');
    $currenthost=$_SERVER[$servervar];
 	if (($lIsMultisites==1) and ($lIsMaster==1) and ($currenthost==$masterurl)){
    // The master multisite has been configured and this is the master - continue
        $data['mastersite']= true;
        $data['authid'] = xarSecGenAuthKey();

        // Call the xarTPL helper function to produce a pager in case of there
        // being many items to display.
        $data['pager'] = xarTplGetPager($startnum,
              xarModAPIFunc('multisites', 'user', 'countitems'),
              xarModURL('multisites', 'admin', 'view', array('startnum' => '%%')),
              xarModGetVar('multisites', 'itemsperpage'));

        // Security Check
        if(!xarSecurityCheck('AdminMultisites')) return;

        // Labels for display
        $data['sitelabel']    = xarVarPrepForDisplay(xarML('Site Name'));
        $data['prefixlabel']  = xarVarPrepForDisplay(xarML('Table Prefix'));
        $data['dblabel']      = xarVarPrepForDisplay(xarML('Database'));
        $data['sharelabel']   = xarVarPrepForDisplay(xarML('Shared'));
        $data['statuslabel']  = xarVarPrepForDisplay(xarML('Status'));
        $data['optionslabel'] = xarVarPrepForDisplay(xarML('Options'));

       // The user API function is called
       $sites = xarModAPIFunc('multisites',
                              'user',
                              'getall',
                        array('startnum' => $startnum,
                             'numitems' => xarModGetVar('multisites',
                                                       'itemsperpage')));
       $data['siteno']=count($sites);
        if (empty($sites)) {
           $msg = xarML('No sites in database.', 'multisites');
           xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
           return;
       }

       // Check individual permissions for Edit/Delete/Enable
       $authid = xarSecGenAuthKey();

       for ($i = 0; $i < count($sites); $i++) {
           $site = $sites[$i];
         if (xarSecurityCheck('EditMultisites', 0, 'Item', "$site[mssite]:All:$site[msid]")) {
               $sites[$i]['enableurl'] = xarModURL('multisites',
                   'admin',
                   'changestatus',
                   array('msid' => $site['msid']));
           } else {
               $sites[$i]['enableurl'] = '';
           }
           $sites[$i]['enablelabel'] = xarML('Change Status');
           if (xarSecurityCheck('EditMultisites', 0, 'Site', "$site[mssite]:All:$site[msid]")) {
               $sites[$i]['editurl'] = xarModURL('multisites',
                   'admin',
                   'modify',
                   array('msid' => $site['msid']));
           } else {
               $sites[$i]['editurl'] = '';
           }
           $sites[$i]['editlabel'] = xarML('Configure');
           if (xarSecurityCheck('DeleteMultisites', 0, 'site', "$site[mssite]:All:$site[msid]")) {
            $sites[$i]['deleteurl'] = xarModURL('multisites',
                                                    'admin',
                                                   'delete',
                                             array('msid' => $site['msid']));
           } else {
            $sites[$i]['deleteurl'] = '';
           }
           $sites[$i]['deletelabel'] = xarML('Delete');
        }
           // Add the array of items to the template variables

          $data['items'] = $sites;
          $data['masterurl'] =$masterurl;

    } else {  // The master site has not been configured, or this is not the master
      $data['mastersite']= false;
      $data['infomsg']=xarML('The Master Multisite has not yet been configured! <br /><br />
                              Please configure the Master Site from the menu option Multisites - Master Config.
                              <br /><br />You can then add new sites through the menu option Multisites - Add Sites.
                              <br /><br />Return here to View Sites once your have added sites to view!');

    }

    // Return the template variables defined in this function
    return $data;
}

/**
 * add a new site (domain or subdomain)
 */
function multisites_admin_addsite()
{
   global $HTTP_SERVER_VARS;
  // Security check
    if (!xarSecurityCheck('AdminMultisites')) {
        return;
    }
      // Check if the Master site has been set up
	$lIsMultisites = xarConfigGetVar('System.MS.MultiSites');
	$lIsMaster=xarConfigGetVar('System.MS.Master');
    $masterurl=xarModGetVar('multisites','masterurl');
    $servervar=xarModGetVar('multisites','servervar');
    $currenthost=$_SERVER[$servervar];

 	if (($lIsMultisites==1) and ($lIsMaster==1) and ($currenthost==$masterurl)){
    
       // This is the master, and Master site has been set up
       $data['items'] = array();
       $data['authid']     = xarSecGenAuthKey();
       $data['mastersite'] = true;
       $data['siteDN']     = '';
       $data['sysPrefix']  = xarDBGetSystemTablePrefix();
       $data['msPrefix']   = '';
       $data['siteDB']     = xarDBGetName(); // database to be used, defaults to Master
       $data['createdb']   = false;

        // Select item values (site status).
       // Default is a 'pending' state. So no need to display when creating the new site
       $data['siteStatus'] = array(
                 'Pending'  => xarML('Pending'),
                 'Active'   => xarML('Active'),
                 'Inactive' => xarML('Inactive'));

      //TO DO: maybe add some extras later when I think of what
       $data['sharedTables']= xarDBGetSystemTablePrefix(); // prefix sharing, defaults to Master

       //Submit button
       $data['btnAddSite'] = xarML('Create New Site');
   } else {  //this is not the Master, or Master site not configured
      $data['mastersite']= false;
      $data['infomsg']=xarML('Multisites must be configured first before you can Add a site! <br /><br />
                              Please configure the master site from the menu option Multisites - Master Config.
                              <br /><br />The Master site can then return here to Add Sites.');
   }
    // Return the template variables defined in this function
    return $data;
}

/**
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('multisites','admin','add') to create a new site
  */
function multisites_admin_createsite($args)
{
global $HTTP_SERVER_VARS;

    extract($args);

    if (!xarVarFetch('siteDN', 'str:2:', $siteDN)) {
                 $msg = xarML('Please enter a name for the new site');
            xarExceptionSet(XAR_USER_EXCEPTION, 'DATA_MISSING', new DefaultUserException($msg));
            return $msg;
    }
    if (!xarVarFetch('msPrefix', 'str:2:', $msPrefix)) return;
    if (!xarVarFetch('siteDB', 'str:2:', $siteDB)) return;
    if (!xarVarFetch('sharedTables', 'str:2:', $sharedTables, $siteDB )) return;
    if (!xarVarFetch('siteStatus', 'int:1:', $siteStatus,'0')) return;
    if (!xarVarFetch('createdb', 'int:1:', $createdb, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $btnAddSite,'', XARVAR_NOT_REQUIRED)) return; 

  
    if (!empty($objectid)) {
        $exid = $objectid;
    } 

   // Auth Key
    if (!xarSecConfirmAuthKey()) return;

    // Security
    if (!xarSecurityCheck('AdminMultisites')) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    $sitedir = multisites_admin_cleanDN($siteDN);

    if (!$sitedir) {
            $msg = xarML("Could not clean ".$siteDN);
            xarExceptionSet(XAR_USER_EXCEPTION, 'ERROR-CLEANDN', new DefaultUserException($msg));
            return $msg;
    
    } 

   //Check the prefix does not already exist
   $prefixexists = xarModAPIFunc('multisites',
                                     'user',
                                     'getall',
                               array('where'   =>
                                   'WHERE xar_msprefix = "'.$msPrefix.'" AND xar_msdb = "'.$siteDB.'"'));

   if ($prefixexists) {
            $msg = xarML("Sorry, a subsite already exists in database '".$siteDB."'\nwith database prefix '".$msPrefix."'\nPlease use a different table prefix.");
            xarExceptionSet(XAR_USER_EXCEPTION, 'ALREADY_EXISTS', new DefaultUserException($msg));
            return $msg;
    }

    $cWhereIsPerso =xarModGetVar('multisites','masterfolder');

    //Create new masterfolder data tree
    $var = is_dir($cWhereIsPerso."/".$sitedir);
    if ($var == true) { // the folder and perhaps site already exists
        $msg = xarML("The subsite ".$siteDN." already exists!\nRemove this subsite and recreate, or edit the exising subsite.");
            xarExceptionSet(XAR_USER_EXCEPTION, 'EXISTING_DIRECTORY', new DefaultUserException($msg));
            return $msg;
    } else {
         $oldumask = umask(0);
         if (!mkdir($cWhereIsPerso."/".$sitedir,0755)) {
            $msg = xarML("The subsite directory ".$cWhereIsPerso."/".$sitedir." was not created!");
            xarExceptionSet(XAR_USER_EXCEPTION, 'FILE_NOT_CREATED', new DefaultUserException($msg));
            return $msg;
         }

         umask($oldumask);
    }
    $var = is_dir($cWhereIsPerso."/".$sitedir."/var");
    if ($var == false) {
        $oldumask = umask(0);
        if (!mkdir($cWhereIsPerso."/".$sitedir."/var",0755)) {
            $msg = xarML("The subsite var directory ".$cWhereIsPerso."/".$sitedir."/var was not created!");
            xarExceptionSet(XAR_USER_EXCEPTION, 'FILE_NOT_CREATED', new DefaultUserException($msg));
            return $msg;
        }
        umask($oldumask);
    }

    // copy the master config.system.php file to the new master/var directory
    $filenamein = $cWhereIsPerso."/master/var/config.system.php";
    $filenameout= $cWhereIsPerso."/".$sitedir."/var/config.system.php";
    if (!copy($filenamein,$filenameout)) {
        $msg = xarML("Unable to copy master config to ".$cWhereIsPerso."/".$sitedir."/var");
        xarExceptionSet(XAR_USER_EXCEPTION, 'CANNOT COPY FILE', new DefaultUserException($msg));
        return $msg;
    }
    // update the new subsite config file
    $configfile =getcwd().'/'.$cWhereIsPerso."/".$sitedir."/var/config.system.php";
    if (!(empty($COMSPEC))) {
        $configfile = str_replace("/","\\",$configfile); //windows
    } else {
        $configfile = str_replace("\\","/",$configfile);
    }
       
    $newConfig = file($configfile);
    $fd = fopen($configfile, 'w');
    while (list ($line_num, $line) = each ($newConfig)) {
        if (strstr($line,"\$systemConfiguration['DB.Name']")) {
             $newConfig[$line_num]="\$systemConfiguration['DB.Name']='".$siteDB."';";
             $line                ="\$systemConfiguration['DB.Name']='".$siteDB."';";
        }
        if (strstr($line,"\$systemConfiguration['DB.TablePrefix']")) {
             $newConfig[$line_num]="\$systemConfiguration['DB.TablePrefix']='".$msPrefix."';";
             $line                ="\$systemConfiguration['DB.TablePrefix']='".$msPrefix."';";
        }
        if (strstr($line,"?>")) {
             $newConfig[$line_num]="// Multisites: Set this Multisite SubSite Active.\n\$systemConfiguration['MS.Active'] = '1';\n\n?>";
               $line                ="// Multisites: Set this Multisite SubSite Active.\n\$systemConfiguration['MS.Active'] = '1';\n\n?>";
        }
        fwrite ($fd, trim($line)."\n");
    }
    fclose ($fd);
       
    //Update new site to the Master multisite table
    $msid = xarModAPIFunc('multisites',
                              'admin',
                              'create',
                        array('mssite'   => $siteDN,
                              'msprefix' => $msPrefix,
                              'msdb'     => $siteDB,
                              'msshare'  => $sharedTables,
                              'msstatus' => $siteStatus));

    if (!$msid) return;

    // TO DO - set the subsite config vars
    
    // TO DO - set subsite mod vars

    // TO DO - option to create database, create tables


   xarResponseRedirect(xarModURL('multisites', 'admin', 'view'));
   // success
   return true;
}


function multisites_admin_delete($args)
{
    extract($args);
    if (!xarVarFetch('msid', 'int:2:', $msid)) return;

    // Security check
    if (!xarSecurityCheck('AdminMultisites')) {
        return;
    }
      // Check if the Master site has been set up
	$lIsMultisites = xarConfigGetVar('System.MS.MultiSites');
	$lIsMaster=xarConfigGetVar('System.MS.Master');
    $masterurl=xarModGetVar('multisites','masterurl');
    $servervar=xarModGetVar('multisites','servervar');
    $currenthost=$_SERVER[$servervar];

 	if (($lIsMultisites==1) and ($lIsMaster==1) and ($currenthost==$masterurl)){
       // This is the master.

       $data['authid']     = xarSecGenAuthKey();
       $data['mastersite'] = true;
//       $data['msid']     = $msid;

       $subsite =xarModAPIFunc('multisites','user','get',
                             array('msid' => $msid));

       if (!$subsite) {
            $msg = xarML("Cannot delete subsite '".$siteDB);
            xarExceptionSet(XAR_USER_EXCEPTION, 'NO_DATA_RECORD', new DefaultUserException($msg));
            return $msg;
       }

       $data['msid']          = $subsite['msid'];
       $data['mssite']        = $subsite['mssite'];
       $data['msprefix']      = $subsite['msprefix'];
       $data['msdb']          = $subsite['msdb'];
       $data['msshare']       = $subsite['msshare'];
       $data['btnDeleteSite'] = xarML('Delete Site');
       $data['removetables']  = 1;
       $data['removedatadir'] = 1;

   } else {  //this is not the Master, or Master site not configured

      $data['mastersite']= false;
      $data['infomsg']=xarML('Sorry, no authority for Deleting subsites.');
   }
    // Return the template variables defined in this function
    return $data;
}


function multisites_admin_removesite($args)
{
    extract($args);
    if (!xarVarFetch('msid', 'int:2:', $msid)) return;
    if (!xarVarFetch('mssite', 'str:2:', $mssite)) return;
    if (!xarVarFetch('msprefix', 'str:2:', $msprefix)) return;
    if (!xarVarFetch('msdb', 'str:5:', $msdb)) return;
    if (!xarVarFetch('removetables', 'int:0:', $removetables,'0')) return;
    if (!xarVarFetch('removedatadir', 'int:0:', $removedatadir,'0')) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $btnDeleteSite,'', XARVAR_NOT_REQUIRED)) return; 
  
    if (!empty($objectid)) {
        $exid = $objectid;
    } 

   // Auth Key
    if (!xarSecConfirmAuthKey()) return;

    // Security
    if (!xarSecurityCheck('DeleteMultisites')) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }
    // detele the site from the multisite table
    $site = xarModAPIFunc('multisites','admin','delete',
                                 array('msid' => $msid));

       if (!$site) {
            $msg = xarML("Cannot delete subsite '".$msid);
            xarExceptionSet(XAR_USER_EXCEPTION, 'NO_DATA_RECORD', new DefaultUserException($msg));
            return $msg;
       }

    if ($removedatadir==1) {
        // remove the site specific data directory tree and files
        // Get site folder name
        $sitedir = multisites_admin_cleanDN($mssite);
        if (!$sitedir) {
            $msg = xarML("Could not clean ".$siteDN);
            xarExceptionSet(XAR_USER_EXCEPTION, 'ERROR-CLEANDN', new DefaultUserException($msg));
            return $msg;
        }
        $cWhereIsPerso =xarModGetVar('multisites','masterfolder');
        $var = is_dir($cWhereIsPerso."/".$sitedir);
        if ($var) {
            chmod($cWhereIsPerso."/".$sitedir,0755);
            if (!is_writable($cWhereIsPerso."/".$sitedir)) {
                $msg = xarML("The subsite directory ".$cWhereIsPerso."/".$sitedir." could not be deleted!");
                xarExceptionSet(XAR_USER_EXCEPTION, 'FILE_NOT_WRITEABLE', new DefaultUserException($msg));
                    return $msg;
            } else {
                multisites_admin_recdeldir($cWhereIsPerso."/".$sitedir."/");
            }
        }
     }
    // remove the site specific tables from the database
    if ($removetables==1) {

    //TO DO

    }
   // success
  xarResponseRedirect(xarModURL('multisites', 'admin', 'view'));

return true;
}


/**
 * This is a function that is called with the results of the
 * form supplied by xarModFunc('multisites','admin','updateconfig') to create a new Master Site
 * This function is run once to setup the Master site, and then to modify settings if required
 */
function multisites_admin_setconfig($args)
{
global $HTTP_SERVER_VARS;

   extract($args);

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
    if ($var == true) {
          // echo "The Site Data directory exisits";
    } else {
            $msg = xarML("The Site Data Directory ".$cWhereIsPerso." does not exist!\n Please create it and chmod 777.");
            xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DIRECTORY', new DefaultUserException($msg));
    return false;
    }

    //Check the site data folder is writable
    $var = is_writeable($cWhereIsPerso);
    if ($var == true) {
          // echo "The directory is writable";
    } else {
            $msg = xarML("The Site Data Directory ".$cWhereIsPerso." is not writeable!\n
                          Please chmod 777 and try again.");
            xarExceptionSet(XAR_USER_EXCEPTION, 'FILE_NON-WRITEABLE', new DefaultUserException($msg));
    return false;
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
   // Set and get some vars here
	$lIsMultisites = xarConfigGetVar('System.MS.MultiSites');
	$lIsMaster=xarConfigGetVar('System.MS.Master');
	$currenthttp = $_SERVER[$servervar];
	$masterurl = xarConfigGetVar('multisites','masterurl');
    if (($lIsMultisites==1) and ($lIsMaster==0)) {
        $var = is_dir ($cWhereIsPerso."/master");
        if ($var == false) {
            $oldumask = umask(0);
            if (!mkdir($cWhereIsPerso."/master",0777)) {
                $msg = xarML("The Site Data Directory ".$cWhereIsPerso."/master is not writeable!");
                xarExceptionSet(XAR_USER_EXCEPTION, 'FILE_NON-WRITEABLE', new DefaultUserException($msg));
                return false;
            }
            umask($oldumask);
        }
        $var = is_dir ($cWhereIsPerso."/master/var");
        if ($var == false) {
            $oldumask = umask(0);
            if (!mkdir($cWhereIsPerso."/master/var",0777)) {
                $msg = xarML("The Site Data Directory ".$cWhereIsPerso."/master/var is not writeable!");
                xarExceptionSet(XAR_USER_EXCEPTION, 'FILE_NON-WRITEABLE', new DefaultUserException($msg));
                return false;
            }
            umask($oldumask);
        }
        // copy the master config.system.php file to the new master/var directory
        $filenamein = "var/config.system.php";
        $filenameout=$cWhereIsPerso."/master/var/config.system.php";
        if (!copy($filenamein,$filenameout)) {
            $msg = xarML("Unable to copy master config to ".$cWhereIsPerso."/master/var.");
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
    $fd = fopen('./var/config.system.php','r');
    while (list ($line_num, $line) = each ($oldConfig)) {
        if ((strstr($line,"<?php")) ||
            (strstr($line,"?>")) ||
            (strstr($line,"GLOBALS")) ||
            (strstr($line,"file_exists")) ||
            (strstr($line,"else")) ||
            (strstr($line,"}")) ||
            (strstr($line,"Multisites")) ||
            (strstr($line,"'MS."))) {
           // do nothing
        }else{
          $holdConfig[$line_num]=$oldConfig[$line_num];
        }
     }
    fclose($fd);

     //Get all the subdomain and domain extensions required for this site
     //Put in an array til we need them
     $ext_array = explode(',',xarModGetVar('multisites','DNexts'));
     usort($ext_array,"multisites_admin_lengthcmp");
     //Create the new config data for the master site
     $newConf=file('./var/config.system.php');
     $oldumask = umask(0);

     $IOk=fopen('./var/config.system.php','w');
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
         $masterurl=$_SERVER[$servervar];
         xarModSetVar('multisites', 'masterurl',$masterurl);
         xarConfigSetVar('System.MS.Master',1);
         list($dbconn) = xarDBGetConn();
       // Call Multisites API function is called
        $msid = xarModAPIFunc('multisites',
                              'admin',
                              'create',
                        array('mssite'   => $masterurl,
                              'msprefix' => xarDBGetSystemTablePrefix(),
                              'msdb'     => xarDBGetName(),
                              'msshare'  => 'Master',
                              'msstatus' => 1));

        if (!$msid) return;

     }

return true;
}


function multisites_admin_mainload()
{
global $HTTP_HOST,$SERVER_NAME;

    $output = new pnHTML();
    if (!xarSecurityCheck('AdminMultisites')) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Start the table that holds the information to be modified.  Note how
    // each item in the form is kept logically separate in the code; this helps
    // to see which part of the code is responsible for the display of each
    // item, and helps with future modifications

	// is the multisites already created ?

    $output->SetInputMode(_PNH_VERBATIMINPUT);
	$master = xarConfigGetVar('System.MS.Master');

	if (	empty($master) )  {
		// it means this form has never been used and saved, so this server
		// will be the master.
		// the others servers will not pass here.
		// xarconfig[master] is declared in the new config.php, created in initconfig.
	    $output->FormStart(xarModURL('multisites', 'admin', 'initconfig'));

	    $output->FormHidden('authid', xarSecGenAuthKey());

		// explain what is going to happen
	    $output->TableStart(_MULTISITES_CREATION);

    	$output->TableRowStart('left', 'top');
    	$output->TableColStart( 2, 'left', 'top');
    	$output->Text(_MULTISITES_EXPLAIN);
		$output->TableColEnd();
		$output->TableRowEnd();

    	$output->TableRowStart('left', 'top');
    	$output->TableColStart( 2, 'left', 'top');
    	$output->Text(_MULTISITES_SERVERNAME);
		$output->TableColEnd();
		$output->TableRowEnd();

		global $SERVER_NAME,$HTTP_HOST;		
	    $row = array();
	    $output->SetInputMode(_PNH_VERBATIMINPUT);
	    $output->SetOutputMode(_PNH_RETURNOUTPUT);
	    $row[] = $output->Text("\$SERVER_NAME= ".$SERVER_NAME);
	    $row[] = $output->FormCheckbox('server_name', false, 'SERVER_NAME','radio' );
	    $output->SetOutputMode(_PNH_KEEPOUTPUT);
	    $output->SetInputMode(_PNH_VERBATIMINPUT);
	    $output->TableAddrow($row, 'left');

	    $row = array();
	    $output->SetOutputMode(_PNH_RETURNOUTPUT);
	    $row[] = $output->Text("\$HTTP_HOST= ".$HTTP_HOST);
	    $row[] = $output->FormCheckbox('server_name', true, 'HTTP_HOST', 'radio' );
	    $output->SetOutputMode(_PNH_KEEPOUTPUT);
	    $output->SetInputMode(_PNH_VERBATIMINPUT);
	    $output->TableAddrow($row, 'left');

    	$output->TableRowStart('left', 'top');
    	$output->TableColStart( 2, 'left', 'top');
    	$output->Text(_MULTISITES_WHEREIS);
		$output->TableColEnd();
		$output->TableRowEnd();

	    $row = array();
	    $output->SetOutputMode(_PNH_RETURNOUTPUT);
	    $row[] = $output->Text(xarVarPrepForDisplay(_MULTISITESWHEREIS));
	    $row[] = $output->FormText('cWhereIsPerso', 'msdata/', 40, 40);
	    $output->SetOutputMode(_PNH_KEEPOUTPUT);
	    $output->SetInputMode(_PNH_VERBATIMINPUT);
	    $output->TableAddrow($row, 'left');

    	$output->TableRowStart('left', 'top');
    	$output->TableColStart( 2, 'left', 'top');
// I dont understand why that function cleanDN does not work here ...		
    	$output->Text( 	
				_MULTISITES_SUITE1 .
				cleanDN($HTTP_HOST) . 
				_MULTISITES_SUITE2 . 
				cleanDN($HTTP_HOST) . 
				_MULTISITES_SUITE3
			);
			
		$output->TableColEnd();
		$output->TableRowEnd();
		
	    $output->SetInputMode(_PNH_PARSEINPUT);
	    $output->Linebreak(2);
		
		}	
	elseif ( 	(xarConfigGetVar('multiSites') == 1) 
		and 	(xarModGetVar('multisites','master')) 
		and		($HTTP_HOST == xarConfigGetVar('masterURL') or $SERVER_NAME == xarConfigGetVar('masterURL') ) 
		)
	
		{
		// this is the master (already prepared), coming again.
		// he may so to create other servers (domains).
		// he is the only one able to create new servers.
	    $output->FormStart(xarModURL('multisites', 'admin', 'createsub'));
	    $output->FormHidden('authid', xarSecGenAuthKey());

	    $output->SetInputMode(_PNH_VERBATIMINPUT);
		
		// explain what is going to happen
	    $output->TableStart();

    	$output->TableRowStart('left', 'top');
    	$output->TableColStart( 2, 'left', 'top');
    	$output->Text(_MULTISITES_MASTER_EXPLAIN);
		$output->TableColEnd();
		$output->TableRowEnd();

	    $row = array();
	    $output->SetOutputMode(_PNH_RETURNOUTPUT);
	    $row[] = $output->Text(_MULTISITES_DN_EXPLAIN);
	    $row[] = $output->FormText('domaine_a_creer', '', 40, 40);
	    $output->SetOutputMode(_PNH_KEEPOUTPUT);
	    $output->SetInputMode(_PNH_VERBATIMINPUT);
	    $output->TableAddrow($row, 'left');

	    $row = array();
	    $output->SetOutputMode(_PNH_RETURNOUTPUT);
	    $row[] = $output->Text(_MULTISITES_DATABASE_EXPLAIN);
	    $row[] = $output->FormText('database_utilisee', '', 40, 40);
	    $output->SetOutputMode(_PNH_KEEPOUTPUT);
	    $output->SetInputMode(_PNH_VERBATIMINPUT);
	    $output->TableAddrow($row, 'left');
		
	    $row = array();
	    $output->SetOutputMode(_PNH_RETURNOUTPUT);
	    $row[] = $output->Text(_MULTISITES_PREFIX_EXPLAIN);
	    $row[] = $output->FormText('prefix_utilise', 'nuke', 40, 40);
	    $output->SetOutputMode(_PNH_KEEPOUTPUT);
	    $output->SetInputMode(_PNH_VERBATIMINPUT);
	    $output->TableAddrow($row, 'left');
		}

	else {
		// right now, a sub site does not have to do anything here.
		// in a normal subsite, with different database, he should not even see the multisites option on the admin menu. With common tables, it may arrive. So, a message.
		
	    $output->FormStart(xarModURL('multisites', 'admin', 'modifconfig'));
	    $output->FormHidden('authid', xarSecGenAuthKey());
		
    	$output->TableRowStart('left', 'top');
    	$output->TableColStart( 2, 'left', 'top');
    	$output->Text(xarVarPrepForDisplay(_MULTISITES_SORRY));
		$output->TableColEnd();
		$output->TableRowEnd();
		}

    $output->SetInputMode(_PNH_PARSEINPUT);
    $output->Linebreak(2);
    $output->TableEnd();

    // End form
    $output->Linebreak(2);
    $output->FormSubmit(_MULTISITES_UPDATE);
    $output->FormEnd();
    // Return the output that has been generated by this function
    return $output->GetOutput();
}

function multisites_admin_createsub() {
	$lNext = false;
    if (!xarSecurityCheck('AdminMultisites')) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
	    }
	$domaine_a_creer 	= xarVarCleanFromInput('domaine_a_creer');
	$database_utilisee 	= xarVarCleanFromInput('database_utilisee');
	$prefix_utilise 	= xarVarCleanFromInput('prefix_utilise');

	// first, copy the master folder.
	$weAreIn 			= getcwd();
// pour test	
 	copy_all( getcwd().'/'.xarModGetVar('multisites','masterfolder'), getcwd().'/'.xarModGetVar('multisites','whereisperso') . cleanDN($domaine_a_creer) );

	// second modif config.php
	chdir($weAreIn);
// pour test
	nettoie_config( $domaine_a_creer, $database_utilisee, $prefix_utilise) ;

	// third, add some variables in the new database
	// get some variables, while ex database still open.
	$whereisperso 			= xarModGetVar('multisites','whereisperso');
	$masterfolderOriginal 	= xarModGetVar('multisites','masterfolder');
	$masterfolderSubSite	= xarModGetVar('multisites','whereisperso') . cleanDN($domaine_a_creer).'/';
	$oldPrefix = xarConfigGetVar('prefix');

/*
global $xarconfig;
print("<pre>");
print_r($xarconfig);
print("</pre>");
print('<hr>');
print(xarConfigGetVar('tipath'));
print('<hr>');
// print('tipath: '.$tipath);
// die;
*/

	// i have to delete tipath from the cache.
	// unset($xarconfig['tipath']);

	// replace $tipath with 'sitesettings/mouzaia/'.$tipath
	// this tipath is coming from the new database.

	// then, I rewrite these variables in the new database.
	// to avoid accidents, I test if it is not already there.
	// the 2 functions multisites* are a pure copy of xar*
	// I had to do this in order to be able to use an other table
	// if it happend $prefix is changed.

	if (!(strstr(xarConfigGetVar('tipath'),$masterfolderSubSite ))) {
		$tipath = xarConfigGetVar('tipath');
		multisitesConfigSetVar(	'tipath',
								$tipath,
								$masterfolderOriginal, 
								$masterfolderSubSite,
								$prefix_utilise,
								$whereisperso,
								$oldPrefix,
								$database_utilisee );
		}

	multisitesModSetVar('multisites',
						'whereisperso',
						$whereisperso,
						$prefix_utilise,
						$oldPrefix,
						$database_utilisee
						);
	multisitesModSetVar('multisites',
						'masterfolder',
						$masterfolderSubSite,
						$prefix_utilise,
						$oldPrefix,
						$database_utilisee
						);
    multisitesModSetVar('multisites', 
						'master', 
						'0',
						$prefix_utilise,
						$oldPrefix,
						$database_utilisee
						);

	// and I dont care about reopening the old database, it is going to be reopened when the page will be refreshed.
	}
	

//Old function setup for PN 
function multisites_admin_initconfig() {
	$lNext = false;
    if (!xarSecurityCheck('AdminMultisites')) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
	    }

	$server_name = xarVarCleanFromInput('server_name');
	$cWhereIsPerso = xarVarCleanFromInput('cWhereIsPerso');
	// create the params folder.
	$var = is_dir ($cWhereIsPerso);
	if ($var == true) {
		// echo "The directory exists";
		}
	else {
		//	echo "The directory does not exist";
	    $output = new pnHTML();
        $output->Text( _MULTISITES_CREATEFOLDER.": ".$cWhereIsPerso );
        return $output->GetOutput();
		}

	// creer whoisit.inc.php
	if (!(is_file($cWhereIsPerso.'whoisit.inc.php'))) {
		$oldumask = umask(0);
		$lOk = fopen($cWhereIsPerso.'whoisit.inc.php','w');
		if ($lOk) {
			fwrite($lOk, "<?php\n");
//			fwrite($lOk, "global \$".$server_name.";\n");
			fwrite($lOk, "global \$HTTP_HOST,\$SERVER_NAME;\n");
			fwrite($lOk, "\$serverName = \$".$server_name.";\n");
			fwrite($lOk, "\$serverName = str_replace('www.','',\$serverName);\n");
			fwrite($lOk, "\$serverName = str_replace('.org','',\$serverName);\n");
			fwrite($lOk, "\$serverName = str_replace('.net','',\$serverName);\n"); 
			fwrite($lOk, "\$serverName = str_replace('.com','',\$serverName);\n");
			fwrite($lOk, "?>\n");
			fclose($lOk);
		} else {
		    $output = new pnHTML();
	        $output->Text("Pas pu creer fichier whoisit.inc.php");
	        return $output->GetOutput();
			}

		umask($oldumask);
		}

	// creer dossier pour http_host ou server_name.
	global $SERVER_NAME, $HTTP_HOST;
	if ($server_name == 'SERVER_NAME') {
		$nomServeur = $SERVER_NAME;
		$nomMaster = $SERVER_NAME;
	} else {
		$nomServeur = $HTTP_HOST;
		$nomMaster = $HTTP_HOST;
	}	
	// for instance, if www.mouzaia.com is $HTTP_HOST, then it will create sitesettings/mouzaia
	// BUT, if it is toto.mouzaia.com, it is going to be sitesettings/toto.mouzaia
	// does not matter, when adding a new domain, it will not be a pb
	// www has no interest to be keeped
	$nomServeur = cleanDN( $nomServeur );

	// necessary to add there all the possible others.
	$nomServeur = $cWhereIsPerso . $nomServeur;

	// create the folder for the domain, in 'sitesettings'
	$var = is_dir ($nomServeur);

	if ($var == true) { }
	else {
			$oldumask = umask(0);
			if (!mkdir($nomServeur,0777)) {
			    $output = new pnHTML();
		        $output->Text("Pas pu creer dossier serveur");
		        return $output->GetOutput();
			}
			umask($oldumask);
		}

	// create the folder "sitesettings/images"
	$var = is_dir ($nomServeur."/images");
	if ($var == true) { }
	else {
			$oldumask = umask(0);
			if (!mkdir($nomServeur."/images",0777)) {
			    $output = new pnHTML();
		        $output->Text("Pas pu creer dossier serveur/images: ".$nomServeur);
		        return $output->GetOutput();
			}
			umask($oldumask);
		}

	// create the folder "sitesettings/images/topics"
	$var = is_dir ($nomServeur."/images/topics");
	if ($var == true) { }
	else {
			$oldumask = umask(0);
			if (!mkdir($nomServeur."/images/topics",0777)) {
			    $output = new pnHTML();
		        $output->Text("Pas pu creer dossier serveur/images/topics");
		        return $output->GetOutput();
			}
			umask($oldumask);
		}

	// create the folder "sitesettings/themes"
	$var = is_dir ($nomServeur."/themes");
	if ($var == true) { }
	else { 
			$oldumask = umask(0);
			if (!(mkdir($nomServeur."/themes",0777))) {
			    $output = new pnHTML();
		        $output->Text("Pas pu creer dossier serveur/themes: ");
		        return $output->GetOutput();
			}
			umask($oldumask);
		}


	// copier config.php en config_multisites_sauve.php, dans 'sitesettings'
	$filenamein  = "config.php";
	$filenameout = $cWhereIsPerso."config_multisites_sauve.php";
	if (!copy($filenamein,$filenameout)) {
		    $output = new pnHTML();
	        $output->Text("Pas pu sauvegarder config.php: ");
	        return $output->GetOutput();
		} else {
			$lNext = true;
		}
	
	if ($lNext) {
		// copier config.php dans parametres/folder
		$filenamein = "config.php";
		$filenameout = $nomServeur."/config.php";
		if (!copy($filenamein,$filenameout)) {
		    $output = new pnHTML();
	        $output->Text("Pas pu recopier config dans param perso");
	        return $output->GetOutput();
		} else {
			$lNext = true;
			}
		}
		

	if ($lNext) {
		// copier tout le contenu de topics dans le nouveau topics.
		$weAreIn = getcwd();
		copy_all( getcwd().'/images/topics/', getcwd().'/'.$nomServeur.'/images/topics/');
		chdir($weAreIn);
		}	

	// replace $tipath with 'sitesettings/mouzaia/'.$tipath
	$tipath = xarConfigGetVar('tipath');
	// to avoid accidents, I test if it is not already there.
	if (!(strstr($tipath,$nomServeur ))) {
		$tipath = $nomServeur . '/' .$tipath;
	    xarConfigSetVar('tipath', $tipath);		
		}
	
	// this site will be the master. 
    xarModSetVar('multisites', 'master', '1');
	// i keep this
	xarModSetVar('multisites', 'whereisperso',$cWhereIsPerso);
	xarModSetVar('multisites', 'masterfolder',$nomServeur.'/');
	
	// creer nouveau config.php
	if ($lNext) {
		umask(0);
		$lOk = fopen('config.php','w');
		if ($lOk) {
			fwrite($lOk, "<?php\n");
			fwrite($lOk, "\$xarconfig['multiSites'] = 1;\n");
			fwrite($lOk, "\$xarconfig['masterURL'] = \"".$nomMaster."\";\n");
			fwrite($lOk, "include(\"".$cWhereIsPerso."whoisit.inc.php\");\n");
			fwrite($lOk, "if (!(empty(\$serverName)))\n");
			fwrite($lOk, "	if (is_file( \"".$cWhereIsPerso."\".\$serverName.\"/config.php\" )) { \n");
			fwrite($lOk, "	{ include(\"".$cWhereIsPerso."\".\$serverName.\"/config.php\");}\n");
			fwrite($lOk, "} else { \n");
			fwrite($lOk, _MULTISITES_UNDECLARED);
			fwrite($lOk, "} \n");
			fwrite($lOk, "?>\n");
			fclose($lOk);
		} else {
		    $output = new pnHTML();
			$output->Text(str_replace('#NOM_FICHIER#','config.php',_MULTISITES_UNABLE_MODIF_CONFIG));
	        return $output->GetOutput();
			}
	}
    xarResponseRedirect(xarModURL('multisites', 'admin', 'main'));
//    Header("Location: admin.php");
		}


function template_admin_updateconfig()
{
    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    $bold = xarVarCleanFromInput('bold');

    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;

    // Update module variables.  Note that depending on the HTML structure used
    // to obtain the information from the user it is possible that the values
    // might be unset, so it is important to check them all and assign them
    // default values if required
    if (!isset($bold)) {
        $bold = 0;
    }
    xarModSetVar('template', 'bold', $bold);

    if (!isset($itemsperpage)) {
        $bold = 10;
    }
    xarModSetVar('template', 'itemsperpage', $itemsperpage);

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('Template', 'admin', 'view'));

    // Return
    return true;
}
//<jojodee> Recursively delete everything in a directory!!
function multisites_admin_recdeldir($sitedir)
{
$current_dir = opendir($sitedir);
    while($topdir = readdir($current_dir)){
        if(is_dir("$sitedir/$topdir") and ($topdir != "." and $topdir!="..")){
            multisites_admin_recdeldir("${sitedir}/${topdir}");
        }elseif($topdir != "." and $topdir!=".."){
            unlink("${sitedir}/${topdir}");
        }
    }
    closedir($current_dir);
    rmdir($sitedir);

return true;
}


// <jojodee> Need new cleanDN function - to clean all extensions that people may want to use
// The multisite config has already setup modvar called DNexts. Let's use this
function multisites_admin_cleanDN($siteDN)
{
  $siteext =xarModGetVar('multisites','DNexts');

  $ext_array = explode(',',$siteext);
  // sort so for examp .com.au is before .com
  usort ($ext_array,"multisites_admin_lengthcmp");
  // get rid of www prefix and all dn extensions
  $siteDN = str_replace('www.','',$siteDN);
  foreach ($ext_array as $key => $ext) {
    $siteDN = str_replace($ext,'',$siteDN);
  }
 return $siteDN;
}

// <jojodee> Sorts the array of domain extensions -
// eg must do .com.au before .com else wrong outcome
function multisites_admin_lengthcmp ($a, $b) {
    if (strlen($a) > strlen($b)) return 0;
    return ($a > $b) ? -1 : 1;
}




######################################################################
function cleanDN($nomServeur) {
	$nomServeur = str_replace("www.","",$nomServeur);
	$nomServeur = str_replace(".com","",$nomServeur);
	$nomServeur = str_replace(".net","",$nomServeur);
	$nomServeur = str_replace(".org","",$nomServeur);
	$nomServeur = str_replace(".fr","",$nomServeur);
	$nomServeur = str_replace(".co.uk","",$nomServeur);
	return $nomServeur;
	}

function nettoie_config($domaine_a_creer, $database_utilisee, $prefix_utilise) {
	// second modify config.php in $domaine_a_creer.
	$nomConfig = getcwd().'/'.xarModGetVar('multisites','whereisperso') . cleanDN($domaine_a_creer) . '/' . 'config.php';
	if (!(empty($COMSPEC))) {
		// windows
		$nomConfig = str_replace("/","\\",$nomConfig);
	} else {
		// linux
		$nomConfig = str_replace("\\","/",$nomConfig);
	}		

	$aConfig = file( $nomConfig );
	$fd = fopen( $nomConfig, 'w');
	while (list ($line_num, $line) = each ($aConfig)) {
		if (strstr($line,"\$xarconfig['dbname']")) {
			$aConfig[$line_num] = "\$xarconfig['dbname']='".$database_utilisee."';";
			$line = "\$xarconfig['dbname']='".$database_utilisee."';";
			}
		if (strstr($line,"\$xarconfig['prefix']")) {
			$aConfig[$line_num] = "\$xarconfig['prefix']='".$prefix_utilise."';";
			$line = "\$xarconfig['prefix']='".$prefix_utilise."';";
			}
			
		fwrite( $fd, trim($line) ."\n");
		}
	fclose( $fd );
		
// die;
	}
	

function copy_all($from_path,$to_path) {
	global $COMSPEC;
	$oldumask = umask(0);
	if (!(empty($COMSPEC))) {
		// windows
		$from_path = str_replace("/","\\",$from_path);
		$to_path = str_replace("/","\\",$to_path);
	} else {
		// linux
		$from_path = str_replace("\\","/",$from_path);
		$to_path = str_replace("\\","/",$to_path);
	}		
	
	if (!is_dir($to_path))
		{
		  create_path($to_path);
		}
	if(!is_dir($to_path))
		{
		    $output = new pnHTML();
	        $output->Text('Creating destination path failed.');
        	return $output->GetOutput();
		}
	else
		{
			rec_copy($from_path, $to_path);
		}
	if (!is_dir($to_path))
	    { mkdir($to_path, 0777); }

	$this_path = getcwd();

	if (is_dir($from_path))
		{
		chdir($from_path);
		$handle = opendir('.');

		while (($file = readdir($handle)) !== false)
		{
			if (($file != ".") && ($file != ".."))
			{
				if (is_dir($file))
					{
					rec_copy ($from_path._FOLDER_SEPARATOR.$file, $to_path._FOLDER_SEPARATOR.$file);
					chdir($from_path);
					}
				if (is_file($file))
					{
					copy($from_path._FOLDER_SEPARATOR.$file, $to_path._FOLDER_SEPARATOR.$file);
			        }
		      }
	    }
		closedir($handle); 
	} 
  umask($oldumask);
	}

##############################################
function rec_copy($from_path, $to_path)
{
	$oldumask = umask(0);
  if (!is_dir($to_path))
    mkdir($to_path, 0777);

  $this_path = getcwd();

  if (is_dir($from_path))
  {
    chdir($from_path);
    $handle = opendir('.');

    while (($file = readdir($handle)) !== false)
    {
      if (($file != ".") && ($file != ".."))
      {
        if (is_dir($file))
        {
          rec_copy ($from_path._FOLDER_SEPARATOR.$file, 
$to_path._FOLDER_SEPARATOR.$file);
          chdir($from_path);
        }
        if (is_file($file))
        {
          copy($from_path._FOLDER_SEPARATOR.$file,
$to_path._FOLDER_SEPARATOR.$file);
        }
      }
    }
    closedir($handle); 
  }
  umask($oldumask);

}
##############################################
function create_path($to_path)
{
	$oldumask = umask(0);
	$path_array = explode(_FOLDER_SEPARATOR, $to_path );
	
  // split the path by directories
	$dir='';                 // start with empty directory
	foreach($path_array as $key => $val) {
  // echo "$key => $val\n";
	if (!strpos($val, ':')) {  // if it's not a drive letter
    	$dir .= '/'. $val;
	    if (!is_dir($dir)) {
    	  // echo "Not a dir: $dir\n";
	      if (!mkdir($dir, 0777)) {
		    $output = new pnHTML();
	        $output->Text('Failed creating directory: $dir');
        	return $output->GetOutput();
			}
	      }
	    }
	  }
  umask($oldumask);
}



// ==================================================================================================
// the next 3 functions are replacement of them equivalent, xarMod*
// i have been obliged to do this terrible hacking, unable to change the prefix in the xartables ...
function multisitesModSetVar(	$modname, 
								$name, 
								$value,
								$prefix_utilise,
								$oldPrefix,
								$database_utilisee
								)
	{
    list($dbconn) = xarDBGetConn();
   	$xartable = xarDBGetTables();
    if ((empty($modname)) || (empty($name))) {
    	    return false;
	    }
	// and change database
	// then, open the new database.
	// Get database parameters
	$dbtype 	= xarConfigGetVar('dbtype');
	$dbhost 	= xarConfigGetVar('dbhost');
	$dbname 	= xarConfigGetVar('dbname');
	$dbuname 	= xarConfigGetVar('dbuname');
	$dbpass 	= xarConfigGetVar('dbpass');
	
	// Start connection with the new database.
	$dbconn 	= ADONewConnection($dbtype);
	$dbh 		= $dbconn->Connect($dbhost, $dbuname, $dbpass, $database_utilisee);

    $modulevarstable = $xartable['module_vars'];

	// i have to change the name of the table, due to a possible change of $prefix.
	$modulevarstable = str_replace($oldPrefix,$prefix_utilise,$modulevarstable);

	// --------------------------------------------------------------------------

	list($dbconn) = xarDBGetConn();

    $curvar = multisitesModGetVar($modname, $name, $oldPrefix, $prefix_utilise);


    if (!isset($curvar)) {
        $nextid = $dbconn->GenId($modulevarstable);
        $query = "INSERT INTO $modulevarstable 
                     ( xar_id,
                       xar_modname,
                       xar_name,
                       xar_value )
                  VALUES
                     ('" . $nextid . "',
                      '" . xarVarPrepForStore($modname) . "',
                      '" . xarVarPrepForStore($name) . "',
                      '" . xarVarPrepForStore($value) . "');";
    } else {
		$query = "UPDATE $modulevarstable
              SET xar_value = '" . xarVarPrepForStore($value) . "'
              WHERE xar_modname = '" . xarVarPrepForStore($modname) . "'
              AND xar_name = '" . xarVarPrepForStore($name) . "'";
	}

    $dbconn->Execute($query);
    if($dbconn->ErrorNo() != 0) {
		die(mysql_error());
        return false;
    }
    return true;
}

function multisitesModGetVar($modname, $name, $oldPrefix, $prefix_utilise)
{
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $modulevarstable = $xartable['module_vars'];

	// i have to change the name of the table, due to a possible change of $prefix.
	$modulevarstable = str_replace($oldPrefix,$prefix_utilise,$modulevarstable);

    $query = "SELECT xar_value 
              FROM $modulevarstable
              WHERE xar_modname = '" . xarVarPrepForStore($modname) . "'
              AND xar_name = '" . xarVarPrepForStore($name) . "'";

    $result = $dbconn->Execute($query);

    if($dbconn->ErrorNo() != 0) {
        return;
    }

    if ($result->EOF) {
        return;
    }

    list($value) = $result->fields;
    $result->Close();

    return $value;
}


function multisitesConfigSetVar( 	$name, 
									$value,
									$masterfolderOriginal,
									$masterfolderSubSite,
									$prefix_utilise,
									$whereisperso,
									$oldPrefix,
									$database_utilisee )
{
$value = str_replace($masterfolderOriginal,$masterfolderSubSite,$value);
/*
print("VALUE: ".$value.'<br />');
print("MASTERFOLDER ORIGINAL: ".$masterfolderOriginal.'<br />');
print("MASTERFOLDER SUB SITE: ".$masterfolderSubSite.'<br />');
print("OLDPREFIX: ".$oldPrefix.'<br />');
print("PREFIX_UTILISE: ".$prefix_utilise.'<br />');
print("WHEREISPERSO: ".$whereisperso.'<br />');
// die;
*/

list($dbconn) 	= xarDBGetConn();
$xartable 		= xarDBGetTables();

// and change database
// then, open the new database.
// Get database parameters
/* yes, I know, dirty programming.
$dbtype 	= $xarconfig['dbtype'];
$dbhost 	= $xarconfig['dbhost'];
$dbname 	= $xarconfig['dbname'];
$dbuname 	= $xarconfig['dbuname'];
$dbpass 	= $xarconfig['dbpass'];
*/


// Start connection with the new database.
// $dbconn 	= ADONewConnection($dbtype);
$dbconn = ADONewConnection(
					xarConfigGetVar('dbtype')
					);
// $dbh 		= $dbconn->Connect($dbhost, $dbuname, $dbpass, $database_utilisee);
$dbh 	= $dbconn->Connect(
					xarConfigGetVar('dbhost'), 
					xarConfigGetVar('dbuname'), 
					xarConfigGetVar('dbpass'), 
					xarConfigGetVar('database_utilisee')
					);

$table 		= $xartable['module_vars'];
// $columns 	= &$xartable['module_vars_column'];

list($dbconn) = xarDBGetConn();

// i have to change the name of the table, due to a possible change of $prefix.
$table 				= str_replace($oldPrefix,$prefix_utilise,$table);
/*
// _column arrays dooes not exist anymore.
// $columns[value] 	= str_replace($oldPrefix,$prefix_utilise,$columns[value]);
// $columns[modname] 	= str_replace($oldPrefix,$prefix_utilise,$columns[modname]);
// $columns[name] 		= str_replace($oldPrefix,$prefix_utilise,$columns[name]);
*/
// --------------------------------------------------------------------------
$query = "UPDATE $table
          SET xar_value='" . xarVarPrepForStore(serialize($value)) . "'
          WHERE xar_modname='" . xarVarPrepForStore(_XAR_CONFIG_MODULE) . "'
          AND xar_name='" . xarVarPrepForStore($name) . "'";

// print($query.'<br />');

$dbconn->Execute($query);
if($dbconn->ErrorNo() != 0) {
//		die("ERREUR: ".$dbconn->ErrorNo().' - '.mysql_error());
        return false;
    }
// die("FIN");
return true;
}

// ==================================================================================================

?>
