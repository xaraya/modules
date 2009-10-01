<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/* @Get table information from a database for selection of tables for partial backup
 * @author jojodee
 * @param database name, $dbname the physical database name (optional)
 * @param database type $dbtype (optional)
 * @return array $data - table id, table names, number of records
*/
function sitetools_adminapi_gettabledata($dbname='', $dbtype='')
{
    // Security check
    if (!xarSecurityCheck('AdminSiteTools')) return;

    if (($dbname='') || (empty($dbname))){
        $dbconn = xarDB::getConn();
        $dbname= xarDB::getName();
    }

    $items =array();

    switch ($dbtype) {

    default:
            $dbtables=array();
            $dbconn = xarDB::getConn();
            $dbname= xarDB::getName();
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

            $items['tabletotal']= mysql_numrows($tables);
            $items['dbtables']  = $dbtables;
            $items['dbname']    = $dbname;

    }
   //Return data for display
   return $items;
}
?>