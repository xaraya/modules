<?php

/**
 * File: $Id$
 *
 * statistics view for bkview module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


include_once("modules/bkview/xarincludes/bk.class.php");
    
function bkview_user_statsview($args)
{
    xarVarFetch('repoid','id',$repoid);
    xarVarFetch('user','str::',$user,'',XARVAR_NOT_REQUIRED);

    extract($args);

    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back
    $repo= new bkRepo($item['repopath']);
    
    // Get a sorted array of timestamp=>user combo's
    $stats = $repo->bkGetStats($user);
   
    // :UTC: is like 20021003152103 
    //               yyyymmddhhmmss
    $month_ago = date("YmdHis",mktime(date("H"),date("i"),date("s"),date("m")-1,date("d"),date("Y")));

    // The total number of csets is easy, just count the user values.
    $allcsets = array_count_values($stats);

    // For the recent changes we only need a slice of the array, namely the elements where:
    // $key > $monthago. The stats are sorted from new to old so the slice starts at 0
    if(!array_key_exists($month_ago,$stats)) {
        // This will be almost always i guess
        $stats[$month_ago] ="BUG! this shouldn't be visible";
        krsort($stats);
    }
    $keys = array_keys($stats);
    $cutoff = array_search($month_ago,$keys);
    $recentcsets = array_count_values(array_slice($stats,0,$cutoff));
    
    $data['users']=array();
    $counter=1;
    foreach($allcsets as $user => $total) {
        $results[$counter]=array();
        $results[$counter]['user'] = $user;
        $results[$counter]['allcsets'] = $total;
        $results[$counter]['recentcsets'] = 0;
        if(array_key_exists($user,$recentcsets)) $results[$counter]['recentcsets'] = $recentcsets[$user];
        $counter++;
    }

    $results = array_csort($results,'allcsets',SORT_DESC);
    $data['pageinfo']=xarML("User statistics");
    $data['repoid']=$repoid;
    $data['name_value']=$item['reponame'];
    $data['users']=$results;
    return $data;
}

/**
 * Utility function to sort multidimensional array
 *
 */
function array_csort($marray, $column, $flags) 
{
 foreach ($marray as $row) {
   $sortarr[] = $row[$column];
 }
 array_multisort($sortarr, $flags, $marray);
 return $marray;
}
?>