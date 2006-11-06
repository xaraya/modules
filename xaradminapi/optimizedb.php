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
    if ($dbtype=='' || !isset($dbtype)){
    $dbtype='mysql';
    }

    // Security check  - allow scheduler api funcs to run as anon bug #2802
    // if (!xarSecurityCheck('AdminSiteTools')) return;
       $items=array();

    if (($dbname='') || (empty($dbname))){
        $dbconn =& xarDBGetConn();
            $dbname= xarDBGetName();
    }

    $rowinfo=array();//bug #2595
  // Instantiation of SiteTools class

     include_once("modules/sitetools/xarclass/dbSiteTools_".$dbtype.".php");

     $classname="dbSiteTools_".$dbtype;
     $items= new $classname();
     if (!$rowdata= $items->_optimize($dbname)) {return;}

/** Move all this to db specific classes.
 ** Remove it when we have cleaned up a little and know it is working without a hitch
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
                if (!$resultat) {return false;} //TODO: fix bug # 2594 but need still clean up here
            }
        }


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
*/
    //return
   return $rowdata;
}
?>