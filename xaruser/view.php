<?php
function headlines_user_view()
{
    // Security Check
    if (!xarSecurityCheck('ReadHeadlines')) return;
    if (!xarVarFetch('hid', 'id', $hid)) return;

    // The user API function is called
    $links = xarModAPIFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));
    if (empty($links)) return;

    // Check and see if a feed has been supplied to us.
    if(isset($links['url'])) {
        $feedfile = $links['url'];
    } else {
        $feedfile = "";
    }
    if (xarModGetVar('headlines', 'magpie')){
        $data = xarModAPIFunc('magpie',
                              'user',
                              'process',
                              array('feedfile' => $feedfile));
    } else {
        $data = xarModAPIFunc('headlines',
                              'user',
                              'process',
                              array('feedfile' => $feedfile));
    }

    if (!empty($data['warning'])){
        $msg = xarML('There is a problem with this feed : #(1)', $info['warning']);
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    xarTplSetPageTitle(xarVarPrepForDisplay($data['chantitle']));

    if (isset($links['catid'])) {
        $data['catid'] = $links['catid'];
    } else {
        $data['catid'] = '';
    }
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
    // only generate authid when the user is allowed to import
    $importpubtype = xarModGetVar('headlines','importpubtype');
    if (!empty($importpubtype) && xarSecurityCheck('EditHeadlines', 0)) {
        $data['authid'] = xarSecGenAuthKey();
    } else {
        $data['authid'] = '';
    }
    return $data;
}
?>
