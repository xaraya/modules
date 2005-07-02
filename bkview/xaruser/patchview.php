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

include_once("modules/bkview/xarincludes/bk.class.php");


function bkview_user_patchview($args)
{
    xarVarFetch('repoid','id',$repoid);
    xarVarFetch('rev','str::',$rev,'+');
    extract($args);

    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $repo= new bkRepo($item['repopath']);

    $changeset= new bkChangeSet($repo,$rev);
    
    $data=array();
    $deltalist=array();
    $data['deltalist']=array();
    $counter=1;
    // First get a list of filenames and revisions in this changeset
    $dlist = $changeset->bkDeltaList();
    if(!is_null($dlist)) {
        foreach($dlist as $delta_id => $delta) {
            //while (list($delta_id) = each($dlist)) {
            //$delta=$changeset->bkDelta($delta_id);
            $deltalist[$counter]['file']=$delta->bkFile();
            $deltalist[$counter]['revision']=$delta->bkRev();
            
            $diff=$delta->bkDiffs();
            $deltalist[$counter]['difflines']=array();
            $linecounter=1;
            while(list(,$line) = each($diff)) {
                if (strlen($line) >0) {
                    if ($line[0]=='+') 
                        $deltalist[$counter]['difflines'][$linecounter]['difflineclass']='diffadded';
                    elseif ($line[0]=='-') 
                        $deltalist[$counter]['difflines'][$linecounter]['difflineclass']='diffremoved';
                    else 
                        $deltalist[$counter]['difflines'][$linecounter]['difflineclass']='diffnochange';
                    
                    $deltalist[$counter]['difflines'][$linecounter]['diffline']= htmlspecialchars("$line\n");
                }
                $linecounter++;
            }
            $counter++;
        }
    }
    // Return the data to BL compiler
    $data['pageinfo']=xarML("All diffs for ChangeSet #(1)",$rev);
    $data['repoid']=$repoid;
    $data['name_value']=$item['reponame'];
    $data['deltalist']=$deltalist;
    return $data;
}

?>