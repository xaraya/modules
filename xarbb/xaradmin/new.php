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
    if (!xarVarFetch('fstatus','int', $data['fstatus'], 0)) return;
	if (!xarVarFetch('phase', 'str:1:', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cids',     'array',    $cids,    NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('new_cids',     'array',    $cids,    NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('postsperpage','int:1:',$postsperpage, 20 ,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('topicsperpage','int:1:',$topicsperpage, 20, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hottopic','int:1:',$hottopic, 20, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowhtml','checkbox', $allowhtml, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowbbcode','checkbox', $allowbbcode, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showcats','checkbox', $showcats, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('linknntp','checkbox', $linknntp, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('nntpport','int:1:4',$nntpport, 119, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('nntpserver', 'str:1:', $nntpserver, 'news.xaraya.com', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('nntpgroup', 'str:1:', $nntpgroup, 'xaraya.test', XARVAR_NOT_REQUIRED)) return;

    switch(strtolower($phase)) {

        case 'form':
        default:

            $xarsettings= xarModGetVar('xarbb', 'settings');
            if (!empty($xarsettings)) {
                $settings = unserialize($xarsettings);
            }
            $data['postsperpage']    = !isset($settings['postsperpage']) ? 20 :$settings['postsperpage'];
            $data['topicsperpage']   = !isset($settings['topicsperpage']) ? 20 :$settings['topicsperpage'];
            $data['hottopic']        = !isset($settings['hottopic']) ? 20 :$settings['hottopic'];
            $data['allowhtml']       = !isset($settings['allowhtml']) ? false :$settings['allowhtml'];
            $data['allowbbcode']     = !isset($settings['allowbbcode']) ? false :$settings['allowbbcode'];
            $data['showcats']        = !isset($settings['showcats']) ? false :$settings['showcats'];
            $data['linknntp']        = !isset($settings['linknntp']) ? false :$settings['linknntp'];
            $data['nntpport']        = !isset($settings['nntpport']) ? 119 :$settings['nntpport'];
            $data['nntpserver']      = !isset($settings['nntpserver']) ? 'news.xaraya.com' :$settings['nntpserver'];
            $data['nntpgroup']       = !isset($settings['nntpgroup']) ? '' :$settings['nntpgroup'];
 
            $item = array();
            $item['module'] = 'xarbb';
            $item['itemtype'] = 0; // forum
            $hooks = xarModCallHooks('item', 'new','', $item); // forum
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

            $tposter = xarUserGetVar('uid');

            // The API function is called
            $newfid= xarModAPIFunc('xarbb',
                                   'admin',
                                  'create',
                               array('fname'    => $data['fname'],
                                     'fdesc'    => $data['fdesc'],
                                     'cids'     => $data['cids'],
                                     'fposter'  => $tposter,
                                     'ftopics'  => 1,
                                     'fposts'   => 1,
                                     'fstatus'  => $data['fstatus']));
        if (!$newfid) return; 

           // Get New Forum ID
            $forum = xarModAPIFunc('xarbb',
                                   'user',
                                   'getforum',
                                   array('fid' => $newfid));


                // Need to create a topic so we don't get the nasty empty error when viewing the forum.
            $ttitle = xarML('Welcome to ').$forum['fname'];
            $tpost = xarML('This is the first topic for ').$forum['fname'];

            $tid= xarModAPIFunc('xarbb',
                               'user',
                               'createtopic',
                               array('fid'      => $forum['fid'],
                                     'ttitle'   => $ttitle,
                                     'tpost'    => $tpost,
                                     'tposter'  => $tposter));
           if (!$tid) return;

           // Enable hitcount hooks for xarbb forum
            xarModAPIFunc('modules','admin','enablehooks', array('callerModName'        => 'xarbb',
                                                                     'callerItemType'   => $forum['fid'],
                                                                     'hookModName'      => 'hitcount'));

            // Enable comment hooks for xarbb forum - let's just make sure
            xarModAPIFunc('modules','admin','enablehooks', array('callerModName'        => 'xarbb',
                                                                     'callerItemType'   => $forum['fid'],
                                                                     'hookModName'      => 'comments'));
            // Enable bbcode hooks for new xarbb forum
            if (xarModIsAvailable('bbcode')) {
                if ($allowbbcode) {
                    xarModAPIFunc('modules','admin','enablehooks',
                            array('callerModName'    => 'xarbb',
                                  'callerItemType'   => $forum['fid'],
                                  'hookModName'      => 'bbcode'));
                } else {
                    xarModAPIFunc('modules','admin','disablehooks',
                            array('callerModName'    => 'xarbb',
                                  'callerItemType'   => $forum['fid'],
                                  'hookModName'      => 'bbcode'));
                }
            }
            // Enable html hooks for xarbb forums
            if (xarModIsAvailable('html')) {
                if ($allowhtml) {
                    xarModAPIFunc('modules','admin','enablehooks',
                            array('callerModName'    => 'xarbb',
                                  'callerItemType'   => $forum['fid'],
                                  'hookModName'      => 'html'));
                } else {
                    xarModAPIFunc('modules','admin','disablehooks',
                            array('callerModName'    => 'xarbb',
                                  'callerItemType'   => $forum['fid'],
                                  'hookModName'      => 'html'));
                }
            }


            $settings = array();
            $settings['postsperpage']       = $postsperpage;
            $settings['topicsperpage']      = $topicsperpage;
            $settings['hottopic']           = $hottopic;
            $settings['allowhtml']          = $allowhtml;
            $settings['allowbbcode']        = $allowbbcode;
            $settings['showcats']           = $showcats;
            $settings['linknntp']           = $linknntp;
            $settings['nntpport']           = $nntpport;
            $settings['nntpserver']         = $nntpserver;
            $settings['nntpgroup']          = $nntpgroup;

            xarModSetVar('xarbb', 'settings.'.$forum['fid'], serialize($settings));
            xarResponseRedirect(xarModURL('xarbb', 'admin', 'view'));
            break;
    }
    // Return the output
 return $data;
}
?>
