<?php
/**
 * File: $Id$
 * 
 * Set the read data
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_user_redirect($args)
{   
    extract($args);
    if(!xarVarFetch('fid', 'id', $fid, '', XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('tid', 'id', $tid, '', XARVAR_NOT_REQUIRED)) return;
    // Security Check
    if(!xarSecurityCheck('ViewxarBB',1,'Forum')) return;
    $data['uid']    = xarUserGetVar('uid');
    $data['now']    = serialize(time());
    if ((empty($fid)) && (empty($tid))){
        $sitename = xarModGetVar('themes', 'SiteName', 0);
        setcookie('xarbb_all', $data['now'], time()+300000, "/", "", 0);
        setcookie('xarbb_lastvisit', $data['now'], time()+300000, "/", "", 0);
        xarResponseRedirect(xarModURL('xarbb', 'user', 'main'));
    } elseif ((!empty($fid)) && (empty($tid))) {
        setcookie('xarbb_f_'.$fid, $data['now'], time()+300000, "/", "", 0);
        setcookie('xarbb_lastvisit', $data['now'], time()+300000, "/", "", 0);
        xarResponseRedirect(xarModURL('xarbb', 'user', 'viewforum', array('fid' => $fid)));
    } elseif (!empty($tid)) {
        setcookie('xarbb_f_'.$fid, $data['now'], time()+300000, "/", "", 0);
        setcookie('xarbb_t_'.$tid, $data['now'], time()+300000, "/", "", 0);
        setcookie('xarbb_lastvisit', $data['now'], time()+300000, "/", "", 0);
        xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));
    }
    return true;
}
?>