<?php
function headlines_user_view()
{
    // Security Check
    if(!xarSecurityCheck('ReadHeadlines')) return;
    xarVarFetch('hid', 'id', $hid, XARVAR_PREP_FOR_DISPLAY);
    // The user API function is called
    $links = xarModAPIFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));

    if (isset($links['catid'])) {
        $data['catid'] = $links['catid'];
    } else {
        $data['catid'] = '';
    }
    // Check and see if a feed has been supplied to us.
    if(isset($links['url'])) {
        $feedfile = $links['url'];
    } else {
        $feedfile = "";
    }
    // The user API function is called
    $data = xarModAPIFunc('headlines',
                          'user',
                          'process',
                          array('feedfile' => $feedfile));

    xarTplSetPageTitle(xarVarPrepForDisplay($data['chantitle']));

    $data['hid'] = $hid;
    $data['module'] = 'headlines';
    $data['itemtype'] = 0;
    $data['itemid'] = $hid;
    $data['returnurl'] = xarModURL('headlines',
                                   'user',
                                   'view',
                                   array('hid' => $hid));
    $hooks = xarModCallHooks('item', 'display', $hid, $data);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}
?>