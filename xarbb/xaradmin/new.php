<?php
/**
 * File: $Id$
 * 
 * Create a new forum
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
 * add new forum
 */
function xarbb_admin_new()
{
    // Security Check
    if(!xarSecurityCheck('AddxarBB',1,'Forum')) return;

    // Get parameters
	if (!xarVarFetch('fname', 'str:1:', $data['fname'], '', XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('fdesc', 'str:1:', $data['fdesc'], '', XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('phase', 'str:1:', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cids',     'isset',    $cids,    NULL, XARVAR_DONT_SET)) return;

    switch(strtolower($phase)) {

        case 'form':
        default:

            $item = array();
            $item['module'] = 'xarbb';
            $item['itemtype'] = 1; // forum
            $hooks = xarModCallHooks('item','new','',$item);
            if (empty($hooks)) {
                $data['hooks'] = '';
            } elseif (is_array($hooks)) {
                $data['hooks'] = join('',$hooks);
            } else {
                $data['hooks'] = $hooks;
            }

            $data['createlabel'] = xarML('Submit');
            $data['authid'] = xarSecGenAuthKey();
            break;

        case 'update':
            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;

            if (!empty($cids) && count($cids) > 0) {
                $data['cids'] = array_values(preg_grep('/\d+/',$cids));
            } else {
                $data['cids'] = array();
            }
            //var_dump($data['cids']); return;
            $tposter = xarUserGetVar('uid');

            // The API function is called
            if (!xarModAPIFunc('xarbb',
                               'admin',
                               'create',
                               array('fname'    => $data['fname'],
                                     'fdesc'    => $data['fdesc'],
                                     'cids'     => $data['cids'],
                                     'fposter'  => $tposter))) return;

            // Get New Forum ID
            $forum = xarModAPIFunc('xarbb',
                                   'user',
                                   'getforum',
                                   array('fname' => $data['fname']));

            // Need to create a topic so we don't get the nasty empty error when viewing the forum.
            $ttitle = xarML('First Post');
            $tpost = xarML('This is your first topic');

            if (!xarModAPIFunc('xarbb',
                               'user',
                               'createtopic',
                               array('fid'      => $forum['fid'],
                                     'ttitle'   => $ttitle,
                                     'tpost'    => $tpost,
                                     'tposter'  => $tposter))) return;

            xarResponseRedirect(xarModURL('xarbb', 'admin', 'view'));
            break;
    }
    // Return the output
    return $data;
}
?>
