<?php
/**
 * view items
 */
function pmember_admin_view()
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('startnum','str:1:',$startnum,'',XARVAR_NOT_REQUIRED)) return;
    $data['items'] = array();
    // Specify some labels for display
    $data['membername'] = xarVarPrepForDisplay(xarML('Member Name'));
    $data['expires']    = xarVarPrepForDisplay(xarML('Expires'));
    $data['subscribed'] = xarVarPrepForDisplay(xarML('Subscribed'));

    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('pmember', 'user', 'countitems'),
                                    xarModURL('pmember', 'admin', 'view', array('startnum' => '%%')),
                                    50);


    // Security Check
    if(!xarSecurityCheck('AdminPMember')) return;

    // The user API function is called
    $links = xarModAPIFunc('pmember',
                           'user',
                           'getall',
                           array('startnum' => $startnum,
                                 50));

    if (empty($links)) {
        $msg = xarML('No subscriptions recorded');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];
        $getname = xarModAPIFunc('roles',
                                 'user',
                                 'get',
                                 array('uid' => $link['uid']));
        $links[$i]['name'] = $getname['name'];
    $links[$i]['username'] = $getname['uname'];
    }

    // Add the array of items to the template variables
    $data['items'] = $links;
    // Return the template variables defined in this function
    return $data;
}
?>
