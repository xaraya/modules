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
    xarVarFetch('repoid','id',$repoid);
    xarVarFetch('rev','str::',$rev,'1.0');
    xarVarFetch('file','str::',$file,'ChangeSet');
    extract($args);

    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $repo= $item['repo'];

    // Check that rev and file are properly set
    $csetrev=$repo->bkChangeSet($file,$rev);
    $changeset= new bkChangeSet($repo,$csetrev);
    $delta = new bkDelta($changeset,$file,$rev);
      
    $data['age']=$delta->_age;
    $data['author']=$delta->_author;
    $data['domain']=$delta->_domain;
    $data['rev']=$delta->_rev;
    $data['comments']=$delta->_comments;
    
    // Show differences for this file and revision
    $diffs=$delta->bkDiffs();
    //print_r($diffs);
    $counter=1;
    $difflines=array();
    $data['difflines']=array();
    while (list($nr,$line) = each($diffs)) {
        if (strlen($line) >0) {
            if ($line[0]=='+') 
                $difflines[$counter]['difflineclass']='diffadded';
            elseif ($line[0]=='-') 
                $difflines[$counter]['difflineclass']='diffremoved';
            else 
                $difflines[$counter]['difflineclass']='diffnochange';
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