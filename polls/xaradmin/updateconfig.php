<?php

/**
 * Update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function polls_admin_updateconfig()
{
    // Get parameters
    list($barscale,
        $itemsperpage,
        $defaultopts,
        $comments,
        $imggraph,
        $voteinterval,
        $previewresults) = xarVarCleanFromInput('barscale',
                         'itemsperpage',
                         'defaultopts',
                         'comments',
                         'imggraph',
                         'voteinterval',
                         'previewresults');

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Security Check
    if (!xarSecurityCheck('AdminPolls')) {
        return;
    }

    // Check arguments
    if (!is_numeric($barscale) || $barscale <= 0) {
        $msg = xarML("Invalid value for config variable: barscale");
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
    }
    if (strval(intval($itemsperpage)) !== $itemsperpage || $itemsperpage < 1) {
        $msg = xarML("Invalid value for config variable: itemsperpage");
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
    }
    if (strval(intval($defaultopts)) !== $defaultopts || $defaultopts < 2) {
        $msg = xarML("Invalid value for config variable: defaultopts");
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
    }
    if ($comments != 1) {
        $comments = 0;
    }
    if (strval(intval($imggraph)) !== $imggraph || $imggraph < 0 || $imggraph > 3) {
        $msg = xarML("Invalid value for config variable: imggraph");
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
    }
    if (!(($voteinterval == -1) ||
        ($voteinterval == 86400) ||
        ($voteinterval == 604800) ||
        ($voteinterval == 2592000))) {
        $msg = xarML("Invalid value for config variable: voteinterval");
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_DATA',
                       new SystemException($msg));
        return;
    }
    if ($previewresults != 1) {
        $previewresults = 0;
    }

    // update the data

    xarModSetVar('polls', 'barscale', $barscale);
    xarModSetVar('polls', 'itemsperpage', $itemsperpage);
    xarModSetVar('polls', 'defaultopts', $defaultopts);
    xarModSetVar('polls', 'comments', $comments);
    xarModSetVar('polls', 'imggraph', $imggraph);
    xarModSetVar('polls', 'voteinterval', $voteinterval);
    xarModSetVar('polls', 'previewresults', $previewresults);


    xarResponseRedirect(xarModURL('polls', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>
