<?php

/**
 * File: $Id$
 *
 * delta view function for bkview
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

include_once("modules/bkview/xarincludes/bk.class.inc");

function bkview_user_deltaview($args) 
{
    xarVarFetch('repoid','id',$repoid);
    xarVarFetch('rev','str::',$rev,'+');
    extract($args);

    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back
    
    $data=array();
    $data['deltalist']=array();
    $deltalist=array();
    $counter=1;
    $formatstring="':TAG:|:GFILE:|:REV:|:D:|:T:|:USER:|:DOMAIN:|\$each(:C:){(:C:)<br />}'";
    $repo = new bkRepo($item['repopath']);
    $changeset= new bkChangeSet($repo,$rev);
    $deltas=$changeset->bkDeltas($formatstring);
    while (list($key,$val) = each($deltas)){
        list($tag,$file,$revision,$date,$time,$user,$domain,$comments)= explode('|',$val);
        $deltalist[$counter]['tag']=$tag;
        $deltalist[$counter]['file']=$file;
        $deltalist[$counter]['revision']=$revision;
        $deltalist[$counter]['date']=$date;
        $deltalist[$counter]['time']=$time;
        $deltalist[$counter]['user']=$user;
        $deltalist[$counter]['domain']=$domain;
        $deltalist[$counter]['comments']=$comments;
        $counter++;
    }
    
    // Pass data to BL compiler
    $data['pageinfo']=xarML("Changeset details for #(1)",$rev);
    $data['rev']=$rev;
    $data['repoid']=$repoid;
    $data['deltalist']=$deltalist;
    $data['name_value']=$item['reponame'];
    return $data;
}

?>