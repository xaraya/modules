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

function bkview_user_browseview($args)
{
    xarVarFetch('repoid','id',$repoid);
    xarVarFetch('dir','str::',$dir,'/');
    extract($args);
    
    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $repo =& $item['repo'];
    
    $dirlist=$repo->bkDirList($dir);
    asort($dirlist);
   
    $data['dirlist']=array();
    $dirs=array();
    $counter=1;
    $maxlen = 1;
    while (list(,$name) = each($dirlist)) {
        if(strlen($name) > $maxlen) $maxlen = strlen($name);
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
    $data['maxlen'] = 0.8 * $maxlen;
    $filelist=$repo->bkFileList($dir);
    $data['files']=array();
    $files=array();
    $counter=1;
    while (list(,$file) = each($filelist)) {
        list($tag,$name,$rev,$age,$author,$comments) = explode('|',$file);
        $files[$counter]['name']=$name;
        $files[$counter]['basename']=basename($name);
        $files[$counter]['rev']=$rev;
        $files[$counter]['author']=$author;
        $files[$counter]['age']=$age;
        $files[$counter]['age_code'] = bkAgeToRangeCode($age);
        $comments = str_replace(BK_NEWLINE_MARKER,"\n",$comments);
        $files[$counter]['comments']=nl2br(xarVarPrepForDisplay($comments));
        $files[$counter]['relfile']=substr($dir,0,strlen($dir)-1)."/".basename($name);
        // FIXME: huge performance penalty for this, make it configurable
        //$the_file= new bkFile($repo,$files[$counter]['relfile']);
        $files[$counter]['tag'] = $tag;
        $counter++;
    }
    
    // Return data to BL
    // FIXME: make this dynamic
    // $dir is something like /html/modules/bkview/xartemplate/includes/
    $dirtrace = explode('/', $dir); array_pop($dirtrace);array_shift($dirtrace);
    $breadcrumb['[root]']='/'; $sofar ='/';
    $pageinfo = '<a href="'.xarModUrl('bkview','user','browseview',array('repoid'=>$repoid,'dir'=>'/')).'">['.xarML('root').']</a>/';
    foreach($dirtrace as $crumb) {
        $sofar .= $crumb . '/';
        $pageinfo .= '<a href="'.xarModUrl('bkview','user','browseview',array('repoid'=>$repoid,'dir'=>$sofar)).'">'.$crumb.'</a>/';
    }
    
    $data['breadcrumb'] = $breadcrumb;
    $data['imgloc']='modules/bkview/xarimages';
    $data['pageinfo']= $pageinfo;
    $data['dir']=$dir;
    $data['repoid']=$repoid;
    $data['name_value']=$item['reponame'];
    $data['dirlist']=$dirs;
    $data['files']=$files;
    return $data;
}
?>