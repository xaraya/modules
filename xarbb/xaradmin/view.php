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
    $startnum = xarVarCleanFromInput('startnum');
    $data['items'] = array();

    // Specify some labels for display
    $data['authid'] = xarSecGenAuthKey();
    $data['pager'] = '';

    // Security Check
    if(!xarSecurityCheck('EditxarBB',1,'Forum')) return;

    // The user API function is called
    $links = xarModAPIFunc('xarbb',
                           'user',
                           'getallforums',
                           array('startnum' => $startnum,
                                 'numitems' => xarModGetVar('xarbb',
                                                            'itemsperpage')));

    if (empty($links)) {
        $msg = xarML('There are no Forums registered.  Please add a forum.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($links); $i++) {
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
    $data['pager'] = '';

    // Return the template variables defined in this function
    return $data;
}

?>
