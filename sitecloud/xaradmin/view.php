<?php
/**
 * view items
 */
function sitecloud_admin_view()
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('startnum','str:1:',$startnum,'',XARVAR_NOT_REQUIRED)) return;
    $data['items'] = array();
    // Specify some labels for display
    $data['urllabel'] = xarVarPrepForDisplay(xarML('URL'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('Options'));
    $data['authid'] = xarSecGenAuthKey();
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('sitecloud', 'user', 'countitems'),
                                    xarModURL('sitecloud', 'admin', 'view', array('startnum' => '%%')),
                                    xarModGetVar('sitecloud', 'itemsperpage'));

    // Security Check
	if(!xarSecurityCheck('Editsitecloud')) return;
    // The user API function is called
    $links = xarModAPIFunc('sitecloud',
                           'user',
                           'getall',
                           array('startnum' => $startnum,
                                 'numitems' => xarModGetVar('sitecloud',
                                                          'itemsperpage')));
    if (empty($links)) {
        $msg = xarML('No urls to spider, please add a site.', 'sitecloud');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];
        if (xarSecurityCheck('Editsitecloud',0)) {
            $links[$i]['editurl'] = xarModURL('sitecloud',
                                              'admin',
                                              'modify',
                                              array('id' => $link['id']));
        } else {
            $links[$i]['editurl'] = '';
        }
        $links[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('Deletesitecloud',0)) {
            $links[$i]['deleteurl'] = xarModURL('sitecloud',
                                                'admin',
                                                'delete',
                                                array('id' => $link['id'],
                                                      'authid' => $data['authid']));
            $links[$i]['javascript'] = "return xar_base_confirmLink(this, '" . xarML('Delete Headline Feed') . " $link[url] ?')";
        } else {
            $links[$i]['deleteurl'] = '';
        }
        $links[$i]['deletetitle'] = xarML('Delete');
    }

    // Add the array of items to the template variables
    $data['items'] = $links;
    $data['selstyle']  = xarModGetUserVar('sitecloud', 'selstyle');
    if (empty($data['selstyle'])){
        $data['selstyle'] = 'plain';
    }
    // select vars for drop-down menus
    $data['style']['plain']   = xarML('Plain');
    $data['style']['compact'] = xarML('Compact');
    // Return the template variables defined in this function
    return $data;
}
?>
