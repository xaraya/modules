<?php
function bbcode_admin_view()
{
    // Security Check
    if(!xarSecurityCheck('EditBBCode')) return;
    if(!xarVarFetch('startnum', 'isset',    $startnum, 1,     XARVAR_NOT_REQUIRED)) {return;}
    $data['items'] = array();
    // Specify some labels for display
    $data['authid'] = xarSecGenAuthKey();

    // The user API function is called
    $links = xarModAPIFunc('bbcode',
                           'user',
                           'getall',
                           array('startnum' => $startnum,
                                 'numitems' => xarModGetVar('bbcode',
                                                            'itemsperpage')));

    if (empty($links)) {
        $msg = xarML('There are no custom bbcode added.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];

        $links[$i]['editurl'] = xarModURL('bbcode',
            'admin',
            'modify',
            array('id' => $link['id']));

        $links[$i]['deleteurl'] = xarModURL('bbcode',
                                              'admin',
                                              'delete',
                                              array('id' => $link['id'],
                                                    'confirmation' => 1,
                                                    'authid' => $data['authid']));

        $links[$i]['javascript'] = "return confirmLink(this, '" . xarML('Delete BBCode') . " $link[name] ?')";

    }

    // Add the array of items to the template variables
    $data['items'] = $links;
    // Return the template variables defined in this function
    return $data;
}
?>