<?php

//TODO FInish this function.
function xarbb_user_newreply()
{
    if (!xarVarFetch('tid','int:1:',$tid,10,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cid','int:1:',$cid,10,XARVAR_NOT_REQUIRED)) return;

    // Security Check
    if(!xarSecurityCheck('ReadxarBB')) return;


    // Var Set-up
    $header['input-title']  = xarML('Post a Reply');
    $header['modid']        = xarModGetIDFromName('xarbb');
    $header['objectid']     = $tid;

    $receipt['post_url']    = xarModUrl('comments', 'user', 'reply', array('tid' => $tid));
    $receipt['action']      = 'reply';
    $receipt['returnurl']['decoded'] = xarModUrl('xarbb', 'user', 'updatetopic', array('tid' => $tid));
    $receipt['returnurl']['encoded'] = rawurlencode($receipt['returnurl']['decoded']);

    $package['name']        = xarUserGetVar('name');
    $package['uid']         = xarUserGetVar('uid');


    // Form Hooks
    $formhooks = xarbb_user_formhooks();
    $data['hooks']      = $formhooks;
    $data['receipt']    = $receipt;
    $data['package']    = $package;
    $data['header']     = $header;

    return $data;
}


?>
