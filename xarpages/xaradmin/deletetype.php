<?php

function xarpages_admin_deletetype()
{
    if (!xarVarFetch('ptid', 'id', $ptid)) return;
    if (!xarVarFetch('confirm', 'str:1', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    // Security check
    //if (!xarSecurityCheck('EditSurvey', 1, 'Survey', 'All')) {
    //    // No privilege for editing survey structures.
    //    return false;
    //}

    // Get page type information
    $type = xarModAPIFunc(
        'xarpages', 'user', 'gettype',
        array('ptid' => $ptid)
    );

    if (empty($type)) {
        $msg = xarML('The page type "#(1)" to be deleted does not exist', $ptid);
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Check for confirmation
    if (empty($confirm)) {
        $data = array('type' => $type);
        $data['authkey'] = xarSecGenAuthKey();

        // Get a count of pages that will also be deleted.
        $data['count'] = xarModAPIfunc(
            'xarpages', 'user', 'getpages',
            array('count' => true, 'itemtype' => $type['ptid'])
        );

        // Return output
        return $data;
    }

    // Confirm Auth Key
    if (!xarSecConfirmAuthKey()) {return;}

    // Pass to API
    if (!xarModAPIFunc(
        'xarpages', 'admin', 'deletetype',
        array('ptid' => $ptid))
    ) return;

    xarResponseRedirect(xarModURL('xarpages', 'admin', 'viewtypes'));

    return true;
}

?>