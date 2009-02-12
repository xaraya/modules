<?php

function dossier_relationships_create($args)
{
    extract($args);
    
    if (!xarVarFetch('contactid', 'id', $contactid, $contactid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('connectedid', 'id', $connectedid, $connectedid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('relationship', 'str::', $relationship, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('bidirectional', 'str::', $bidirectional, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('relationship2', 'str::', $relationship2, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('private', 'str::', $private, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('private2', 'str::', $private2, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, $returnurl, XARVAR_NOT_REQUIRED)) return;
    
    if(empty($returnurl)) $returnurl = xarModURL('dossier', 'admin', 'display', array('contactid' => $contactid, 'mode' => "relationshipS"));
                                            
    if (!xarSecConfirmAuthKey()) return;

    $relationshipid = xarModAPIFunc('dossier',
                        'relationships',
                        'create',
                        array('contactid'    => $contactid,
                            'connectedid'        => $connectedid,
                            'relationship'        => $relationship,
                            'private'        => $private,
                            'notes'          => $notes));


    if (!isset($relationshipid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('RELATIONSHIPCREATED'));
    
    if($bidirectional) {
        $relationshipid = xarModAPIFunc('dossier',
                            'relationships',
                            'create',
                            array('contactid'    => $connectedid,
                                'connectedid'        => $contactid,
                                'relationship'        => $relationship2,
                                'private'        => $private2,
                                'notes'          => $notes));
    
    
        if (!isset($relationshipid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    }

    xarResponseRedirect($returnurl);

    return true;
}

?>
