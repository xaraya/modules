<?php

function dossier_relationships_modify($args)
{
    extract($args);
    
    if (!xarVarFetch('relationshipid',     'id',     $relationshipid)) return;
    if (!xarVarFetch('return_url',     'isset',     $returnurl,     $returnurl,     XARVAR_NOT_REQUIRED)) return;

    if (!xarModAPILoad('dossier', 'user')) return;
    
    $item = xarModAPIFunc('dossier',
                         'relationships',
                         'get',
                         array('relationshipid' => $relationshipid));
    
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('TeamDossierAccess', 1, 'Contact', "All:All:All:All")) {
        return;
    }

    $contactinfo = xarModAPIFunc('dossier',
                          'user',
                          'get',
                          array('contactid' => $item['contactid']));
    
    $data = xarModAPIFunc('dossier','admin','menu');
    
    $data['relationshipid'] = $item['relationshipid'];
    
    $data['authid'] = xarSecGenAuthKey();
    
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));

    $data['item'] = $item;
    
    $data['contactinfo'] = $contactinfo;
    
    $data['returnurl'] = $returnurl;
    
    return $data;
}

?>
