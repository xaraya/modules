<?php

/**
 * File: $Id$
 *
 * patch view for bkview module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function bkview_user_diffview($args)
{
    if(!xarVarFetch('repoid','id',$repoid)) return;
    if(!xarVarFetch('rev','str::',$rev,'1.0')) return;
    if(!xarVarFetch('file','str::',$file,'ChangeSet')) return;
    extract($args);

    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $repo= $item['repo'];

    // Check that rev and file are properly set
    $csetrev=$repo->bkChangeSet($file,$rev);
    $changeset= new bkChangeSet($repo,$csetrev);
    $delta = new bkDelta($changeset,$file,$rev);
      
    if(xarModIsAvailable('mime') && $delta->checkedout) {
        $mime_type = xarModAPIFunc('mime','user','analyze_file',array('fileName' => $repo->_root . '/' . $file));
        $delta->icon = xarModApiFunc('mime','user','get_mime_image',array('mimeType' => $mime_type));
    } else {
        $delta->icon = xarTplGetImage('file.gif','bkview');
    }
    
    $delta->repoid = $repoid;
    $delat->csetrev = $delta->cset->_rev;
    $delta->tag = $changeset->_tag;
    $data['delta'] = (array) $delta;
    
    // Show differences for this file and revision
    $diffs=$delta->bkDiffs();
    //print_r($diffs);
    $counter=1;
    $difflines=array();
    $data['difflines']=array();
    while (list($nr,$line) = each($diffs)) {
        if (strlen($line) >0) {
            if ($line[0]=='+') 
                $difflines[$counter]['difflineclass']='precontent added';
            elseif ($line[0]=='-') 
                $difflines[$counter]['difflineclass']='precontent removed';
            else 
                $difflines[$counter]['difflineclass']='precontent nochange';
            $difflines[$counter]['diffline']= htmlspecialchars("$line\n");
        }
        $counter++;
    }

    $data['pageinfo']=xarML("Changes for #(1)@#(2)",$file,$rev);
    $data['file']=$file;
    $data['rev']=$rev;
    $data['repoid']=$repoid;
    $data['name_value']=$item['reponame'];
    $data['difflines']=$difflines;
    return $data;
}

?>