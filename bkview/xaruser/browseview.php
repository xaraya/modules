<?php

/**
 * File: $Id$
 *
 * browse view for bkview module
 *
 * @package modules
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

include_once("modules/bkview/xarincludes/bk.class.inc");

function bkview_user_browseview($args)
{
    xarVarFetch('repoid','id',$repoid);
    xarVarFetch('dir','str::',$dir,'/');
    extract($args);
    
    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back
    $repo= new bkRepo($item['repopath']);
    
    $dirlist=$repo->bkDirList($dir);
    $data['dirlist']=array();
    $dirs=array();
    $counter=1;
    while (list(,$name) = each($dirlist)) {
        if ($name!="..") {
            $dirs[$counter]['dirname']=$name;
            $dirs[$counter]['newpath']=$dir.$name;
        } elseif ($dir!="/") {
            $dirs[$counter]['dirname']=" .. ";
            $tmp = join("/",array_slice(split("/",$dir),0,-2));
            $dirs[$counter]['newpath']=$tmp;
        }
        $counter++;
    }
    $filelist=$repo->bkFileList($dir);
    $data['files']=array();
    $files=array();
    $counter=1;
    while (list(,$file) = each($filelist)) {
        list($name,$rev,$author,$age,$comments) = explode('|',$file);
        $files[$counter]['name']=$name;
        $files[$counter]['basename']=basename($name);
        $files[$counter]['rev']=$rev;
        $files[$counter]['author']=$author;
        $files[$counter]['age']=$age;
        $files[$counter]['comments']=$comments;
        $files[$counter]['relfile']=substr($dir,0,strlen($dir)-1)."/".basename($name);
        // FIXME: huge performance penalty for this, make it configurable
        $the_file= new bkFile($repo,$files[$counter]['relfile']);
        $files[$counter]['tag'] = $the_file->bkTag($rev);
        $counter++;
    }
    
    // Return data to BL
    // FIXME: make this dynamic
    $data['imgloc']='modules/bkview/xarimages';
    $data['pageinfo']=xarML("Sourcedirectory: #(1)",$dir);
    $data['dir']=$dir;
    $data['repoid']=$repoid;
    $data['name_value']=$item['reponame'];
    $data['dirlist']=$dirs;
    $data['files']=$files;
    return $data;
}
?>