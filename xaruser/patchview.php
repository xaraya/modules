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

function bkview_user_patchview($args)
{
    if(!xarVarFetch('repoid','id',$repoid)) return;
    if(!xarVarFetch('rev','str::',$rev,'+')) return;
    if(!xarVarFetch('file','str::',$file,'ChangeSet')) return;
    extract($args);

    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $repo =& $item['repo'];

    if($file != 'ChangeSet') {
        // rev is a delta revision
        $deltarev = $rev;
        $csetrev = $repo->ChangeSet($file,$rev);
    } else {
        $csetrev = $rev;
    }
    $changeset= $repo->getChangeSet($csetrev);
    $changeset->repoid = $repoid;
    $changeset->icon = xarModAPIFunc('bkview','user','geticon', array('file' => $repo->_root . '/ChangeSet'));
    
    $data=array();
    $deltalist=array();
    $data['deltalist']=array();
    $counter=1;
    // First get a list of filenames and revisions in this changeset
    if(isset($deltarev)) {
        $delta = new bkDelta($repo, $file, $deltarev);
        $dlist[$deltarev] = $delta;
    } else {
        $dlist = $changeset->deltas;
    }
    
    if(!is_null($dlist)) {
        foreach($dlist as $delta_id => $delta) {
            $delta->repoid = $repoid;
            $delta->icon = xarModAPIFunc('bkview','user','geticon', array('file' => $repo->_root . '/' . $delta->file));
            $deltalist[$counter] = (array) $delta;
            
            $diff=$delta->Diffs();
            $deltalist[$counter]['difflines']=array();
            $linecounter=1;
            while(list(,$line) = each($diff)) {
                if (strlen($line) >0) {
                    if ($line[0]=='+') 
                        $deltalist[$counter]['difflines'][$linecounter]['difflineclass']='precontent added';
                    elseif ($line[0]=='-') 
                        $deltalist[$counter]['difflines'][$linecounter]['difflineclass']='precontent removed';
                    else 
                        $deltalist[$counter]['difflines'][$linecounter]['difflineclass']='precontent nochange';
                    
                    $deltalist[$counter]['difflines'][$linecounter]['diffline']= htmlspecialchars("$line\n");
                }
                $linecounter++;
            }
            $counter++;
        }
    }
    // Return the data to BL compiler
    $data['file']       = $file;
    $data['pageinfo']   = xarML("Diffs for #(1) revision #(2)",$file, $rev);
    $data['repoid']     = $repoid;
    $data['name_value'] = $item['reponame'];
    $data['deltalist']  = $deltalist;
    $data['cset']       = (array) $changeset;
    return $data;
}

?>