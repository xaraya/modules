<?php

function labaccounting_journals_new($args)
{
    extract($args);

    if (!xarVarFetch('inline', 'str', $inline, $inline, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('owneruid', 'int', $owneruid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('agentuid', 'int', $agentuid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('contactid', 'int', $contactid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('journaltype', 'str', $journaltype, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str', $returnurl, 0, XARVAR_NOT_REQUIRED)) return;
    
    $data = xarModAPIFunc('labaccounting','admin','menu');

    if (!xarSecurityCheck('AddJournal')) {
        return;
    }
    
    if(!empty($returnurl)) {
        $data['returnurl'] = $returnurl;
    } else {
        $data['returnurl'] = xarServerGetVar('HTTP_REFERER');
    }

    $data['authid'] = xarSecGenAuthKey();
    $data['inline'] = $inline;
    $data['owneruid'] = $owneruid ? $owneruid : xarSessionGetVar('uid');
    $data['contactid'] = $contactid;
    $data['journaltype'] = $journaltype;
    $data['agentuid'] = $agentuid ? $agentuid : xarSessionGetVar('uid');
    $data['opendate'] = date("Y-m-d");
    $data['billdate'] = date("Y-m-d");
    $data['invoicefreq'] = 1;
    
    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add'));

    return $data;
}

?>
