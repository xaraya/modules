<?php
/**
 * view items
 */
function censor_admin_view()
{ 
    // Get parameters
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    $authid = xarSecGenAuthKey();
    $data['items'] = array(); 
    // Specify some labels for display
    $data['keywordlabel'] = xarVarPrepForDisplay(xarML('Key Word'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('Options'));
    $data['authid'] = xarSecGenAuthKey(); 
    // Call the xarTPL helper function to produce a pager in case of there
    // being many items to display.
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('censor', 'user', 'countitems'),
        xarModURL('censor', 'admin', 'view', array('startnum' => '%%')),
        xarModGetVar('censor', 'itemsperpage')); 
    // Security Check
    if (!xarSecurityCheck('EditCensor')) return; 
    // The user API function is called
    $censors = xarModAPIFunc('censor',
        'user',
        'getall',
        array('startnum' => $startnum,
            'numitems' => xarModGetVar('censor', 'itemsperpage')));
    if (empty($censors)) {
        $msg = xarML('No censor in database.',
            'censor');
        xarExceptionSet(XAR_USER_EXCEPTION,
            'MISSING_DATA',
            new DefaultUserException($msg));
        return;
    } 
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($censors); $i++) {
        $censor = $censors[$i];
        if (xarSecurityCheck('EditCensor', 0)) {
            $censors[$i]['editurl'] = xarModURL('censor',
                'admin',
                'modify',
                array('cid' => $censor['cid']));
        } else {
            $censors[$i]['editurl'] = '';
        } 
        $censors[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteCensor', 0)) {
            $censors[$i]['deleteurl'] = xarModURL('censor',
                'admin',
                'delete',
                array('cid' => $censor['cid'],
                      'authid' => $authid));
            $censors[$i]['javascript'] = "return confirmLink(this, '" . xarML('Delete Censored Word') . " $censor[keyword] ?')";
        } else {
            $censors[$i]['deleteurl'] = '';
        } 
        $censors[$i]['deletetitle'] = xarML('Delete');
    } 
    // Add the array of items to the template variables
    $data['items'] = $censors; 

    $data['selstyle']                               = xarModGetUserVar('censor', 'selstyle');
    if (empty($data['selstyle'])){
        $data['selstyle'] = 'plain';
    }
    // select vars for drop-down menus
    $data['style']['plain']                         = xarML('Plain');
    $data['style']['compact']                       = xarML('Compact');
    // Return the template variables defined in this function
    return $data;
} 
?>