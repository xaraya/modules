<?php

/**
 * File: $Id$
 *
 * tag view for bkview module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

include_once("modules/bkview/xarincludes/bk.class.php");

function bkview_user_tagview($args)
{
    xarVarFetch('repoid','id',$repoid);
    extract($args);

    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back
    $repo= new bkRepo($item['repopath']);
        
    $formatstring="'\$if(:TAG:){:AGE:|:TAG:|:REV:|\$each(:C:){(:C:)".BK_NEWLINE_MARKER."}}'";
    $changesets=$repo->bkChangeSets('','',$formatstring,true);
    $data['csetlist']=array();
    $csetlist=array();
    $counter=1;
    while (list(,$cset) = each($changesets)) {
        list($age,$tag,$rev,$comments) = explode('|',$cset);
        $csetlist[$counter]['age']=$age;
        $csetlist[$counter]['tag']=$tag;
        $csetlist[$counter]['rev']=$rev;
        $comments = str_replace(BK_NEWLINE_MARKER,"\n",$comments);
        $csetlist[$counter]['comments']=nl2br(xarVarPrepForDisplay($comments));
        $counter++;

    }
    
    $data['pageinfo']=xarML("Tags");
    $data['name_value']=$item['reponame'];
    $data['repoid']=$repoid;
    $data['csetlist']=$csetlist;
    return $data;
}
