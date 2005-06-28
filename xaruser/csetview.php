<?php

/**
 * Create view over multiple csets
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function bkview_user_csetview($args) 
{
    if(!xarVarFetch('repoid','id',$repoid)) return;
    if(!xarVarFetch('range','str::',$range,NULL,XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('showmerge','int:0:1',$showmerge,0)) return;
    if(!xarVarFetch('sort','int:0:1',$sort,0)) return;
    if(!xarVarFetch('user','str::',$user,'',XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('taggedonly','int:0:1',$taggedonly,0,XARVAR_NOT_REQUIRED)) return;

    extract($args);
    $data=array();
        
    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    
    $repo =& $item['repo'];

    $flags = ($sort * BK_FLAG_FORWARD) + ($showmerge * BK_FLAG_SHOWMERGE) + ($taggedonly * BK_FLAG_TAGGEDONLY);
    $csetlist =& $repo->ChangeSets($user, $range, $flags);

    $icon = xarModAPIFunc('bkview','user','geticon', array('file' => $repo->_root . '/ChangeSet'));
    
    $csets = array();
    foreach($csetlist as $rev => $changeset) {
        $changeset->repoid = $repoid;
        $changeset->icon = $icon;
        $csets[$rev] = (array) $changeset;
    }

    // Pass data to BL compiler
    $rangetext = bkRepo::RangeToText($range);
    if($taggedonly) {
        if($user =='') {
            $data['pageinfo'] = xarML("Tagged changesets #(1)",$rangetext);
        } else {
            $data['pageinfo'] = xarML("Tagged changesets #(1) by #(2)", $rangetext, $user);
            $data['user'] = $user;
        }
    } else {
        if($user == '') {
            $data['pageinfo']=xarML("Changeset summaries #(1)",$rangetext);
        } else {
            $data['pageinfo']=xarML("Changeset summaries #(1) by #(2)",$rangetext,$user);
            $data['user'] = $user;
        }
    }

    $data['showmerge']  = $showmerge;
    $data['taggedonly'] = $taggedonly;
    $data['range']      = $range;
    $data['csets']      = $csets;
    $data['name_value'] = $item['reponame'];
    $data['repoid']     = $repoid;
    return $data;
}
?>