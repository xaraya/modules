<?php

/**
 * File: $Id$
 *
 * delta view function for bkview
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function bkview_user_deltaview($args) 
{
    xarVarFetch('repoid','id',$repoid);
    xarVarFetch('rev','str::',$rev,'+');
    extract($args);

    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    
    $data=array();
    $data['deltalist']=array();
    $deltalist=array();
    $counter=1;
    $formatstring="':TAG:|:GFILE:|:REV:|:D:|:T:|:USER:|:DOMAIN:|\$each(:C:){(:C:)".BK_NEWLINE_MARKER."}'";
    $repo =& $item['repo'];
    // FIXME: do we need to do this as a method of $repo ? Now it's really a coincedence it works
    $changeset= new bkChangeSet($repo,$rev);
    $deltas=$changeset->bkDeltas($formatstring);
    while (list($key,$val) = each($deltas)){
        // FIXME: if comments contain a | only the part before it is shown
        // (example: exclude csets )
        list($tag,$file,$revision,$date,$time,$user,$domain,$comments)= explode('|',$val);
        $deltalist[$counter]['tag']=$tag;
        $deltalist[$counter]['file']=$file;
        $deltalist[$counter]['revision']=$revision;
        $deltalist[$counter]['date']=$date;
        $deltalist[$counter]['time']=$time;
        $deltalist[$counter]['user']=$user;
        $deltalist[$counter]['domain']=$domain;
        $comments = str_replace(BK_NEWLINE_MARKER,"\n",$comments);
        $deltalist[$counter]['comments']=nl2br(xarVarPrepForDisplay($comments));
        $counter++;
    }

    $hooks='';
    // We have to construct an artificial $hookId because we don't use the database
    // 1. It needs to include the identification of the repository: ROOTKEY
    //    example : mrb@duel.hsdev.com|ChangeSet|20020928140945|52607|ce70d3e6fd7d585b
    // 2. It needs to include the identification of the changeset: KEY
    //    example:
    //    - mrb@duel.hsdev.com|ChangeSet|20020928140946|22731
    // 3. Can we use the cset number for something? 1.xxx.xxx.xxx.xxx problem with cset numbers
    //    is that they can change on merges, so we can't rely on them
    // And we have to squeeze all this info in a 11 digit integer
    
    //  $hooks = xarModCallHooks('item', 'display', $hookId, $extraInfo, 'bkview', 'changeset')
    
    // Pass data to BL compiler
    $data['pageinfo']=xarML("Changeset details for #(1)",$rev);
    $data['rev']=$rev;
    $data['repoid']=$repoid;
    $data['deltalist']=$deltalist;
    $data['name_value']=$item['reponame'];
    $data['hooks'] = $hooks;
    return $data;
}

?>