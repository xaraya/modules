<?php

/**
 * File: $Id$
 *
 * Short description of purpose of file
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

include_once("modules/bkview/xarincludes/bk.class.php");

function bkview_user_csetview($args) 
{
    xarVarFetch('repoid','id',$repoid);
    xarVarFetch('range','str::',$range,NULL,XARVAR_NOT_REQUIRED);
    xarVarFetch('revs','str::',$revs,NULL,XARVAR_NOT_REQUIRED);
    xarVarFetch('showmerge','int:0:1',$showmerge,0);
    xarVarFetch('sort','str::',$sort,0);
    xarVarFetch('user','str::',$user,'',XARVAR_NOT_REQUIRED);

    extract($args);
    $data=array();
        
    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back
    
    $repo = new bkRepo($item['repopath']);
    $formatstring = "':TAG:|:AGE:|:P:|:REV:|\$each(:C:){(:C:)<br/>}'";
    $list = $repo->bkChangeSets($revs,$range,$formatstring,$showmerge,$sort,$user);

    $counter=1;
    $data['csets']=array();
    $csets=array();
    while (list($key,$val) = each($list)) {
        list($tag,$age, $author, $rev, $comments) = explode('|',$val);
        $comments=str_replace("<br/>","\n",$comments);
        $comments=nl2br(htmlspecialchars($comments));
        $csets[$counter]['tag']=$tag;
        $csets[$counter]['age']=$age;
        $csets[$counter]['author']=$author;
        $csets[$counter]['rev']=$rev;
        $csets[$counter]['comments']=$comments;
        $counter++;
    }

    // Pass data to BL compiler
    $rangetext = bkRangeToText($range);
    if($user == '') {
        $data['pageinfo']=xarML("Changeset summaries #(1)",$rangetext);
    } else {
        $data['pageinfo']=xarML("Changeset summaries #(1) by #(2)",$rangetext,$user);
        $data['user'] = $user;
    }
    $data['csets']=$csets;
    $data['name_value'] = $item['reponame'];
    $data['repoid']=$repoid;
    return $data;
}