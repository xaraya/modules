<?php
/**
 * Add a term to the encyclopedia
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_user_newentry()
{
    if (!xarSecurityCheck('CommentEncyclopedia',1,'Entry')) {return;}

    if(!xarVarFetch('vid',   'int', $data['vid']   , 0, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('term',   'str', $data['term']   , '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('pronunciation',   'str', $data['pronunciation']   , '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('related',   'str', $data['related']   , '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('links',   'str', $data['links']   , '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('definition',   'str', $data['definition']   , '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('upload',   'str', $data['upload']   , '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('active',   'int', $data['active']   , '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('author',   'str', $author   , '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('confirmed',   'int', $confirmed   , 0, XARVAR_NOT_REQUIRED)) {return;}

    $user = xarModAPIFunc('roles','user','get', array('uid' => xarSessionGetVar('uid')));
    $data['author'] = $user['name'];
    $data['volumes'] = xarModAPIFunc('encyclopedia',
                          'user',
                          'vols');
    if ($confirmed) {
        if (!xarSecConfirmAuthKey()) return;
        $id = xarModAPIFunc('encyclopedia',
                            'admin',
                            'addentry',
                            array('vid' => $data['vid'],
                                  'term' => $data['term'],
                                  'pronunciation' => $data['pronunciation'],
                                  'related' => $data['related'],
                                  'links' => $data['links'],
                                  'definition' => $data['definition'],
                                  'author' => $author,
                                  'file' => $data['upload'],
                                  'active' => $data['active']));
        $data['vid'] = 0;
        $data['term'] = '';
        $data['pronunciation'] = '';
        $data['related'] = '';
        $data['links'] = '';
        $data['definition'] = '';
        $data['upload'] = '';
        $data['active'] = 0;
    }
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}
?>