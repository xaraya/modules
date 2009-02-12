<?php

function dossier_relationships_delete($args)
{
    extract($args);
    
    if (!xarVarFetch('relationshipid', 'id', $relationshipid)) return;
    if (!xarVarFetch('confirm', 'isset', $confirm, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, $returnurl, XARVAR_NOT_REQUIRED)) return;
    
    $item = xarModAPIFunc('dossier',
                         'relationships',
                         'get',
                         array('relationshipid' => $relationshipid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;


    if (!xarSecurityCheck('MyDossierLog', 1, 'Log', "All:All:All:All")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'xtasks', xarVarPrepForDisplay($workrelationshipid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    if (empty($confirm)) {

        $contactinfo = xarModAPIFunc('dossier',
                              'user',
                              'get',
                              array('contactid' => $item['contactid']));
        
        $data = xarModAPIFunc('dossier','admin','menu');

        $data['item'] = $item;
        $data['relationshipid'] = $relationshipid;
        $data['contactid'] = $item['contactid'];
        $data['contactinfo'] = $contactinfo;
        $data['returnurl'] = $returnurl;
        
        $data['confirmbutton'] = xarML('Confirm');

        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    if (!xarModAPIFunc('dossier',
                     'relationships',
                     'delete',
                     array('relationshipid' => $relationshipid))) {
        return;
    }
    xarSessionSetVar('statusmsg', xarML('Workrelationship Record Deleted'));

    xarResponseRedirect(xarModURL('dossier', 'admin', 'display', array('contactid' => $item['contactid'], 'mode' => "contactrelationship")));

    return true;
}

?>
