<?php
/**
 * File: $Id$
 * 
 * Update a forum topic
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
function xarbb_user_locktopic()
{
    if (!xarVarFetch('tid','int:1:',$tid)) return;
    if (!xarVarFetch('tstatus','int',$tstatus)) return;

    // Need to handle locked topics
    $data = xarModAPIFunc('xarbb',
                          'user',
                          'gettopic',
                          array('tid' => $tid));

    // The user API function is called.
    $forum = xarModAPIFunc('xarbb',
                          'user',
                          'getforum',
                          array('fid' => $data['fid']));

    if(!xarSecurityCheck('ModxarBB',1,'Forum',$forum['catid'].':'.$forum['fid'])) return;

    if (!xarModAPIFunc('xarbb',
                       'user',
                       'updatetopic',
                       array('tid'      => $tid,
                             'tstatus'  => $tstatus))) return;

    xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));
    return;
}
?>