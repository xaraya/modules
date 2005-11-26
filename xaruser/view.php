<?php
/**
 * MyBookMarks Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage  MyBookMarks Module
 * @author John Cox
 */

/**
 * view the bookmarks
 */
function mybookmarks_user_view($args)
{
    extract($args);
    if (!xarVarFetch('url','str',$url, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarSecurityCheck('Viewmybookmarks')) return;
    if (!xarUserIsLoggedIn()) return;
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
                                                      'confirm' => 1,
                                                      'redirect' => $url));
        $bookmarks[$i]['javascript'] = "return confirmLink(this, '" . xarML('Delete Bookmark') . " $bookmark[name] ?')";
        $bookmarks[$i]['deletetitle'] = xarML('Delete');
    }
    $data['bookmarks'] = $bookmarks;
    return $data;
}
?>