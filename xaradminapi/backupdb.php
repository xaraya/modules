<?php
/**
 * @package modules
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools
 * @link http://xaraya.com/index.php/release/887.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * Backup tables in your database
 *
 * @author jojodee
 * @return array ['bkfiletype']
                 ['bkfilename']=$backupabsolutepath.$partbackupfilename;
                 ['bkname']
 * @TODO: Add in multidatabase once multidatabase functionality and location decided
 * @TODO: Remove all the commented out code once classes fully tidied and tested
 */
function sitetools_adminapi_backupdb($args)
{
    extract($args);
    // Security check - allow scheduler api funcs to run as anon bug #2802
    //if (!xarSecurityCheck('AdminSiteTools')) return;

    $items=array();
    $items['startbackup']=$startbackup;
    $items['usegz']=$usegz;
    //Assign missing or empty variables
    if (!isset($startbackup)) return;

    if ((($usegz==1) || !isset($usegz)) && (bool)(function_exists('gzopen'))) {
       $GZ_enabled =true;
    } else {
       $GZ_enabled = false;
    }

    if ($screen=='') $screen==false;

    if (!isset($dbname) || ($dbname='') || (empty($dbname))){
        $dbconn =& xarDBGetConn();
            $dbname= xarDB::getName();
            $dbtype= xarDBGetType();
    }
    //setup variables
    $items['usegz']=$usegz;
    $items['screen']=$screen;
    $backtickchar   = '';
    $quotechar      = '\'';
    $buffer_size    = 32768;
    $usedbprefix = xarModGetVar('sitetools','usedbprefix');
    $items['number_of_cols'] = xarModGetVar('sitetools','colnumber');
    $number_of_cols=$items['number_of_cols'];
    $lineterminator = xarModGetVar('sitetools','lineterm'); // not in use  --hard coded with \n for now
    $backupabsolutepath= xarModGetVar('sitetools','backuppath').'/';
    $items['warning']=0;

    //Let's make dbname as a prefix configurable
    if ($usedbprefix==1) {
        $thedbprefix=$dbname.'.';
    } else {
        $thedbprefix='';
    }

    if (xarModGetVar('sitetools','timestamp') ==1) {
        $items['backuptimestamp'] = '.'.date('Y-m-d');
    } else {
        $items['backuptimestamp'] = '';
    }
    //check directory exists and is writeable
    $items['warningmessage']='<span class="xar-accent">'
                            .xarML('WARNING: directory does not exist or is not writeable: ').$backupabsolutepath.'</span><br /><br />'
                            .xarML(' Please ensure the backup directory exisits and is writeable');

    if ((!is_dir($backupabsolutepath)) || (!is_writeable($backupabsolutepath))) {
       $items['warning']=1;
       return $items;
    }
    $items['authid']     = xarSecGenAuthKey();
    $backuptimestamp    = $items['backuptimestamp'];
    $fullbackupfilename = $dbname.'xar_backup'.$backuptimestamp.'.sql'.($GZ_enabled ? '.gz' : '');
    $partbackupfilename = $dbname.'.xar_backup_partial'.$backuptimestamp.'.sql'.($GZ_enabled ? '.gz' : '');
    $strubackupfilename = $dbname.'.xar_backup_structure'.$backuptimestamp.'.sql'.($GZ_enabled ? '.gz' : '');
    $tempbackupfilename = $dbname.'.xar_backup.temp.sql'.($GZ_enabled ? '.gz' : '');
    $xarbackupversion   = '0.2.0'; //TO DO: get actual version from var
    $items['gz-enabled']=$GZ_enabled;
    $runningstatus=array();

    if (!function_exists('getmicrotime')) {
        function getmicrotime()
        {
            list($usec, $sec) = explode(' ', microtime());
            return ((float) $usec + (float) $sec);
        }
    }
    // Instantiation of SiteTools class
     include_once("modules/sitetools/xarclass/dbSiteTools_".$dbtype.".php");
     $classname="dbSiteTools_".$dbtype;
     $bkitems= new $classname();

    //Open up files for write and setup up file headers
    if (($GZ_enabled && ($zp = gzopen($backupabsolutepath.$tempbackupfilename, 'wb'))) || (!$GZ_enabled && ($fp = fopen($backupabsolutepath.$tempbackupfilename, 'wb')))) {
        $fileheaderline = "# Xaraya SiteToolsBackup v".$xarbackupversion."\n# mySQL backup (".date('F j, Y g:i a').")   Type = ";
        if ($GZ_enabled) {
            gzwrite($zp, $fileheaderline, strlen($fileheaderline));
            $fp='';
        } else {
            fwrite($fp, $fileheaderline, strlen($fileheaderline));
            $zp='';
        }

        //headers for structure only
        if ($startbackup == 'structure') {
            if ($GZ_enabled) {
                gzwrite($zp, "Structure Only\n\n", strlen("Structure Only\n\n"));
            } else {
                fwrite($fp, "Structure Only\n\n", strlen("Structure Only\n\n"));
            }
            $backuptype = 'full';
            $btype=xarML('Structure Only');
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
            $btype=xarML('Selected Tables - Complete Inserts');
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
            if ($startbackup=='complete')
            {
              $btype=xarML('Full - Complete Inserts');
            } else {
            $btype=xarML('Full - Standard Inserts');
            }
            unset($SelectedTables);
            $SelectedTables =$bkitems-> selecttables($dbname);
         /* Move to class
         $tables = mysql_list_tables($dbname);
            if (is_resource($tables)) {
                $tablecounter = 0;
                 while (list($tablename) = mysql_fetch_array($tables)) {
                    $SelectedTables["$dbname"][] = $tablename;
                }
            }
         */

        }//headers finished

        $runningstatus[]['message']='<span id="topprogress" class="xar-block-title">'.xarML('Overall Progress: ').'</span>';
        $runningstatus[]['message']='<p>'.xarML('Checking tables for database ').'<b>'.$dbname."</b></p>";

        //Let's check the tables and return errors
        $TableErrors = array();
        $TableErrors = $bkitems-> checktables($SelectedTables);
        /* Move to class

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
       */
        if (count($TableErrors) > 0) {
            $tableerrormsg=xarML('TABLE ERRORS!');
            $runningstatus[]['message']='<strong>'.$tableerrormsg.'</strong><ul><li>'.implode('</li><li>', $TableErrors).'</li></ul>';
        }

        //Put this here for now - format output later
        $overallrows = 0;
        $overallrows = $bkitems-> bkcountoverallrows($SelectedTables);
        //Count up the overall number of rows in the selected table list
        /* Move to classes
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
        */
        //Let's do the actual backup now

        $starttime = getmicrotime();
        $alltablesstructure = '';
        $runningstatus[]['message']=xarML('Creating table structures for ').'<b>'.$dbname.'</b>'.xarML(' database tables').'<br /><br />';
           //Switch to backup class
           //Pass $SelectedTables
           //Pass all our vars in an array
           $bkvars = array('SelectedTables' => $SelectedTables,
                           'GZ_enabled' => $GZ_enabled,
                           'number_of_cols' => $number_of_cols,
                           'overallrows'    => $overallrows,
                           'thedbprefix'    => $thedbprefix,
                           'alltablesstructure' => $alltablesstructure,
                           'fp' =>$fp,
                           'zp' =>$zp,
                           'startbackup' => $startbackup,
                           'backtickchar' => $backtickchar,
                           'quotechar' => $quotechar,
                           'buffer_size' =>$buffer_size,
                        'runningstatus' =>$runningstatus,
                        'starttime' => $starttime,
                        'screen'    => $screen);

        $runningstatus = $bkitems-> backup($bkvars);
           /*
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
                   $tablestructure  = "CREATE TABLE ".$thedbprefix.$SelectedTables["$dbname"]["$t"]." (\n";
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
                    if ($startbackup=='complete') {
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
                            // @set_time_limit(60);
                            $runningstatus[]['message']='<b>'.$SelectedTables["$dbname"]["$t"].' ('.number_format($rows["$t"]).' records, ['.number_format(($currentrow / $rows["$t"])*100).'%])</b>';
                            $elapsedtime = getmicrotime() - $starttime;
                            $percentprocessed = ($processedrows + $currentrow) / $overallrows;
                            $runningstatus[]['message']='Overall Progress: '.number_format($processedrows + $currentrow).' / '.number_format($overallrows).' ('.number_format($percentprocessed * 100, 1).'% done) ['.FormattedTimeRemaining($elapsedtime).' elapsed';
                            if (($percentprocessed > 0) && ($percentprocessed < 1)) {
                                $runningstatus[]['message']=', '.FormattedTimeRemaining(abs($elapsedtime - ($elapsedtime / $percentprocessed))).' remaining';
                            }
                            $runningstatus[]['message']= ']';
                            //flush();

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
end the backup that is moved to class
*/
        if ($startbackup == 'structure') {
            if (file_exists($backupabsolutepath.$strubackupfilename)) {
                unlink($backupabsolutepath.$strubackupfilename); // Windows won't allow overwriting via rename
            }
            rename($backupabsolutepath.$tempbackupfilename, $backupabsolutepath.$strubackupfilename);
            $items['bkfiletype']=xarML('Structure backup filename: ');
            $items['bkfilename']=$backupabsolutepath.$strubackupfilename;
            $items['bkname']=$strubackupfilename;
        } else if ($backuptype == 'full') {
            if (file_exists($backupabsolutepath.$fullbackupfilename)) {
                unlink($backupabsolutepath.$fullbackupfilename); // Windows won't allow overwriting via rename
            }
            rename($backupabsolutepath.$tempbackupfilename, $backupabsolutepath.$fullbackupfilename);
            $items['bkfiletype']=xarML('Full backup filename: ');
            $items['bkfilename']=$backupabsolutepath.$fullbackupfilename;
            $items['bkname']=$fullbackupfilename;
        } else {
            if (file_exists($backupabsolutepath.$partbackupfilename)) {
                unlink($backupabsolutepath.$partbackupfilename); // Windows won't allow overwriting via rename
            }
            rename($backupabsolutepath.$tempbackupfilename, $backupabsolutepath.$partbackupfilename);
            $items['bkfiletype']=xarML('Partial backup filename: ');
            $items['bkfilename']=$backupabsolutepath.$partbackupfilename;
            $items['bkname']=$partbackupfilename;
        }
        $items['bkfilesize']=FileSizeNiceDisplay(filesize($items['bkfilename']), 2);
        $items['completetime']= FormattedTimeRemaining(getmicrotime() - $starttime, 2);
        $items['deleteurl']="[Click to Delete]";
    } else {
        $items['warning']=1;
    }
    $items['runningstatus']=$runningstatus;
    $items['backuptype']=$backuptype;
    $items['btype']=$btype;

    // Log a message
    xarLogMessage('SITETOOLS: Created backup');

   //Return data for display
   return $items;
}
 //A few formatting functions
 function FormattedTimeRemaining($seconds, $precision=1)
 {
    if ($seconds > 86400) {
        return number_format($seconds / 86400, $precision).' days';
    } else if ($seconds > 3600) {
        return number_format($seconds / 3600, $precision).' hours';
    } else if ($seconds > 60) {
        return number_format($seconds / 60, $precision).' minutes';
    }
    return number_format($seconds, $precision).' seconds';
}
function FileSizeNiceDisplay($filesize, $precision=2)
{
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