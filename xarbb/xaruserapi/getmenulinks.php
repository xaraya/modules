<?php
/**
 * File: $Id$
 * 
 * Standard function to retrieve menu links
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_userapi_getmenulinks()
{
    $forums = xarModAPIFunc('xarbb',
                            'user',
                            'getallforums');

    foreach ($forums as $forum){
        // The user API function is called.
        $data = xarModAPIFunc('xarbb',
                              'user',
                              'getforum',
                              array('fid' => $forum['fid']));
        if(!xarSecurityCheck('ReadxarBB',1,'Forum',$data['catid'].':'.$forum['fid'])) return;
        $menulinks[] = Array('url'   => xarModURL('xarbb',
                                                  'user',
                                                  'viewforum', array('fid' => $forum['fid'])),
                             'title' => $forum['fname'],
                             'label' => $forum['fname']);
    }
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}
?>