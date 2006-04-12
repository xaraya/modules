<?php
/**
 * Create a new forum
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
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
    if (!xarVarFetch('fstatus','int', $data['fstatus'], 0)) return;
    if (!xarVarFetch('phase', 'str:1:', $phase, 'form', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cids',     'array',    $cids,    NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('new_cids',     'array',    $cids,    NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('postsperpage','int:1:',$postsperpage, 20 ,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('postsortorder', 'str:1:', $postsortorder, 'ASC', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('topicsperpage','int:1:',$topicsperpage, 20, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('topicsortby', 'str:1:', $topicsortby, 'time', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('topicsortorder', 'str:1:', $topicsortorder, 'DESC', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hottopic','int:1:',$hottopic, 20, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowhtml','checkbox', $allowhtml, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowbbcode','checkbox', $allowbbcode, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('editstamp','int:0:2',$editstamp, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showcats','checkbox', $showcats, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('nntp', 'str:1:', $nntp, '', XARVAR_NOT_REQUIRED)) return;

    switch(strtolower($phase)) {

        case 'form':
        default:

            if (!xarVarFetch('fname', 'str:1:', $data['fname'], '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('fdesc', 'str:1:', $data['fdesc'], '', XARVAR_NOT_REQUIRED)) return;
            $xarsettings= xarModGetVar('xarbb', 'settings');
            if (!empty($xarsettings)) {
                $settings = unserialize($xarsettings);
            }
            $data['postsperpage']    = !isset($settings['postsperpage']) ? 20 :$settings['postsperpage'];
            $data['postsortorder']   = !isset($settings['postsortorder']) ? 'ASC' :$settings['postsortorder'];
            $data['topicsperpage']   = !isset($settings['topicsperpage']) ? 20 :$settings['topicsperpage'];
            $data['topicsortby']     = !isset($settings['topicsortby']) ? 'time' :$settings['topicsortby'];
            $data['topicsortorder']  = !isset($settings['topicsortorder']) ? 'DESC' :$settings['topicsortorder'];
            $data['hottopic']        = !isset($settings['hottopic']) ? 20 :$settings['hottopic'];
            $data['editstamp']       = 0;// default is zero !isset($settings['editstamp']) ? 0 :$settings['editstamp'];
            $data['allowhtml']       = !isset($settings['allowhtml']) ? false :$settings['allowhtml'];
            $data['allowbbcode']     = !isset($settings['allowbbcode']) ? false :$settings['allowbbcode'];
            $data['showcats']        = !isset($settings['showcats']) ? false :$settings['showcats'];
            $data['usenntp']         = !isset($settings['usenntp']) ? false :$settings['usenntp'];
 
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
            $masternntpsetting=xarModGetVar('xarbb','masternntpsetting');
            $masternntpsetting  = !isset($masternntpsetting) ? false :$masternntpsetting;
            //jojodee- let's only do this if we allow nntp in the master setting else this is loading each time now
            //even if the nntp settings are not available. Review when nntp is available
            if (xarModIsAvailable('newsgroups') && $masternntpsetting){
                // get the current list of newsgroups
                $data['items'] = xarModAPIFunc('newsgroups','user','getgroups',
                                               array('nocache' => true));
                $grouplist = xarModGetVar('newsgroups','grouplist');
                if (!empty($grouplist)) {
                    $selected = unserialize($grouplist);
                    // get list of selected newsgroups
                    $data['selected'] = array_keys($selected);
                    // update description of selected newsgroups
                    foreach ($selected as $group => $info) {
                        if (isset($data['items'][$group]) && isset($info['desc'])) {
                            $data['items'][$group]['desc'] = $info['desc'];
                        }
                    }
                } else {
                    $data['selected'] = '';
                }
            }

            $data['createlabel'] = xarML('Submit');
            $data['authid'] = xarSecGenAuthKey();
            break;

        case 'update':
            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;

            if (!xarVarFetch('fname', 'str:1:', $data['fname'])) return;
            if (!xarVarFetch('fdesc', 'str:1:', $data['fdesc'])) return;

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

            // Recovery procedure in case the forum is no longer assigned to any category
            if (empty($forum['fid'])) {
                $forums = xarModAPIFunc('xarbb','user','getallforums');
                foreach ($forums as $info) {
                    if ($info['fid'] == $newfid) {
                        $forum = $info;
                        break;
                    }
                }
                if (empty($forum['fid'])) {
                    $msg = xarML('Invalid Parameter Count');
                    xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                    return;
                }
            }

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
            $settings['postsortorder']      = $postsortorder;
            $settings['topicsperpage']      = $topicsperpage;
            $settings['topicsortby']        = $topicsortby;
            $settings['topicsortorder']     = $topicsortorder;
            $settings['hottopic']           = $hottopic;
            $settings['allowhtml']          = $allowhtml;
            $settings['allowbbcode']        = $allowbbcode;
            $settings['editstamp']          = $editstamp;            
            $settings['showcats']           = $showcats;
            $settings['nntp']               = $nntp;

            xarModSetVar('xarbb', 'settings.'.$forum['fid'], serialize($settings));
            xarResponseRedirect(xarModURL('xarbb', 'admin', 'view'));
            break;
    }
    // Return the output
 return $data;
}
?>
