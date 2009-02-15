<?php

function dossier_locations_modify($args)
{
    extract($args);

    if (!xarVarFetch('contactid',     'id',     $contactid,     $contactid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('locationid',     'id',     $locationid,     $locationid,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('inline',     'str',     $inline,     '',     XARVAR_NOT_REQUIRED)) return;

    $item = xarModAPIFunc('dossier',
                         'locations',
                         'get',
                         array('locationid' => $locationid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    $contactinfo = xarModAPIFunc('dossier',
                        'user',
                        'get',
                        array('contactid' => $contactid));

    if (!isset($contactinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('PublicDossierAccess', 1, 'Contact', $contactinfo['cat_id'].":".$contactinfo['userid'].":".$contactinfo['company'].":".$contactinfo['agentuid'])) {
        return;
    }

    $data = xarModAPIFunc('dossier','admin','menu');

    $data['contactid'] = $contactid;

    $data['locationid'] = $locationid;

    $data['authid'] = xarSecGenAuthKey();

    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update'));

    $item['module'] = 'dossier';

    $data['item'] = $item;

    $data['inline'] = $inline;

    return $data;
}

?>
