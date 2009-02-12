<?php

function dossier_locations_new($args)
{
    extract($args);

    if (!xarVarFetch('contactid',     'id',     $contactid,     $contactid,     XARVAR_NOT_REQUIRED)) return;

    $data = array();

    if (!xarSecurityCheck('PublicDossierAccess')) {
        return;
    }

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $data['authid'] = xarSecGenAuthKey();
    $data['contactid'] = $contactid;

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add Location'));

    return $data;
}

?>
