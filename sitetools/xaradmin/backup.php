<?php
/*
 * File: $Id: $
 *
 * SiteTools Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by Jo Dalle Nogare
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage SiteTools module
 * @author jojodee@xaraya.com <http://xaraya.athomeandabout.com >
 * @based in part on backupDB() by James Heinrich <info@silisoftware.com>
*/

/**
 * Backup tables in your database 
 * TO DO: Add in multidatabase once multidatabase functionality and location decided
 * TO DO: Split off functions to sitetools api
 * TO DO: add in more customization of configurations
 */
function sitetools_admin_backup($args)
{
   if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('startbackup', 'str:1:', $startbackup, '', XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('usegz', 'int:1', $usegz, 0, XARVAR_NOT_REQUIRED)) return;
  if (!xarVarFetch('screen', 'int:1', $screen, 0, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('SelectedTables', 'array:', $SelectedTables, '', XARVAR_NOT_REQUIRED)) return;
   // Security check
    if (!xarSecurityCheck('AdminSiteTools')) return;

    $data=array();
    //setup variables
    $data['usegz']=$usegz;
    $data['screen']=$screen;
    $backtickchar   = '';
    $quotechar      = '\'';
    $buffer_size    = 32768;
    $data['number_of_cols'] = xarModGetVar('sitetools','colnumber');
    $number_of_cols=$data['number_of_cols'];
    $lineterminator = xarModGetVar('sitetools','lineterm'); // not in use  --hard coded with \n for now
    $backupabsolutepath= xarModGetVar('sitetools','backuppath').'/';
    $data['warning']=0;

    if (($data['usegz']==1) && (bool)(function_exists('gzopen'))) {
         $GZ_enabled =true;
    } else {
       $GZ_enabled = false;
    }

    if (xarModGetVar('sitetools','timestamp') ==1) {
        $data['backuptimestamp'] = '.'.date('Y-m-d');
    } else {
        $data['backuptimestamp'] = '';
    }
    //check directory exists and is writeable
   $data['warningmessage']='<span class="xar-accent">'
                            .xarML('WARNING: directory does not exist or is not writeable: ').$backupabsolutepath.'</span><br /><br />'
                            .xarML(' Please ensure the backup directory exisits and is writeable');

    if ((!is_dir($backupabsolutepath)) || (!is_writeable($backupabsolutepath))) {
       $data['warning']=1;
       return $data;
    }
    $data['authid']     = xarSecGenAuthKey();
    $backuptimestamp    = $data['backuptimestamp'];
    $fullbackupfilename = 'xar_backup'.$backuptimestamp.'.sql'.($GZ_enabled ? '.gz' : '');
    $partbackupfilename = 'xar_backup_partial'.$backuptimestamp.'.sql'.($GZ_enabled ? '.gz' : '');
    $strubackupfilename = 'xar_backup_structure'.$backuptimestamp.'.sql'.($GZ_enabled ? '.gz' : '');
    $tempbackupfilename = 'xar_backup.temp.sql'.($GZ_enabled ? '.gz' : '');
    $xarbackupversion   = '0.1'; //TO DO: get actual version from var
    $data['gz-enabled']=$GZ_enabled;
    $runningstatus=array();

    //set javascript header
    xarModAPIfunc('base', 'javascript', 'modulefile', array('filename'=>'sitetools_admin_backup.js'));

    if (!function_exists('getmicrotime')) {
	    function getmicrotime() {
	        list($usec, $sec) = explode(' ', microtime());
	        return ((float) $usec + (float) $sec);
	    }
	}

        list($dbconn) = xarDBGetConn();
        $dbname= xarDBGetName();
	    $data['startbackup']=$startbackup;
		$data['confirm']=$confirm;


    if (empty($startbackup))
     {
       // No confirmation yet - display a suitable form to obtain confirmation
       // of this action from the user
       //setup option links
        $data['backupops']=array();
        $data['backupops']['complete'] = xarML('Full backup - complete inserts');
        $data['backupops']['standard'] = xarML('Full backup - standard inserts');
        $data['backupops']['partial'] =  xarML('Partial - select tables, complete inserts');
        $data['backupops']['structure'] = xarML('Full backup - Structure only');

        $confirm='';

    //Start actual backup for all types here
    } elseif ($startbackup) {
        if ($startbackup =='partial'){

             // Generate a one-time authorisation code for this operation
            //$data['authid']         = xarSecGenAuthKey();
            flush();
            $confirm='';

            $dbtables=array();
            list($dbconn) = xarDBGetConn();
            $dbname= xarDBGetName();
            $tables = mysql_list_tables($dbname);
            $i=0;
            if ($tables) {
 	            while (list($tablename) = mysql_fetch_array($tables)) {
 	               $i++;
            	   $SQLquery = 'SELECT COUNT(*) AS num FROM '.$tablename;
				   $result = mysql_query($SQLquery);
				   $row = mysql_fetch_array($result);
                   $dbtables[] = array('tablenum'=>$i, 'tablename'=>$tablename, 'tablerecs'=>$row['num']);
                }
            }else {
                echo "No table query results";
            }
            $tabletotal=mysql_numrows($tables);
            $data['checkboxname']='SelectedTables['.htmlentities($dbname, ENT_QUOTES).'][]';
            $data['dbname']=$dbname;

            $data['dbtables']=$dbtables;
            return $data;
         }

        if (!xarSecConfirmAuthKey()) {return;}
        @set_time_limit(600);

         //Open up files for write and setup up file headers
        if (($GZ_enabled && ($zp = gzopen($backupabsolutepath.$tempbackupfilename, 'wb'))) || (!$GZ_enabled && ($fp = fopen($backupabsolutepath.$tempbackupfilename, 'wb')))) {
		    $fileheaderline = "# Xaraya SiteToolsBackup v".$xarbackupversion."\n# mySQL backup (".date('F j, Y g:i a').")   Type = ";
		    if ($GZ_enabled) {
			    gzwrite($zp, $fileheaderline, strlen($fileheaderline));
		    } else {
			    fwrite($fp, $fileheaderline, strlen($fileheaderline));
            }

            //headers for structure only
            if ($startbackup == 'structure') {

			    if ($GZ_enabled) {
				    gzwrite($zp, "Structure Only\n\n", strlen("Structure Only\n\n"));
			    } else {
				    fwrite($fp, "Structure Only\n\n", strlen("Structure Only\n\n"));

			    }
			    $backuptype = 'full';
			    $tables = mysql_list_tables($dbname);
			    if ($tables) {
				    $tablecounter = 0;
				    while (list($tablename) = mysql_fetch_array($tables)) {
					    $SelectedTables["$dbname"][] = $tablename;
				    }
			    }
             //headers for partial - selected tables
            } elseif ($SelectedTables) {

			    if ($GZ_enabled) {
				    gzwrite($zp, "Selected Tables Only\n\n", strlen("Selected Tables Only\n\n"));
 			    } else {
				fwrite($fp, "Selected Tables Only\n\n", strlen("Selected Tables Only\n\n"));
			    }

			    $backuptype = 'partial';
			    $SelectedTables = $SelectedTables;

            // headers for complete backup
		    } else {

		    	if ($GZ_enabled) {
		    		gzwrite($zp, "Complete\n\n", strlen("Complete\n\n"));
		    	} else {
			    	fwrite($fp, "Complete\n\n", strlen("Complete\n\n"));
		    	}
			    $backuptype = 'full';
			    unset($SelectedTables);
			    $tables = mysql_list_tables($dbname);
			    if (is_resource($tables)) {
			    	$tablecounter = 0;
			     	while (list($tablename) = mysql_fetch_array($tables)) {
	  				    $SelectedTables["$dbname"][] = $tablename;
				    }
			    }
	  	    }//headers finished
		    $runningstatus[]['message']='<span id="topprogress" class="xar-block-title">'.xarML('Overall Progress: ').'</span>';
     		$runningstatus[]['message']='<p>'.xarML('Checking tables for database ').'<b>'.$dbname."</b></p>";

           //Let's check the tables
		   $TableErrors = array();
		   foreach ($SelectedTables as $dbname => $selectedtablesarray) {
		     	mysql_select_db($dbname);
			    foreach ($selectedtablesarray as $selectedtablename) {
				    $result = mysql_query('CHECK TABLE '.$selectedtablename);
				    while ($row = mysql_fetch_array($result)) {
					    if ($row['Msg_text'] == 'OK') {
						    mysql_query('OPTIMIZE TABLE '.$selectedtablename);
					    } else {
						    $TableErrors[] = $row['Table'].' ['.$row['Msg_type'].'] '.$row['Msg_text'];
						    if (!isset($TableErrorTables) || !is_array($TableErrorTables) || !in_array($dbname.'.'.$selectedtablename, $TableErrorTables)) {
							    $TableErrorDB[]     = $dbname;
							    $TableErrorTables[] = $selectedtablename;
						   }
					    }
				    }
			    }
		    }

		   if (isset($TableErrorTables) && is_array($TableErrorTables)) {
		 	   for ($t = 0; $t < count($TableErrorTables); $t++) {
			    	mysql_select_db($TableErrorDB["$t"]);
				    $fixresult = mysql_query('REPAIR TABLE '.$TableErrorTables["$t"].' EXTENDED');
				    while ($fixrow = mysql_fetch_array($fixresult)) {
					   $TableErrors[] = $fixrow['Table'].' ['.$fixrow['Msg_type'].'] '.$fixrow['Msg_text'];
				    }
			   }
		   }

		   if (count($TableErrors) > 0) {
               $runningstatus[]['message']='<b>TABLE ERRORS!</b><ul><li>'.implode('</li><li>', $TableErrors).'</li></ul>';
		    }


           //Put this here for now - format output later
 	        $overallrows = 0;
		    foreach ($SelectedTables as $dbname => $value) {
			   mysql_select_db($dbname);
			   $tablecounter = 1;
			   for ($t = 0; $t < count($SelectedTables["$dbname"]); $t++) {
 		    	    if ($tablecounter++ < $number_of_cols) {
				    } else {
                     $tablecounter=1;
                    }
			    	$SQLquery = 'SELECT COUNT(*) AS num FROM '.$SelectedTables["$dbname"]["$t"];
				    $result = mysql_query($SQLquery);
				    $row = mysql_fetch_array($result);
				    $rows["$t"] = $row['num'];
				    $overallrows += $rows["$t"];
			    }
		    }

		    $starttime = getmicrotime();
		    $alltablesstructure = '';
		    $runningstatus[]['message']=xarML('Creating table structures for ').'<b>'.$dbname.'</b>'.xarML(' database tables').'<br /><br />';
    	    foreach ($SelectedTables as $dbname => $value) {
			    mysql_select_db($dbname);
			    for ($t = 0; $t < count($SelectedTables["$dbname"]); $t++) {
				    $fieldnames     = array();
				    $structurelines = array();
				    $result = mysql_query('SHOW FIELDS FROM '.$SelectedTables["$dbname"]["$t"]);
				    while ($row = mysql_fetch_array($result)) {
					    $structureline  = $row['Field'];
					    $structureline .= ' '.$row['Type'];
					    $structureline .= ' '.($row['Null'] ? '' : 'NOT ').'NULL';
					    if (isset($row['Default'])) {
						    switch ($row['Type']) {
							    case 'tinytext':
							    case 'tinyblob':
							    case 'text':
							    case 'blob':
							    case 'mediumtext':
							    case 'mediumblob':
							    case 'longtext':
							    case 'longblob':
								// no default values
								    break;
							    default:
								    $structureline .= ' default \''.$row['Default'].'\'';
								    break;
						    }
					    }
					    $structureline .= ($row['Extra'] ? ' '.$row['Extra'] : '');
					    $structurelines[] = $structureline;

					    $fieldnames[] = $row['Field'];
				    }
				    mysql_free_result($result);

				    $tablekeys    = array();
				    $uniquekeys   = array();
				    $fulltextkeys = array();
				    $result = mysql_query('SHOW KEYS FROM '.$SelectedTables["$dbname"]["$t"]);
				    while ($row = mysql_fetch_array($result)) {
					    $uniquekeys[$row['Key_name']] = FALSE;
					    if ($row['Non_unique'] == 0) {
						    $uniquekeys[$row['Key_name']] = TRUE;
					    }
					    $fulltextkeys[$row['Key_name']] = FALSE;
					    if ($row['Comment'] == 'FULLTEXT') {
						    $fulltextkeys[$row['Key_name']] = TRUE;
					    }
					    $tablekeys[$row['Key_name']][$row['Seq_in_index']] = $row['Column_name'];
					    ksort($tablekeys[$row['Key_name']]);
				    }
				    mysql_free_result($result);
				    foreach ($tablekeys as $keyname => $keyfieldnames) {
					    $structureline  = '';
					    if ($keyname == 'PRIMARY') {
						    $structureline .= 'PRIMARY ';
					    } else {
						    $structureline .= ($fulltextkeys[$keyname] ? 'FULLTEXT ' : '');
						    $structureline .= ($uniquekeys[$keyname]   ? 'UNIQUE '   : '');
					    }
					    $structureline .= 'KEY'.(($keyname == 'PRIMARY') ? '' : ' '.$keyname);
					    $structureline .= ' ('.implode(',', $keyfieldnames).')';
					    $structurelines[] = $structureline;
				    }

				    $tablestructure  = "CREATE TABLE ".$dbname.".".$SelectedTables["$dbname"]["$t"]." (\n";
                    $tablestructure .= "  ".implode(",\n  ", $structurelines)."\n";
				    $tablestructure .= ");\n\n";

				    $alltablesstructure .= str_replace(' ,', ',', $tablestructure);

			    } // end table structure backup
		    }
		    if ($GZ_enabled) {
			    gzwrite($zp, $alltablesstructure."\n", strlen($alltablesstructure) + strlen("\n"));
		    } else {
			    fwrite($fp, $alltablesstructure."\n", strlen($alltablesstructure) + strlen("\n"));
		    }

		    if ($startbackup != 'structure') {
			    $processedrows    = 0;
			    foreach ($SelectedTables as $dbname => $value) {
				    mysql_select_db($dbname);
				    for ($t = 0; $t < count($SelectedTables["$dbname"]); $t++) {
					    $result = mysql_query('SELECT * FROM '.$SelectedTables["$dbname"]["$t"]);
					    $rows["$t"] = mysql_num_rows($result);
					    if ($rows["$t"] > 0) {
						    $tabledatadumpline = "# dumping data for ".$dbname.".".$SelectedTables["$dbname"]["$t"]."\n";
						    if ($GZ_enabled) {
							    gzwrite($zp, $tabledatadumpline, strlen($tabledatadumpline));
						    } else {
							    fwrite($fp, $tabledatadumpline, strlen($tabledatadumpline));
						    }
					    }
					    unset($fieldnames);
					    for ($i = 0; $i < mysql_num_fields($result); $i++) {
						    $fieldnames[] = mysql_field_name($result, $i);
					    }
					    if ($_REQUEST['startbackup'] == 'complete') {
						    $insertstatement = 'INSERT INTO '.$backtickchar.$SelectedTables["$dbname"]["$t"].$backtickchar.' ('.implode(', ', $fieldnames).') VALUES (';
					    } else {
						    $insertstatement = 'INSERT INTO '.$backtickchar.$SelectedTables["$dbname"]["$t"].$backtickchar.' VALUES (';
					    }
					    $currentrow       = 0;
					    $thistableinserts = '';

					    while ($row = mysql_fetch_array($result)) {
						    unset($valuevalues);
						    foreach ($fieldnames as $key => $val) {
							    $valuevalues[] = mysql_escape_string($row["$key"]);
						    }
						    $thistableinserts .= $insertstatement.$quotechar.implode($quotechar.", ".$quotechar, $valuevalues).$quotechar.");\n";

						    if (strlen($thistableinserts) >= $buffer_size) {
							    if ($GZ_enabled) {
								    gzwrite($zp, $thistableinserts, strlen($thistableinserts));
							    } else {
								    fwrite($fp, $thistableinserts, strlen($thistableinserts));
							    }
							    $thistableinserts = '';
						    }
						    if ((++$currentrow % 1000) == 0) {
							  //  set_time_limit(60);
                                    $runningstatus[]['message']='<b>'.$SelectedTables["$dbname"]["$t"].' ('.number_format($rows["$t"]).' records, ['.number_format(($currentrow / $rows["$t"])*100).'%])</b>';
								    $elapsedtime = getmicrotime() - $starttime;
								    $percentprocessed = ($processedrows + $currentrow) / $overallrows;
								    $runningstatus[]['message']='Overall Progress: '.number_format($processedrows + $currentrow).' / '.number_format($overallrows).' ('.number_format($percentprocessed * 100, 1).'% done) ['.FormattedTimeRemaining($elapsedtime).' elapsed';
								    if (($percentprocessed > 0) && ($percentprocessed < 1)) {
									   $runningstatus[]['message']=', '.FormattedTimeRemaining(abs($elapsedtime - ($elapsedtime / $percentprocessed))).' remaining';
								    }
								    $runningstatus[]['message']= ']';
								    flush();

						    }
					    }

						$runningstatus[]['message']=$SelectedTables["$dbname"]["$t"].' ('.number_format($rows["$t"]).' records, [100%])';
						$processedrows += $rows["$t"];

					    if ($GZ_enabled) {
						    gzwrite($zp, $thistableinserts."\n\n", strlen($thistableinserts) + strlen("\n") + strlen("\n"));
					    } else {
						    fwrite($fp, $thistableinserts."\n\n", strlen($thistableinserts) + strlen("\n") + strlen("\n"));
					    }
				    }
			    }
		    }
		    if ($GZ_enabled) {
			    gzclose($zp);
		    } else {
			    fclose($fp);
		    }

		    $data['completetime']= FormattedTimeRemaining(getmicrotime() - $starttime, 2);
		    if ($startbackup == 'structure') {
			    if (file_exists($backupabsolutepath.$strubackupfilename)) {
				    unlink($backupabsolutepath.$strubackupfilename); // Windows won't allow overwriting via rename
			    }
			    rename($backupabsolutepath.$tempbackupfilename, $backupabsolutepath.$strubackupfilename);
                $data['bkfiletype']=xarML('Structure backup filename: ');
                $data['bkfilename']=$backupabsolutepath.$strubackupfilename;
                $data['bkname']=$strubackupfilename;
                $data['bkfilesize']=FileSizeNiceDisplay(filesize($backupabsolutepath.$strubackupfilename), 2);
		    } else if ($backuptype == 'full') {
			    if (file_exists($backupabsolutepath.$fullbackupfilename)) {
				    unlink($backupabsolutepath.$fullbackupfilename); // Windows won't allow overwriting via rename
			    }
			    rename($backupabsolutepath.$tempbackupfilename, $backupabsolutepath.$fullbackupfilename);
                $data['bkfiletype']=xarML('Full backup filename: ');
                $data['bkfilename']=$backupabsolutepath.$fullbackupfilename;
                $data['bkname']=$fullbackupfilename;
                $data['bkfilesize']=FileSizeNiceDisplay(filesize($backupabsolutepath.$fullbackupfilename), 2);

		    } else {
			    if (file_exists($backupabsolutepath.$partbackupfilename)) {
				    unlink($backupabsolutepath.$partbackupfilename); // Windows won't allow overwriting via rename
			    }
			    rename($backupabsolutepath.$tempbackupfilename, $backupabsolutepath.$partbackupfilename);
                $data['bkfiletype']=xarML('Partial backup filename: ');
                $data['bkfilename']=$backupabsolutepath.$partbackupfilename;
                $data['bkname']=$partbackupfilename;
                $data['bkfilesize']=FileSizeNiceDisplay(filesize($backupabsolutepath.$partbackupfilename), 2);
  		    }
 // TODO          $data['deleteurl']="<a href=".xarModURL('sitetools','admin','deletediskfile', array('filename' => $data['bkfilename'])).">".xarML('Delete backup file now')."</a>";
              $data['deleteurl']="[Click to Delete]";
        } else {
            $data['warning']=1;
	    }
    } //end if start backup
    $data['runningstatus']=$runningstatus;

   //Return data for display
   return $data;
}
 //A few formatting functions - move these later
 function FormattedTimeRemaining($seconds, $precision=1) {
	if ($seconds > 86400) {
		return number_format($seconds / 86400, $precision).' days';
	} else if ($seconds > 3600) {
		return number_format($seconds / 3600, $precision).' hours';
	} else if ($seconds > 60) {
		return number_format($seconds / 60, $precision).' minutes';
	}
	return number_format($seconds, $precision).' seconds';
}
function FileSizeNiceDisplay($filesize, $precision=2) {
	if ($filesize < 1000) {
		$sizeunit  = 'bytes';
		$precision = 0;
	} else {
		$filesize /= 1024;
		$sizeunit = 'kB';
	}
	if ($filesize >= 1000) {
		$filesize /= 1024;
		$sizeunit = 'MB';
	}
	if ($filesize >= 1000) {
		$filesize /= 1024;
		$sizeunit = 'GB';
	}
	return number_format($filesize, $precision).' '.$sizeunit;
}
?>
