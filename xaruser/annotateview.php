<?php

/**
 * File: $Id$
 *
 * annotated view for a file 
 *
 * @package modules
 * @copyright (C) 2004 The Digital Development Foundation, Inc.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function bkview_user_annotateview($args)
{
    if(!xarVarFetch('file','str::',$file,'ChangeSet')) return;
    if($file == 'ChangeSet') {
        // No annotate of Changeset, display the cset referred to
        return xarModFunc('bkview','user','deltaview');
    }
    if(!xarVarFetch('repoid','id',$repoid)) return;
    if(!xarVarFetch('rev','str::',$rev,'1.0')) return;
    extract($args);

    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $repo =& $item['repo'];

    $delta = new bkDelta($repo, $file, $rev);
    $delta->icon = xarModAPIFunc('bkview','user','geticon',array('file' => $repo->_root . '/' . $delta->file));
    $delta->repoid = $repoid;
    $data['delta'] = (array) $delta;
    
    $annotate = $delta->bkAnnotate();
    $annolines =array();
    $data['annolines']=array();
    $counter=1;
    while (list(,$line) = each($annotate)) {
        $annolines[$counter]['annoline'] = htmlspecialchars("$line\n");
        $counter++;
    }
    
    // Return data to BL compiler
    $data['pageinfo']=xarML("Annotated listing of #(1)@#(2)",$file,$rev);
    $data['repoid']=$repoid;
    $data['name_value']=$item['reponame'];
    $data['annolines']=$annolines;
    return $data;
}

?>