<?php
/*
 * MyBookMarks Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage  MyBookMarks Module
 * @author John Cox
*/

/**
 * view censored words
 */
function mybookmarks_user_view($args)
{
    extract($args);
    if (!xarVarFetch('startnum', 'int:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return; 
    $bookmarks = xarModAPIFunc('mybookmarks',
                               'user',
                               'getall',
                                array('uid' => xarUserGetVar('uid')));

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($bookmarks); $i++) {
        $bookmark = $bookmarks[$i];

        $bookmarks[$i]['deleteurl'] = xarModURL('mybookmarks',
                                                'user',
                                                'delete',
                                                array('id' => $bookmark['id'],
                                                      'authid' => xarSecGenAuthKey(),
                                                      'confirm' => 1));
        $bookmarks[$i]['javascript'] = "return confirmLink(this, '" . xarML('Delete Bookmark') . " $bookmark[name] ?')";
        $bookmarks[$i]['deletetitle'] = xarML('Delete');
    }
    $data['bookmarks'] = $bookmarks;
    /*
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('mybookmarks', 'user', 'countitems'),
        xarModURL('mybookmarks', 'user', 'view', array('startnum' => '%%', 'theme' => 'print')),
        20);
    */
    return $data;
}
?>