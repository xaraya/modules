<?php

function dossier_relationships_new($args)
{
    extract($args);

    if (!xarVarFetch('contactid',     'id',     $contactid,     $contactid)) return;
    if (!xarVarFetch('return_url',     'isset',     $returnurl,     $returnurl,     XARVAR_NOT_REQUIRED)) return;
    
    if (!xarModAPILoad('dossier', 'user')) return;
    
    $data = xarModAPIFunc('dossier','admin','menu');

    if (!xarSecurityCheck('AddDossierLog')) {
        return;
    }

    $contactinfo = xarModAPIFunc('dossier',
                          'user',
                          'get',
                          array('contactid' => $contactid));
                          
    if($contactinfo == false) return;

    $data['authid'] = xarSecGenAuthKey();
    $data['contactid'] = $contactid;
    $data['contactinfo'] = $contactinfo;
    $data['returnurl'] = $returnurl;

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add Note'));

    return $data;
}

?>
