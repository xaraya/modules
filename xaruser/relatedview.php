<?php

/**
 * File: $Id$
 *
 * related view for bkview module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function bkview_user_relatedview($args)
{
    if(!xarVarFetch('file','str::',$file,'ChangeSet')) return;
    if($file == 'ChangeSet') {
        // A request for other changesets on the ChangeSet file is the overview
        // of changeset itself, go there
        return xarModFunc('bkview','user','display');
    }
    if(!xarVarFetch('repoid','id',$repoid)) return;
    extract($args);

    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    $repo =& $item['repo'];
    $the_file = new bkFile($repo,$file);

    $icon = xarModAPIFunc('bkview','user','geticon',array('file' => $the_file->bkAbsoluteName()));
    
    $changesets=$the_file->bkChangeSets();
    foreach($changesets as $revision => $changeset) {
        $changeset->repoid = $repoid;
        $changeset->icon = $icon;
        $csets[$revision] = (array) $changeset;
    }
    
    // Return data to BL
    $data['pageinfo']   = xarML("Changesets that modify #(1)",$file);
    $data['repoid']     = $repoid;
    $data['name_value'] = $item['reponame'];
    $data['csets']      = $csets;
    return $data;
}

?>