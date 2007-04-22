<?php
/**
 * @package modules
 * @copyright (C) 2002-2006 Digital Development Foundation
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * Browse files in a revision
 *
 */
function bkview_user_browseview($args)
{
    if(!xarVarFetch('repoid','id',$repoid)) return;
    if(!xarVarFetch('dir','str::',$dir,'/')) return;
    if(!xarVarFetch('branch','str::',$branch,'',XARVAR_NOT_REQUIRED)) return;
    extract($args);
    
    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    if($item['repotype']==2 && $branch=='') {
        xarResponseRedirect(xarModUrl('bkview','user','branchview'));
    }
    $repo =& $item['repo'];
    
    $dirlist=$repo->dirList($dir,'',$branch);

    asort($dirlist);

    $data['dirlist']=array();
    $dirs=array();
    $counter=1;
    $maxlen = 1;
    while (list(,$name) = each($dirlist)) {
        if (strlen($name) > $maxlen) $maxlen = strlen($name);
        if ($name != '..') {
            $dirs[$counter]['dirname']=$name;
            $dirs[$counter]['newpath']=$dir.$name;
        } elseif ($dir != '/') {
            $dirs[$counter]['dirname']=' .. ';
            $tmp = join("/",array_slice(split('/',$dir),0,-2));
            $dirs[$counter]['newpath']=$tmp;
            
        }
        $dirs[$counter]['url'] = xarModURL('bkview','user','browseview',array('repoid' => $repoid,
            'branch' => $branch, 'dir' => $dirs[$counter]['newpath'] .'/'));
        $counter++;
    }

    $data['maxlen'] = 0.8 * $maxlen;
    $filelist=$repo->fileList($dir,'',$branch);

    $data['files'] = array();
    $files=array();
    $counter=1;
    while (list(,$file) = each($filelist)) {
        list($tag,$name,$rev,$age,$author,$comments) = explode('|',$file);
        $files[$counter]['repoid'] = $repoid;
        $files[$counter]['branch'] = $branch;
        $files[$counter]['rev'] = $rev;
        $files[$counter]['author'] = $author;
        $files[$counter]['age'] = $age;
        $comments = str_replace(BK_NEWLINE_MARKER,"\n",$comments);
        $files[$counter]['comments'] = nl2br(xarVarPrepForDisplay($comments));
        $files[$counter]['file'] = substr($dir,0,strlen($dir)-1)."/".basename($name);
        $files[$counter]['checkedout'] = file_exists($name);
        $files[$counter]['icon'] = xarModAPIFunc('bkview','user','geticon',array('file' => $name));
        $files[$counter]['tag'] = $tag;
        $counter++;
    }
    
    $dirtrace = explode('/', $dir); array_pop($dirtrace);array_shift($dirtrace);
    $breadcrumb['[root]']='/'; 

    $data['sofar']      = '/';
    $data['dirtrace']   = $dirtrace;
    $data['breadcrumb'] = $breadcrumb;
    $data['pageinfo']   = '';
    $data['dir']        = $dir;
    $data['repoid']     = $repoid;
    $data['name_value'] = $item['reponame'];
    $data['dirlist']    = $dirs;
    $data['files']      = $files;
    $data['branch']     = $branch;
    return $data;
}
?>
