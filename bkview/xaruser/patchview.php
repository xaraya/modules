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
        $csetrev = $repo->bkChangeSet($file,$rev);
    } else {
        $csetrev = $rev;
    }
    $changeset= new bkChangeSet($repo,$csetrev);
    
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
            
            if(xarModIsAvailable('mime') && $delta->checkedout) {
                $mime_type = xarModAPIFunc('mime','user','analyze_file',array('fileName' => $repo->_root . '/' . $delta->file));
                $delta->icon = xarModApiFunc('mime','user','get_mime_image',array('mimeType' => $mime_type));
            } else {
                $delta->icon = xarTplGetImage('file.gif','bkview');
            }
            $deltalist[$counter] = (array) $delta;
            
            $diff=$delta->bkDiffs();
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
    $data['file'] = $file;
    $data['pageinfo']=xarML("Diffs for #(1) revision #(2)",$file, $rev);
    $data['repoid']=$repoid;
    $data['name_value']=$item['reponame'];
    $data['deltalist']=$deltalist;
    $data['cset']['file'] = 'ChangeSet';
    $data['cset']['repoid'] = $repoid;
    $data['cset']['rev'] = $rev;
    $data['cset']['age'] = $changeset->age;
    $data['cset']['range'] = bkAgeToRangeCode($changeset->age);
    $data['cset']['author'] = $changeset->author;
    $data['cset']['comments'] = nl2br(xarVarPrepForDisplay($changeset->bkGetComments()));
    $data['cset']['tag'] = $changeset->tag;
    return $data;
}

?>