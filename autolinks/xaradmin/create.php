<?php

/**
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('autolinks','admin','new') to create a new item
 * @param 'keyword' the keyword of the link to be created
 * @param 'title' the title of the link to be created
 * @param 'url' the url of the link to be created
 * @param 'comment' the comment of the link to be created
 */
function autolinks_admin_create()
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('keyword', 'str:1', $keyword)) return;
    if (!xarVarFetch('title',   'str:1', $title)) return;
    if (!xarVarFetch('url',     'str:1', $url)) return;
    if (!xarVarFetch('comment', 'isset', $comment, NULL, XARVAR_DONT_SET)) return;

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // The API function is called
    $lid = xarModAPIFunc('autolinks',
                        'admin',
                        'create',
                        array('keyword' => $keyword,
                              'title' => $title,
                              'url' => $url,
                              'comment' => $comment));

    if (!$lid) return;

    xarResponseRedirect(xarModURL('autolinks', 'admin', 'view'));

    // Return
    return true;
}

?>
