<?php

function dossier_reminders_create($args)
{
    extract($args);
    
    if (!xarVarFetch('contactid', 'id', $contactid, $contactid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notes', 'str::', $notes, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ownerid', 'int::', $ownerid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('reminderdate', 'str::', $reminderdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('warningtime', 'str::', $warningtime, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
                                            
    if (!xarSecConfirmAuthKey()) return;

    if(empty($returnurl)) $returnurl = xarModURL('dossier', 'admin', 'display', array('contactid' => $contactid, 'mode' => "reminders"));
                                            
    $reminderid = xarModAPIFunc('dossier',
                        'reminders',
                        'create',
                        array('contactid'       => $contactid,
                            'notes'             => $notes,
                            'ownerid'           => $ownerid,
                            'warningtime'       => $warningtime,
                            'reminderdate'      => $reminderdate));


    if (!isset($reminderid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('REMINDERCREATED'));

    xarResponseRedirect($returnurl);

    return true;
}

?>
