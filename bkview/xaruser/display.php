<?php

/**
 * File: $Id$
 *
 * display function for bkview
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@hsdev.com>
*/

include_once("modules/bkview/xarincludes/bk.class.php");


/**
 * display an item
 * This is a standard function to provide detailed informtion on a single item
 * available from the module.
 *
 * @param $args an array of arguments (if called by other modules)
 * @param $args['objectid'] a generic object id (if called by other modules)
 * @param $args['repoid'] the item id used for this bkview module
 */
function bkview_user_display($args)
{
    xarVarFetch('repoid','id',$repoid);
    xarVarFetch('user','str::',$user,'',XARVAR_NOT_REQUIRED);
    extract($args);

    // Prepare the variable that will hold some status message if necessary
    $data['status'] = '';
    
    $item = xarModAPIFunc('bkview', 'user','get', array('repoid' => $repoid));
    if (!isset($item) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back
    
    $data['name_value'] = $item['reponame'];
    $data['repoid'] = xarVarPrepForDisplay($repoid);
    
    $repo = new bkRepo($item['repopath']);
    // Now construct the count array
    $times=array('1h','2h','4h','1d','2d','3d','4d','1w','2w','3w','4w','8w','12w','6M','9M','1y','2y','3y');
    $csets=array();
    while (list(,$time) = each($times)) {
        $csets[$time]=$repo->bkCountChangeSets("-$time",false,$user);
    }
    $csets=array_unique($csets);
    // Now we have the array, construct the ML texts for them
    $rangetext=array();
    while (list($time,$nrofcsets) = each($csets)) {
        if ($nrofcsets !=0 ) {
            $rangetext[$time] = xarML('#(1) Changesets #(2)',$nrofcsets,bkRangeToText("-$time"));
        }
    }
    $allsets=$repo->bkCountChangeSets('',true,$user);
    $mrgsets=$allsets - $repo->bkCountChangeSets('',false,$user);
    
    // Call the generic item search hook (this page is the overall search page)
    $data['hooks'] = xarModCallHooks('item','search',$repoid,array());

    
    // Deliver the data to BL compiler
    if($user == '') {
        $data['pageinfo']=xarML("Changeset activity ");
    } else {
        $data['pageinfo']=xarML("Changeset activity for #(1)",$user);
        $data['user'] =$user;
    }
    $data['rangetext'] = $rangetext;
    $data['allsets']=$allsets;
    $data['mrgsets']=$mrgsets;
    return $data;
}

?>