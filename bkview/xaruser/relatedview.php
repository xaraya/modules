<?php

/**
 * File: $Id$
 *
 * related view for bkview module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function bkview_user_relatedview($args)
{
    xarVarFetch('repoid','id',$repoid);
    xarVarFetch('file','str::',$file,'/ChangeSet');
    extract($args);

    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $repov =& $item['repo'];
    $the_file = new bkFile($repo,$file);

    $changesets=$the_file->bkChangeSets();
    // Make the list of changesets into a range
    $revs='';$range='';
    while (list(,$cset) = each($changesets)) {
        $revs.="$cset,";
    }
    
    $revs=substr($revs,0,strlen($revs)-1);
    $formatstring = "':AGE:|:P:|:REV:|\$each(:C:){(:C:)<br />}'";
    $list = $repo->bkChangeSets($revs,$range,$formatstring,false);
    
    $data['csets']=array();
    $csets=array();
    $counter=1;
    while (list($key,$val) = each($list)) {
        list($age, $author, $rev, $comments) = explode('|',$val);
        $csets[$counter]['age']=$age;
        $csets[$counter]['author']=$author;
        $csets[$counter]['rev']=$rev;
        $csets[$counter]['comments']=$comments;
        // $comments=str_replace("<br />","\n",$comments);
        // nl2br(htmlspecialchars($comments))
        $counter++;
    }
    
    // Return data to BL
    $data['pageinfo']=xarML("Changesets that modify #(1)",$file);
    $data['repoid']=$repoid;
    $data['name_value']=$item['reponame'];
    $data['csets']=$csets;
    return $data;
}

?>