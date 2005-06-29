<?php


/**
 * File: $Id$
 *
 * history view for bkview module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @link  link to where more info can be found
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function bkview_user_historyview($args)
{
    if(!xarVarFetch('file','str::',$file,'ChangeSet')) return;
    if(!xarVarFetch('repoid','id',$repoid)) return;
    if($file == 'ChangeSet') {
        // We really dont wanna be here at all, the history of the ChangeSet file is handled
        // somewhere else (mainly because it is huge)
        return xarModFunc('bkview','user','display');
    }
    if(!xarVarFetch('user','str::',$user,'')) return;
    extract($args);


    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $repo =& $item['repo'];

    $the_file= $repo->getFile($file);//new bkFile($repo,$file);
    
    $icon = xarModAPIFunc('bkview','user','geticon', array('file' => $the_file->AbsoluteName()));
    // Get an array of delta's
    $history= $the_file->History($user);
    foreach($history as $rev => $delta) {
        $delta->repoid = $repoid;
        $delta->icon = $icon;
        $histlist[$rev] = (array) $delta;
    }

    // Return data to BL
    if($user != '') {
        $data['pageinfo'] = xarML('Revision history for #(1) by #(2)',$file, $user);
        $data['user'] = $user;
    } else {
        $data['pageinfo']=xarML("Revision history for #(1)",$file);
    }
    $data['histlist']   = $histlist;
    $data['name_value'] = $item['reponame'];
    $data['repoid']     = $repoid;
    $data['file']       = $file;
    return $data;
}
?>