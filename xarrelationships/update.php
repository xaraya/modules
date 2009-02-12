<?php

function dossier_relationships_update($args)
{
    extract($args);
    
    if (!xarVarFetch('relationshipid', 'id', $relationshipid)) return;
    if (!xarVarFetch('contactid', 'str:1:', $contactid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('connectedid', 'str:1:', $connectedid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('relationship', 'str::', $relationship, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('private', 'checkbox::', $private, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, $returnurl, XARVAR_NOT_REQUIRED)) return;
    
    if(empty($returnurl)) $returnurl = xarModURL('dossier', 'admin', 'display', array('contactid' => $contactid, 'mode' => "relationshipS"));
                                        
    if (!xarSecConfirmAuthKey()) return;
    
    $workrelationship = xarModAPIFunc('dossier', 'relationships', 'get', array('relationshipid' => $relationshipid));
    
    if(!$workrelationship) return;
    
    if(!xarModAPIFunc('dossier',
                    'relationships',
                    'update',
                    array('relationshipid'       => $relationshipid,
                        'connectedid'       => $connectedid,
                        'relationship'       => $relationship,
                        'private'         => $private,
                        'notes'         => $notes))) {
        return;
    }


    xarSessionSetVar('statusmsg', xarML('Contact Record Updated'));
// xarModURL('dossier', 'admin', 'display', array('contactid' => $workrelationship['contactid'], 'mode' => "contactrelationship"))
    xarResponseRedirect($returnurl);

    return true;
}

?>
