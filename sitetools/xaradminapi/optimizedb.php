<?php
/*
 * File: $Id:
 *
 * Optimize a database
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteTools module
 * @author jojodee <jojodee@xaraya.com>
 * optimize function based on original MySQL_Tools by Michaelius (michaelius@nukeland.de)
*/

/**
 * @Optimize a database
 * @author jojodee@xaraya.com
 * @author original MySQL_Tools by Michaelius (michaelius@nukeland.de)
 * @param database name, the physical database name (optional)
 * @param databaseType database type (optional)
 * @return array $data - table names, optimization state, saved, total save
 */
function sitetools_adminapi_optimizedb($dbname,$dbtype='')
{
	//To do: setup for db type

    // Security check
    if (!xarSecurityCheck('AdminSiteTools')) return;
       $items=array();

    if (($dbname='') || (empty($dbname))){
        list($dbconn) = xarDBGetConn();
            $dbname= xarDBGetName();
    }
    
    switch ($dbtype) {

    default:

        $tot_data = 0;
        $tot_idx = 0;
        $tot_all = 0;
        $total_gain=0;
        $total_kbs =0;
        $local_query = 'SHOW TABLE STATUS FROM '.$dbname;
        $result      = @mysql_query($local_query);

        $rowdata=array();
        if (@mysql_num_rows($result)) {
            while ($row = mysql_fetch_array($result)) {
                    $rowdata[]=array('rowname' => $row[0],
                                     'totaldata'  => $row[5],
                                     'totalidx'   => $row[7],
                                     'gain'       => $row[8]);

                $local_query = 'OPTIMIZE TABLE '.$row[0];
                $resultat  = mysql_query($local_query);
            }
        }
        if (!$resultat) {return false;}

        foreach ($rowdata as $datum) {
            $total = $datum['totaldata'] + $datum['totalidx'];
            $total = $total/1024;
            $total = round($total,3);
            $gain  = $datum['gain']/1024;
            $total_gain += $gain;
            $total_kbs += $total;
            $gain  = round ($gain,3);
            $rowinfo['rowdata'][]=array('total' => $total,
                                         'gain'  => $gain,
                                         'tablename' => $datum['rowname']);
       }
       $items['rowinfo']=$rowinfo['rowdata'];
       $items['total_gain']=$total_gain;
       $items['total_kbs']=$total_kbs;
       $items['dbname']=$dbname;
    }
    //return
   return $items;
}
?>
