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
    if(!xarVarFetch('branch','str::',$branch,'',XARVAR_NOT_REQUIRED)) return;
    extract($args);
    $data=array();
        
    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    if($item['repotype'] == 2 && $branch=='') {
        xarResponseRedirect(xarModUrl('bkview','user','branchview',array('repoid'=>$repoid)));
    }
    $repo =& $item['repo'];

    $flags = ($sort * SCM_FLAG_FORWARD) + ($showmerge * SCM_FLAG_SHOWMERGE) + ($taggedonly * SCM_FLAG_TAGGEDONLY);
    $csetlist =& $repo->ChangeSets($user, $range, $flags,$branch);

    $icon = xarModAPIFunc('bkview','user','geticon', array('file' => $repo->_root . '/ChangeSet'));
    
    $csets = array();
    foreach($csetlist as $rev => $changeset) {
        $changeset->repoid = $repoid;
        $changeset->icon = $icon;
        $csets[$rev] = (array) $changeset;
    }

    // Pass data to BL compiler
    $rangetext = scmRepo::RangeToText($range);
    $t = $taggedonly?1:0; $u=($user!='')?1:0; $b=($branch!='')?1:0;
    $tub=$t.$u.$b;
    switch($tub) {
    case '000': // all csets, no user given, no branch given
        $data['pageinfo'] = xarML('Changeset summaries #(1)',$rangetext);
        break;
    case '001': // all csets, no user given, branch specified
        $data['pageinfo'] = xarML('Changeset summaries #(1) on branch #(2)',$rangetext, $branch);
        break;
    case '010': // all csets, user specified, no branch given
        $data['pageinfo'] = xarML('Changesets #(1) by #(2)',$rangetext, $user);
        break;
    case '011': // all csets, user specified, branch given
        $data['pageinfo'] = xarML('Changesets #(1) by #(2) on branch #(3)',$rangetext, $user, $branch);
        break;
    case '100': // only tagged csets, no user given, no branch given
        $data['pageinfo'] = xarML('Tagged changesets #(1)',$rangetext);
        break;
    case '101': // only tagged csets, no user given, branch specified
        $data['pageinfo'] = xarML('Tagged changesets #(1) on branch #(2)',$rangetext,$branch);
        break;
    case '110': // only tagged csets, user specified, no branch given
        $data['pageinfo'] = xarML('Tagged changesets #(1) by #(2)',$rangetext, $user);
        break;
    case '111': // grand finale
        $data['pageinfo'] = xarML('Tagged changeset #(1) by #(2) on branch #(3)',$rangetext, $user, $branch);
        break;
    default:
        // Base text
        $data['pageinfo'] = xarML('Changeset summaries');
    }

    $data['showmerge']  = $showmerge;
    $data['taggedonly'] = $taggedonly;
    $data['range']      = $range;
    $data['csets']      = $csets;
    $data['name_value'] = $item['reponame'];
    $data['repoid']     = $repoid;
    $data['branch']     = $branch;
    return $data;
}
?>