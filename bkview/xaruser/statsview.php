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
    extract($args);

    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back
    $repo= new bkRepo($item['repopath']);
    
    $stats = $repo->bkGetStats();
    
    $data['users']=array();
    $counter=1;
    
    foreach ($stats as $user => $stat) {
        $userlist[$counter] = array();
        $userlist[$counter]['user']=$user;
        $userlist[$counter]['allcsets']=$stat['nrtotal'];
        $userlist[$counter]['recentcsets']=$stat['nrrecent'];
        $counter++;
    }
    $userlist = array_csort($userlist,'allcsets',SORT_DESC);
    $data['pageinfo']=xarML("User statistics");
    $data['repoid']=$repoid;
    $data['name_value']=$item['reponame'];
    $data['users']=$userlist;
    return $data;
}

/**
 * Utility function to sort multidimensional array
 *
 */
function array_csort($marray, $column, $flags) {
 foreach ($marray as $row) {
   $sortarr[] = $row[$column];
 }
 array_multisort($sortarr, $flags, $marray);
 return $marray;
}
?>