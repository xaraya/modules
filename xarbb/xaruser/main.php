<?php
/**
 * File: $Id$
 * 
 * Main user function to display list of all existing forums
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_user_main()
{
   // Get parameters from whatever input we need
    if (!xarVarFetch('startnum', 'id', $startnum, NULL, XARVAR_NOT_REQUIRED)) return;

    // Security Check
    if(!xarSecurityCheck('ViewxarBB',1,'Forum')) return;

    // Get parameters from whatever input we need
    $data = array();
    $data['pager'] = '';    
    $data['catid'] = xarVarCleanFromInput('catid');

    $data['items'] = array();

    // The user API function is called
    $forums = xarModAPIFunc('xarbb',
                            'user',
                            'getallforums',
                             array('catid' => $data['catid'],
                                   'startnum' => $startnum,
                                    'numitems' => xarModGetVar('xarbb',
                                                            'forumsperpage')));
    $totalforums=count($forums);
    for ($i = 0; $i < $totalforums; $i++) {
        $forum = $forums[$i];

        $getname = xarModAPIFunc('roles',
                                 'user',
                                 'get',
                                 array('uid' => $forum['fposter']));

        $forums[$i]['name'] = $getname['name'];
    }

    // TODO, need to check if new topics have been updated since last visit.
    $data['folder']       = '<img src="' . xarTplGetImage('folder.gif') . '" alt="'.xarML('Folder').'"/>';

    // Add a pager
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('xarbb', 'user', 'countforums'),
        xarModURL('xarbb', 'user', 'main', array('startnum' => '%%')),
        xarModGetVar('xarbb', 'forumsperpage'));

    // Add the array of items to the template variables
    $data['items'] = $forums;

    // Return the template variables defined in this function
    return $data;
}

?>
