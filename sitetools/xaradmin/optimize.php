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
 * @author jojodee <http://xaraya.athomeandabout.com >
*/

/**
 * Optimize tables in your database
 */
function sitetools_admin_optimize()
{
   if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    // Security check
    if (!xarSecurityCheck('AdminSiteTools')) return;
    // Check for confirmation.
    if (empty($confirm)) {
        // No confirmation yet - display a suitable form to obtain confirmation
        // of this action from the user
    $data['optimized']=false;
         // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();
       // Return the template variables defined in this function
        return $data;
    }
    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    $data['optimized']=true;

    list($dbconn) = xarDBGetConn();
        $dbname= xarDBGetName();
        $data['dbname'] =$dbname;
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

        foreach ($rowdata as $datum) {
            $total = $datum['totaldata'] + $datum['totalidx'];
            $total = $total/1024;
            $total = round($total,3);
            $gain  = $datum['gain']/1024;
            $total_gain += $gain;
            $total_kbs += $total;
            $gain  = round ($gain,3);
            $data['tabledat'][]=array('total' => $total,
                                      'gain'  => $gain,
                                      'tablename' => $datum['rowname']);
       }
       $total_gain = round ($total_gain,3);
       $total_kbs  = round ($total_kbs,3);
       $data['totalgain'] = $total_gain;
       $data['totalkbs']=$total_kbs;

       //Add this new optimization record to the database
       $optid = xarModAPIFunc('sitetools',
                              'admin',
                              'create',
                              array('totalgain' => $total_gain));

      if (!isset($optid) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back

       //get total number of times this script has run and total kbs
      $items = xarModAPIFunc('sitetools', 'admin', 'getall');
       $gaintd=0;
       $runtimes=0;
       foreach ($items as $item) {
            $gaintd += $item['stgain'];
            $runtimes += 1;
       }

       $data['totalruns']=$runtimes;
       $data['gaintd']=round ($gaintd,3);

    //return
    return $data;
}
?>
