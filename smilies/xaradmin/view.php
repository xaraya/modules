<?php
function smilies_admin_view()
{
    // Security Check
	if(!xarSecurityCheck('EditSmilies')) return;
    if(!xarVarFetch('startnum', 'isset',    $startnum, 1,     XARVAR_NOT_REQUIRED)) {return;}
    $data['items'] = array();
    // Specify some labels for display
    $data['authid'] = xarSecGenAuthKey();
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('smilies', 'user', 'countitems'),
                                    xarModURL('smilies', 'admin', 'view', array('startnum' => '%%')),
                                    xarModGetVar('smilies', 'itemsperpage'));
    // The user API function is called
    $links = xarModAPIFunc('smilies',
                           'user',
                           'getall',
                           array('startnum' => $startnum,
                                 'numitems' => xarModGetVar('smilies',
                                                            'itemsperpage')));

    if (empty($links)) {
        $msg = xarML('There are no smilies registered');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];
        if (xarSecurityCheck('EditSmilies',0)) {
            $links[$i]['editurl'] = xarModURL('smilies',
                                              'admin',
                                              'modify',
                                              array('sid' => $link['sid']));
        } else {
            $links[$i]['editurl'] = '';
        }
        $links[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteSmilies',0)) {
            $links[$i]['deleteurl'] = xarModURL('smilies',
                                               'admin',
                                               'delete',
                                               array('sid' => $link['sid']));
        } else {
            $links[$i]['deleteurl'] = '';
        }
        $links[$i]['deletetitle'] = xarML('Delete');
    }
    // Add the array of items to the template variables
    $data['items'] = $links;
    // Return the template variables defined in this function
    return $data;
}
?>