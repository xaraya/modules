<?php

function uploads_user_save_attachments($args)
{
    // Get parameters
    if (!xarVarFetch('modname',   'isset', $modname,    NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('itemtype',  'isset', $itemtype,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('objectid',  'isset', $objectid,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('returnurl', 'isset', $returnurl,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('rating',    'isset', $rating,     NULL, XARVAR_DONT_SET)) {return;}

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    $newrating = xarModAPIFunc('ratings',
                              'user',
                              'rate',
                              array('modname'    => $modname,
                                    'itemtype'   => $itemtype,
                                    'objectid'   => $objectid,
                                    'rating'     => $rating));

    xarResponseRedirect($returnurl);

    return true;
}

?>