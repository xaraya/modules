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


include_once("modules/bkview/xarincludes/bk.class.inc");
    
function bkview_user_statsview($args)
{
    xarVarFetch('repoid','id',$repoid);
    extract($args);

    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back
    $repo= new bkRepo($item['repopath']);
    
    $list=$repo->bkGetUsers();
    
    $data['users']=array();
    $userlist=array();
    $counter=1;
    
    // My slow machine wasnt going in time retrying allcsets and recentsets
    set_time_limit ( 100 );
    
    foreach ($list as $user) {
        
        $user_csets = $repo->bkGetChangeSets('',true,$user);
        // This is simply too slow...
        // Can´t we get these informations in a quickier way?
        //        $userlist[$counter]['lines']=$repo->bkCountChangedLines($user_csets);
        
        $userlist[$counter] = array();
        $userlist[$counter]['user']=$user;
        $userlist[$counter]['allcsets']=count($user_csets);
        // FIXME: -c and -u options don't go well together, postpone until they do
        $userlist[$counter]['recentcsets']=$repo->bkCountChangeSets('-1M',false,$user);
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