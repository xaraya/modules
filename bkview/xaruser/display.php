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
    xarVarFetch('repoid','id',$repoid,null, XARVAR_NOT_REQUIRED);
    xarVarFetch('itemid','id',$repoid);
    xarVarFetch('user','str::',$user,'',XARVAR_NOT_REQUIRED);
    extract($args);

    // Prepare the variable that will hold some status message if necessary
    $data['status'] = '';
    
    $item = xarModAPIFunc('bkview', 'user','get', array('repoid' => $repoid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    
    $data['name_value'] = $item['reponame'];
    $data['repoid'] = xarVarPrepForDisplay($repoid);
    $repo =& $item['repo'];

    // Now construct the count array
    // FIXME: Do mktime and bk range agree on what time is??? (especially in first 4 entries this is sensitive)
    $times=array('1h' => date("YmdHis",mktime(date("H")-1,date("i"),date("s"),date("m"),date("d"),date("Y"))),
                 '2h' => date("YmdHis",mktime(date("H")-2,date("i"),date("s"),date("m"),date("d"),date("Y"))),
                 '4h' => date("YmdHis",mktime(date("H")-4,date("i"),date("s"),date("m"),date("d"),date("Y"))),
                 '1d' => date("YmdHis",mktime(date("H"),date("i"),date("s"),date("m"),date("d")-1,date("Y"))),
                 '2d' => date("YmdHis",mktime(date("H"),date("i"),date("s"),date("m"),date("d")-2,date("Y"))),
                 '3d' => date("YmdHis",mktime(date("H"),date("i"),date("s"),date("m"),date("d")-3,date("Y"))),
                 '4d' => date("YmdHis",mktime(date("H"),date("i"),date("s"),date("m"),date("d")-4,date("Y"))),
                 '1w' => date("YmdHis",mktime(date("H"),date("i"),date("s"),date("m"),date("d")-7,date("Y"))),
                 '2w' => date("YmdHis",mktime(date("H"),date("i"),date("s"),date("m"),date("d")-14,date("Y"))),
                 '3w' => date("YmdHis",mktime(date("H"),date("i"),date("s"),date("m"),date("d")-21,date("Y"))),
                 '4w' => date("YmdHis",mktime(date("H"),date("i"),date("s"),date("m"),date("d")-28,date("Y"))),
                 '8w' => date("YmdHis",mktime(date("H"),date("i"),date("s"),date("m"),date("d")-56,date("Y"))),
                 '12w'=> date("YmdHis",mktime(date("H"),date("i"),date("s"),date("m"),date("d")-72,date("Y"))),
                 '6M' => date("YmdHis",mktime(date("H"),date("i"),date("s"),date("m")-6,date("d"),date("Y"))),
                 '9M' => date("YmdHis",mktime(date("H"),date("i"),date("s"),date("m")-9,date("d"),date("Y"))),
                 '1y' => date("YmdHis",mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y")-1)),
                 '2y' => date("YmdHis",mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y")-2)),
                 '3y' => date("YmdHis",mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y")-3))
                 );

    // Get a time sorted array of all csets
    $stats = $repo->bkGetStats($user);

    // The total number of csets is easy, just count
    $allsets = array_sum(array_count_values($stats))-1;
    $mrgsets=$allsets - $repo->bkCountChangeSets('',false,$user);

    // Now we need to count the number of entries in the slices defined by the times array
    $rangetext = array();
    $correctwith = 0;
    $savesets =0;
    foreach($times as $rangecode => $timestamp) {
        if(!array_key_exists($timestamp,$stats)) {
            // This will be almost always i guess, chance that the timestamp key exists is rather minimal
            $stats[$timestamp] ="BUG! this shouldn't be visible";
            $correctwith++;
            krsort($stats);
        }
        $keys = array_keys($stats);
        $cutoff = array_search($timestamp,$keys);
        $nrofsets = array_sum(array_count_values(array_slice($stats,0,$cutoff)))-$correctwith;
        if(($nrofsets > 0) && ($nrofsets != $savesets)) {
            $rangetext[$rangecode] = xarML('#(1) Changesets #(2)',$nrofsets, bkRangeToText("-$rangecode"));
        }
        $savesets = $nrofsets;
    }
    
    // Deliver the data to BL compiler
    // Call the generic item search hook (this page is the overall search page)
    $data['hooks'] = xarModCallHooks('item','search',$repoid,array());
    
    if($user == '') {
        $data['pageinfo']=xarML("Changeset activity ");
    } else {
        $data['pageinfo']=xarML("Changeset activity for #(1)",$user);
    }
    $data['user'] =$user;
    $data['rangetext'] = $rangetext;
    $data['allsets']=$allsets;
    $data['mrgsets']=$mrgsets;
    return $data;
}

?>