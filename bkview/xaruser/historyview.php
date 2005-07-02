<?php


/**
 * File: $Id$
 *
 * history view for bkview module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @link  link to where more info can be found
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

include_once("modules/bkview/xarincludes/bk.class.php");

function bkview_user_historyview($args)
{
    xarVarFetch('repoid','id',$repoid);
    xarVarFetch('file','str::',$file,'ChangeSet');
    xarVarFetch('user','str::',$user,'');
    extract($args);

    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $repo= new bkRepo($item['repopath']);

    $the_file=new bkFile($repo,$file);

    $formatstring="'";
    if($user != '') $formatstring .="\$if(:P:=$user){";
    $formatstring .= ":AGE:|:P:|:REV:|\$each(:C:){(:C:)}";
    if($user != '') $formatstring .= "}";
    $formatstring .= "'";
    $history= $the_file->bkHistory($formatstring);
    $data['history']=array();
    $histlist=array();
    $counter=1;
    while (list(,$row) = each($history)) {
        list($age, $author, $filerev, $comments) = explode('|',$row);
        $histlist[$counter]['age']=$age;
        $histlist[$counter]['age_code'] = bkAgeToRangeCode($age);
        $histlist[$counter]['author']=$author;
        $histlist[$counter]['rev']=$filerev;
        $histlist[$counter]['comments']=$comments;
        // This gets the tag for this revision if any
        //bk changes -t -r`bk r2c -r1.2 rfc0028.xml` -d':TAG:'
        $histlist[$counter]['tag']=$the_file->bkTag($filerev);
        $counter++;
    }
    
    // Return data to BL
    if($user != '') {
        $data['pageinfo'] = xarML('Revision history for #(1) by #(2)',$file, $user);
        $data['user'] = $user;
    } else {
        $data['pageinfo']=xarML("Revision history for #(1)",$file);
    }
    $data['histlist']=$histlist;
    $data['name_value']=$item['reponame'];
    $data['repoid']=$repoid;
    $data['file']=$file;
    return $data;
}
?>