<?php
/*
 * File: $Id:
 *
 * Site Tools database class
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteTools module
 * @author jojodee <jojodee@xaraya.com>
*/

/**
 * SiteTools Database abstraction class extension for mySQL
 *
 * @author Richard Cave <rcave@xaraya.com>
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @access private
 */
require_once('modules/sitetools/xarclass/dbSiteTools.php');

class dbSiteTools_mysql extends dbSiteTools
{
    function _optimize()
    {
        $local_query = 'SHOW TABLE STATUS FROM '.$this->dbname;
        $result      = @mysql_query($local_query);
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
                                                                                           
        $rowinfo = array();
        foreach ($rowdata as $datum) {
            $total = $datum['totaldata'] + $datum['totalidx'];
            $total = $total/1024;
            $total = round($total,3);
            $gain  = $datum['gain']/1024;
            $rowinfo['total_gain'] += $gain;
            $rowinfo['total_kbs'] += $total;
            $gain  = round ($gain,3);
            $rowinfo['rowdata'][]=array('total' => $total,
                                        'gain'  => $gain,
                                        'tablename' => $datum['rowname']);
        }

        return $rowinfo; 
    }
}

?>
