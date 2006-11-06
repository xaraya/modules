<?php
/**
 * @package modules
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * SiteTools Database abstraction class extension for mySQL
 *
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @access private
 */
include_once('modules/sitetools/xarclass/dbSiteTools.php');

class dbSiteTools_mysql extends dbSiteTools
{
    function _optimize()
    {
        $tot_data = 0;
        $tot_idx = 0;
        $tot_all = 0;
        $total_gain=0;
        $total_kbs =0;
        $gain=0;
        $rowinfo['total_gain']=0;
        $rowinfo['total_kbs']=0;
        $version=substr(mysql_get_server_info(),0,3);
        $local_query = 'SHOW TABLE STATUS FROM '.$this->dbname;
        $result      = @mysql_query($local_query);
        if (@mysql_num_rows($result)) {
            while ($row = mysql_fetch_array($result)) {
                if ($version>='4.1') {
                  $rowdata[]=array('rowname' => $row[0],
                                    'totaldata'  => $row[6],
                                    'totalidx'   => $row[8],
                                    'gain'       => $row[9]);
                } else {
                   $rowdata[]=array('rowname' => $row[0],
                                    'totaldata'  => $row[5],
                                    'totalidx'   => $row[7],
                                    'gain'       => $row[8]);
                }
                $local_query = 'OPTIMIZE TABLE '.$row[0];
                $resultat  = mysql_query($local_query);
           }
        }

        if (!$resultat) {return false;}

        $rowinfo = array();
        foreach ($rowdata as $datum) {
            $total = $datum['totaldata'] + $datum['totalidx'];
            $total = $total/1024;
            $total = round($total,3);
            $gain  = $datum['gain']/1024;
            $total_gain += $gain;
            $total_kbs  += $total;
            $gain  = round ($gain,3);
            $rowinfo['rowdata'][]=array('total' => $total,
                                        'gain'  => $gain,
                                        'tablename' => $datum['rowname']);
         }
        $rowinfo['total_gain']=$total_gain;
        $rowinfo['total_kbs']=$total_kbs;
        $rowinfo['dbname']=$this->dbname;

        return $rowinfo;

    }

    function _selecttables()
    {
        $tables = mysql_list_tables($this->dbname);
            if (is_resource($tables)) {
                $tablecounter = 0;
                      while (list($tablename) = mysql_fetch_array($tables)) {
                        $SelectedTables["$this->dbname"][] = $tablename;
                        }
            }
        $this->SelectedTables = $SelectedTables;
        return $SelectedTables;
    }

    function _checktables($SelectedTables)
    {
        foreach ($SelectedTables as $this->dbname => $selectedtablesarray) {
               mysql_select_db($this->dbname);
            foreach ($selectedtablesarray as $selectedtablename) {
                $result = mysql_query('CHECK TABLE '.$selectedtablename);
                while ($row = mysql_fetch_array($result)) {
                    if ($row['Msg_text'] == 'OK') {
                        mysql_query('OPTIMIZE TABLE '.$selectedtablename);
                    } else {
                         $TableErrors[] = $row['Table'].' ['.$row['Msg_type'].'] '.$row['Msg_text'];
                        if (!isset($TableErrorTables) || !is_array($TableErrorTables) || !in_array($this->dbname.'.'.$selectedtablename, $TableErrorTables)) {
                            $TableErrorDB[]     = $this->dbname;
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
        return $TableErrors;

        }
    }

    function _bkcountoverallrows($SelectedTables,$number_of_cols)
    {
       $overallrows=0;
       foreach ($SelectedTables as $this->dbname => $value) {
            mysql_select_db($this->dbname);
            $tablecounter = 1;
            for ($t = 0; $t < count($SelectedTables["$this->dbname"]); $t++) {
                if ($tablecounter++ < $number_of_cols) {
                } else {
                    $tablecounter=1;
                }
                $SQLquery = 'SELECT COUNT(*) AS num FROM '.$SelectedTables["$this->dbname"]["$t"];
                $result = mysql_query($SQLquery);
                $row = mysql_fetch_array($result);
                $rows["$t"] = $row['num'];
                $overallrows += $rows["$t"];
            }
        }
        return $overallrows;
    }

    function _backup($bkvars)
    {

        $SelectedTables =$bkvars['SelectedTables'];
        $GZ_enabled =$bkvars['GZ_enabled'];
        $number_of_cols =$bkvars['number_of_cols'];
        $overallrows =$bkvars['overallrows'];
        $thedbprefix =$bkvars['thedbprefix'];
        $alltablesstructure =$bkvars['alltablesstructure'];
        $fp =$bkvars['fp'];
        $zp =$bkvars['zp'];
        $startbackup =$bkvars['startbackup'];
        $backtickchar=$bkvars['backtickchar'];
        $quotechar=$bkvars['quotechar'];
        $buffer_size=$bkvars['buffer_size'];
        $runningstatus=$bkvars['runningstatus'];
        $starttime=$bkvars['starttime'];
        $screen=$bkvars['screen'];

        foreach ($SelectedTables as $this->dbname => $value) {
            mysql_select_db($this->dbname);
            for ($t = 0; $t < count($SelectedTables["$this->dbname"]); $t++) {
                $fieldnames     = array();
                $structurelines = array();
                $result = mysql_query('SHOW FIELDS FROM '.$SelectedTables["$this->dbname"]["$t"]);
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
                $result = mysql_query('SHOW KEYS FROM '.$SelectedTables["$this->dbname"]["$t"]);
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
                   $tablestructure  = "CREATE TABLE ".$thedbprefix.$SelectedTables["$this->dbname"]["$t"]." (\n";
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
                for ($t = 0; $t < count($SelectedTables["$this->dbname"]); $t++) {
                    $result = mysql_query('SELECT * FROM '.$SelectedTables["$this->dbname"]["$t"]);
                    $rows["$t"] = mysql_num_rows($result);
                    if ($rows["$t"] > 0) {
                        $tabledatadumpline = "# dumping data for ".$dbname.".".$SelectedTables["$this->dbname"]["$t"]."\n";
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
                        $insertstatement = 'INSERT INTO '.$backtickchar.$SelectedTables["$this->dbname"]["$t"].$backtickchar.' ('.implode(', ', $fieldnames).') VALUES (';
                    } else {
                        $insertstatement = 'INSERT INTO '.$backtickchar.$SelectedTables["$this->dbname"]["$t"].$backtickchar.' VALUES (';
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
                            $runningstatus[]['message']='<b>'.$SelectedTables["$this->dbname"]["$t"].' ('.number_format($rows["$t"]).' records, ['.number_format(($currentrow / $rows["$t"])*100).'%])</b>';
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

                    $runningstatus[]['message']=$SelectedTables["$this->dbname"]["$t"].' ('.number_format($rows["$t"]).' records, [100%])';
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

    return $runningstatus;
    }
}
?>