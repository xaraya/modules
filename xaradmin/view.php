<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005-2009 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @author John Cox
*/
/**
 * view items
 */
function headlines_admin_view()
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('startnum','str:1:',$startnum,'',XARVAR_NOT_REQUIRED)) return;
    $data['items'] = array();
    // Specify some labels for display
    $data['urllabel'] = xarVarPrepForDisplay(xarML('URL'));
    $data['orderlabel'] = xarVarPrepForDisplay(xarML('Order'));
    $data['warninglabel'] = xarVarPrepForDisplay(xarML('Status'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('Options'));
    $data['authid'] = xarSecGenAuthKey();
    sys::import('modules.base.class.pager');
    $data['pager'] = xarTplPager::getPager($startnum,
                                    xarMod::apiFunc('headlines', 'user', 'countitems'),
                                    xarModURL('headlines', 'admin', 'view', array('startnum' => '%%')),
                                    xarModVars::get('headlines', 'itemsperpage'));

    // Security Check
    if(!xarSecurityCheck('EditHeadlines')) return;
    // The user API function is called
    $links = xarMod::apiFunc('headlines',
                          'user',
                          'getall',
                          array('startnum' => $startnum,
                                'numitems' => xarModVars::get('headlines',
                                                           'itemsperpage')));
    if (empty($links)){
        //xarController::redirect(xarModURL('headlines', 'admin', 'new'));
    }
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];
        if (xarSecurityCheck('EditHeadlines',0)) {
            $links[$i]['editurl'] = xarModURL('headlines',
                                              'admin',
                                              'modify',
                                              array('hid' => $link['hid']));
        } else {
            $links[$i]['editurl'] = '';
        }
        $links[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteHeadlines',0)) {
            $links[$i]['deleteurl'] = xarModURL('headlines',
                                                'admin',
                                                'delete',
                                                array('hid' => $link['hid'],
                                                      'authid' => $data['authid']));
            $links[$i]['javascript'] = "return xar_base_confirmLink(this, '" . xarML('Delete Headline Feed') . " $link[url] ?')";
        } else {
            $links[$i]['deleteurl'] = '';
        }
        $links[$i]['deletetitle'] = xarML('Delete');
    }
    // Add the array of items to the template variables
    $data['items'] = $links;
    $data['selstyle']  = xarModUserVars::get('headlines', 'selstyle');
    if (empty($data['selstyle'])){
        $data['selstyle'] = 'plain';
    }
    // select vars for drop-down menus
    $data['style']['plain']   = xarML('Plain');
    $data['style']['compact'] = xarML('Compact');
    return $data;
}
?>
