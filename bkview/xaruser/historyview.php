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

function bkview_user_historyview($args)
{
    if(!xarVarFetch('repoid','id',$repoid)) return;
    if(!xarVarFetch('file','str::',$file,'ChangeSet')) return;
    if(!xarVarFetch('user','str::',$user,'')) return;
    extract($args);

    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $repo =& $item['repo'];

    $the_file=new bkFile($repo,$file);
    
    if(xarModIsAvailable('mime') && file_exists($the_file->bkAbsoluteName())) {
        $mime_type = xarModAPIFunc('mime','user','analyze_file',array('fileName' => $the_file->bkAbsoluteName()));
        $icon = xarModApiFunc('mime','user','get_mime_image',array('mimeType' => $mime_type));
        $checkedout = true;
    } else {
        $icon = xarTplGetImage('file.gif','bkview');
        $checkedout = false;
    }
    
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
        $histlist[$counter]['author']=$author;
        $histlist[$counter]['rev']=$filerev;
        $histlist[$counter]['comments']=xarVarPrepForDisplay($comments);
        $histlist[$counter]['repoid'] = $repoid;
        $histlist[$counter]['file'] = $file;
        $histlist[$counter]['icon'] = $icon;
        $histlist[$counter]['checkedout'] = $checkedout;
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