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
 * the main administration function
 */
function multisites_admin_main()
{
    // Security check
    if (!xarSecAuthAction(0, 'Multisites::', '::', ACCESS_EDIT)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Return the template variables defined in this function
    return array();

}

function multisites_admin_modifyconfig()
{
    global $HTTP_SERVER_VARS;
    // Security check
    if (!xarSecAuthAction(0, 'Multisites::', '::', ACCESS_ADMIN)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    $data['server_name'] = $HTTP_SERVER_VARS['SERVER_NAME'];
    $data['http_host'] = $HTTP_SERVER_VARS['HTTP_HOST'];
    $data['authid'] = xarSecGenAuthKey();

    // Return the template variables defined in this function
    return $data;

}

function multisites_admin_updateconfig()
{
        list($servervar,
             $themepath,
             $varpath) = xarVarCleanFromInput('servervar',
                                              'themepath',
                                              'varpath');

    // Auth Key
    if (!xarSecConfirmAuthKey()) {
        $msg = xarML('Invalid authorization key for creating new item',
                     'multisites');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    // Security
    if (!xarSecAuthAction(0, 'Multisites::', '::', ACCESS_ADMIN)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }  

    if (!isset($servervar)) {
        $servervar = 'httphost';
    }
    if (!isset($themepath)) {
        $themepath = 'var/default/themes';
    }
    if (!isset($varpath)) {
        $varpath = 'var/default';
    }

    xarModSetVar('multisites', 'servervar', $servervar);
    // Hmmm, needs more thought.
    xarModSetVar('multisites', 'themepath', $themepath);
    xarModSetVar('multisites', 'varpath', $varpath);

}


function multisites_admin_mainload()
{
global $HTTP_HOST,$SERVER_NAME;

    $output = new pnHTML();
    if (!xarSecAuthAction(0, 'Multisites::', '::', ACCESS_ADMIN)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Start the table that holds the information to be modified.  Note how
    // each item in the form is kept logically separate in the code; this helps
    // to see which part of the code is responsible for the display of each
    // item, and helps with future modifications

	// is the multisites already created ?

    $output->SetInputMode(_PNH_VERBATIMINPUT);
	$master = xarModGetVar('multisites','master');

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
	    $row[] = $output->FormText('cWhereIsPerso', 'sitesettings/', 40, 40);
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
    if (!xarSecAuthAction(0, 'Multisites::', '::', ACCESS_ADMIN)) {
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
	


function multisites_admin_initconfig() {
	$lNext = false;
    if (!xarSecAuthAction(0, 'Multisites::', '::', ACCESS_ADMIN)) {
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
    if (!xarSecConfirmAuthKey()) {
        xarSessionSetVar('errormsg', _BADAUTHKEY);
        xarResponseRedirect(xarModURL('Template', 'admin', 'view'));
        return true;
    }

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
print("VALUE: ".$value.'<br>');
print("MASTERFOLDER ORIGINAL: ".$masterfolderOriginal.'<br>');
print("MASTERFOLDER SUB SITE: ".$masterfolderSubSite.'<br>');
print("OLDPREFIX: ".$oldPrefix.'<br>');
print("PREFIX_UTILISE: ".$prefix_utilise.'<br>');
print("WHEREISPERSO: ".$whereisperso.'<br>');
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

// print($query.'<br>');

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