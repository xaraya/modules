<?php

function xarpages_admin_deletepage()
{
    if (!xarVarFetch('pid', 'id', $pid)) return;
    if (!xarVarFetch('confirm', 'str:1', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    // Security check
    //if (!xarSecurityCheck('EditSurvey', 1, 'Survey', 'All')) {
    //    // No privilege for editing survey structures.
    //    return false;
    //}

    // Get page information
    $page = xarModAPIFunc(
        'xarpages', 'user', 'getpage',
        array('pid' => $pid)
    );

    if (empty($page)) {
        $msg = xarML('The page #(1) to be deleted does not exist', $pid);
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Check for confirmation
    if (empty($confirm)) {
        $data = array('page' => $page);
        $data['authkey'] = xarSecGenAuthKey();

        $data['count'] = xarModAPIfunc(
            'xarpages', 'user', 'getpages',
            array('count' => true, 'left_range' => array($page['left']+1, $page['right']-1))
        );

        // Return output
        return $data;
    }

    // Confirm Auth Key
    if (!xarSecConfirmAuthKey()) {return;}

    // Pass to API
    if (!xarModAPIFunc(
        'xarpages', 'admin', 'deletepage',
        array('pid' => $pid))
    ) return;

    xarResponseRedirect(xarModURL('xarpages', 'admin', 'viewpages'));

    return true;
}

?>