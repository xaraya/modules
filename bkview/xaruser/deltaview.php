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

    // Get the information on the repository
    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    
    // Initialize
    $data=array(); $data['deltatree']=array(); $deltatree = array();
    $repo =& $item['repo'];

    // This creates a property array with the deltas in the cset in the cset object
    $changeset= new bkChangeSet($repo,$rev);
    if(!empty($changeset->_deltas)) {
        foreach($changeset->_deltas as $delta_id => $delta) {
            // Repo id is a xaraya thing, add it sneaky to the object because we dont
            // want it in the class
            $delta->repoid = $repoid;
            $arrayindex = '$deltatree[\''. implode("']['",explode('/',$delta->_file)) . "']";
            $type = $arrayindex . "['type']";
            eval("$type = 'file';");
            $arrayindex .= "['".$delta->_rev."']";
            // mwuhahaha
            eval("$arrayindex = \$delta;");
        }
    }   
    //xarLogMessage('deltatree :' . print_r($deltatree,true),XARLOG_LEVEL_WARNING);
    $data['deltatree'] =  array('deltatree' => $deltatree);
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
    // FIXME: this sucks
    $data['pageinfo']=xarML("Changeset details for #(1)",$rev);
    $data['rev']=$rev;
    $data['author'] = $changeset->bkGetAuthor();
    $data['comments'] = $changeset->bkGetComments();
    $data['key'] = $changeset->bkGetKey();
    $data['tag'] = $changeset->bkGetTag();
    $data['repoid']=$repoid;
    $data['name_value']=$item['reponame'];
    $data['hooks'] = $hooks;
    return $data;
}

?>