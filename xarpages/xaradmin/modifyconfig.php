<?php

/*
 * Modify configuration options for the module.
 * TODO: Configure default page, error page (no permission), error page (page not found), short URL support.
 */

function xarpages_admin_modifyconfig()
{
    $data = array();

    // Get the tree of all pages.
    $data['tree'] = xarModAPIfunc('xarpages', 'user', 'getpagestree', array('dd_flag' => false));

    // Implode the names for each page into a path for display.
    foreach ($data['tree']['pages'] as $key => $page) {
        $data['tree']['pages'][$key]['slash_separated'] =  '/' . implode('/', $page['namepath']);
    }

    // Check if we are receiving a submitted form.
    xarVarFetch('authid', 'str', $authid, '', XARVAR_NOT_REQUIRED);

    if (empty($authid)) {
        // First visit to this form (nothing being submitted).

        // Get the current module values.
        $data['defaultpage'] = xarModGetVar('xarpages', 'defaultpage');
        $data['errorpage'] = xarModGetVar('xarpages', 'errorpage');
        $data['notfoundpage'] = xarModGetVar('xarpages', 'notfoundpage');

        $data['shorturl'] = xarModGetVar('xarpages', 'SupportShortURLs');
    } else {
        // Form has been submitted.

        // Confirm authorisation code.
        if (!xarSecConfirmAuthKey()) {return;}

        // Get the special pages.
        foreach(array('defaultpage', 'errorpage', 'notfoundpage') as $special_name) {
            unset($special_id);
            if (!xarVarFetch($special_name, 'id', $special_id, 0, XARVAR_NOT_REQUIRED)) {return;}
            xarModSetVar('xarpages', $special_name, $special_id);

            // Save value for redisplaying in the form.
            $data[$special_name] = $special_id;
        }

        // Short URL flag.
        xarVarFetch('shorturl', 'int:0:1', $shorturl, 0, XARVAR_NOT_REQUIRED);
        xarModSetVar('xarpages', 'SupportShortURLs', $shorturl);
        $data['shorturl'] =$shorturl;
    }

    $data['authid'] = xarSecGenAuthKey();

    return $data;
}

?>