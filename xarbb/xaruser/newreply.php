<?php

//TODO FInish this function.
function xarbb_user_newreply()
{
    if (!xarVarFetch('tid','int:1:',$tid,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cid','int:1:',$cid,'',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase','str:1:',$phase,'',XARVAR_NOT_REQUIRED)) return;

    // Let's get the title, and check to see if we are 
    if ((!empty($tid)) && (empty($cid))){
        // The user API function is called
        $data = xarModAPIFunc('xarbb',
                              'user',
                              'gettopic',
                              array('tid' => $tid));

        $package['title'] = xarVarPrepForDisplay($data['ttitle']);
        if ($phase == 'quote'){
            $package['text'] = '[quote]'. $data['tpost'] .'[/quote]';
        } elseif ($phase == 'edit') {
            $package['text'] = $data['tpost'];
        }
    } elseif (!empty($cid)){
        // The user API function is called
        $data = xarModAPIFunc('comments',
                              'user',
                              'get_one',
                              array('cid' => $cid));

        foreach ($data as $comment){  
            $package['title'] = xarVarPrepForDisplay($comment['xar_title']);
            if ($phase == 'quote'){
                $package['text'] = '[quote]'. $comment['xar_text'] .'[/quote]';
            } elseif ($phase == 'edit') {
                $package['text'] = $comment['xar_text'];
            }
        }
    }

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
