<?php
/**
 * @author John Cox
 * @ View existing forums
*/
function xarbb_admin_view()
{  
    // Get parameters from whatever input we need
    if (!xarVarFetch('startnum', 'id', $startnum, NULL, XARVAR_NOT_REQUIRED)) return;
    $data['items'] = array();
    // Security Check
    if(!xarSecurityCheck('EditxarBB',1,'Forum')) return;
    $data['isforums']=true;
    $forumsperpage=xarModGetVar('xarbb','forumsperpage');
    $data['addforum']=xarModURL('xarbb','admin','new');
    // The user API function is called
    $links = xarModAPIFunc('xarbb',
                           'user',
                           'getallforums',
                           array('startnum' => $startnum,
                                 'numitems' => xarModGetVar('xarbb',
                                                            'forumsperpage')));

    if (empty($links)) {
        $data['isforums']=false;
        //<jojodee> Handle it ourselves Bug #2455 
        //$msg = xarML('There are no Forums registered for viewing.  Please add a forum.');
        // xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return $data;
    }
    $totlinks=count($links);
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < $totlinks; $i++) {
        $link = $links[$i];

        if (xarSecurityCheck('EditxarBB', 0)) {
            $links[$i]['editurl'] = xarModURL('xarbb',
                                              'admin',
                                              'modify',
                                              array('fid' => $link['fid']));
        } else {
            $links[$i]['editurl'] = '';
        }
    }
    // Add the array of items to the template variables
    $data['tabs'] = $links;
    // For the tabs to never be the active tab.
    $data['fid'] = '';
    // TODO add the mass moderation here

    // Return the template variables defined in this function
    return $data;
}
?>