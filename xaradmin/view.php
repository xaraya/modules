<?php
function ping_admin_view()
{   // Get parameters from whatever input we need
    if (!xarVarFetch('startnum', 'id', $startnum, NULL, XARVAR_NOT_REQUIRED)) return;
    $data['authid'] = xarSecGenAuthKey();
    $data['items'] = array();
    // Security Check
    if(!xarSecurityCheck('Adminping',1,'Forum')) return;
    // The user API function is called
    $links = xarModAPIFunc('ping',
                           'user',
                           'getall');

    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];

        $links[$i]['editurl'] = Xarmodurl('ping',
                                          'admin',
                                          'modify',
                                          Array('id' => $link['id']));

        $links[$i]['edittitle'] = xarML('Edit');

        $links[$i]['deleteurl'] = xarModURL('ping',
                                            'admin',
                                            'delete',
                                            array('id' => $link['id'],
                                                  'confirmation' => 1,
                                                  'authid' => $data['authid']));
        $links[$i]['javascript'] = "return confirmLink(this, '" . xarML('Delete Ping URL') . " $link[url] ?')";
        $links[$i]['deletetitle'] = xarML('Delete');
    }

    // Add the array of items to the template variables
    $data['items'] = $links;

    $data['selstyle']  = xarModGetUserVar('ping', 'selstyle');
    if (empty($data['selstyle'])){
        $data['selstyle'] = 'plain';
    }

    // select vars for drop-down menus
    $data['style']['plain']   = xarML('Plain');
    $data['style']['compact'] = xarML('Compact');


    // For the tabs to never be the active tab.
    return $data;
}
?>