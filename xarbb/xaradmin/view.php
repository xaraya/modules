<?php
/**
 * File: $Id$
 * 
 * View forums
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/

/**
 * @author John Cox
 * @ View existing forums
*/
function xarbb_admin_view()
{
    // Get parameters from whatever input we need
    if (!xarVarFetch('startnum', 'id', $startnum, NULL, XARVAR_NOT_REQUIRED)) return;

    $data['items'] = array();

    // Specify some labels for display
    $data['authid'] = xarSecGenAuthKey();
    $data['pager'] = '';

    // Security Check
    if(!xarSecurityCheck('EditxarBB',1,'Forum')) return;

    $forumsperpage=xarModGetVar('xarbb','forumsperpage');

    // The user API function is called
    $links = xarModAPIFunc('xarbb',
                           'user',
                           'getallforums',
                           array('startnum' => $startnum,
                                 'numitems' => xarModGetVar('xarbb',
                                                            'forumsperpage')));

    if (empty($links)) {
        $msg = xarML('There are no Forums registered.  Please add a forum.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
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
        $links[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeletexarBB', 0)) {
            $links[$i]['deleteurl'] = xarModURL('xarbb',
                                               'admin',
                                               'delete',
                                               array('fid' => $link['fid']));
        } else {
            $links[$i]['deleteurl'] = '';
        }
        $links[$i]['deletetitle'] = xarML('Delete');
    }

    // Add the array of items to the template variables
    $data['items'] = $links;

    // TODO : add a pager (once it exists in BL)
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('xarbb', 'user', 'countforums'),
        xarModURL('xarbb', 'admin', 'view', array('startnum' => '%%')),
        xarModGetVar('xarbb', 'forumsperpage'));

    // Return the template variables defined in this function
    return $data;
}

?>
