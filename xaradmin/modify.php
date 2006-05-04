<?php
/**
 * Xaraya Modify an existing forum
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
 * @author John Cox
 * @function to modify an existing forum
*/
function xarbb_admin_modify()
{
    // Get parameters
    if (!xarVarFetch('fid', 'id', $fid)) return;
    if (!xarVarFetch('phase', 'enum:form:update', $phase, 'form', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('cids', 'list:id', $cids, NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('modify_cids', 'list:id', $cids, NULL, XARVAR_DONT_SET)) return;

    switch(strtolower($phase)) {
        case 'form':
        default:
            if (!isset($fid)) {
                xarSessionSetVar('statusmsg', '');
            }

            // The user API function is called.
            $data = xarModAPIFunc('xarbb', 'user', 'getforum', array('fid' => $fid));
            if (empty($data)) return;

            // Recovery procedure in case the forum is no longer assigned to any category
            if (empty($data['fid'])) {
                $forums = xarModAPIFunc('xarbb', 'user', 'getallforums');
                foreach ($forums as $forum) {
                    if ($forum['fid'] == $fid) {
                        $data = $forum;
                        $data['catid'] = xarModGetVar('xarbb', 'mastercids');
                        break;
                    }
                }
                if (empty($data['fid'])) {
                    $msg = xarML('Invalid Parameter Count');
                    xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                    return;
                }
            }

            // Security Check
            if (!xarSecurityCheck('EditxarBB', 1, 'Forum', $data['catid'] . ':' . $data['fid'])) return;

            // Get the settings for this forum
            $settings = xarModGetVar('xarbb', 'settings.' . $fid);
            if (isset($settings)){
                $settings = unserialize($settings);
            } else {
                xarModSetVar('xarbb', 'settings.' . $fid, '');
            }

            if (isset($settings) && is_array($settings)) {
                $data['topicsperpage']          = empty($settings['topicsperpage']) ? 20 : $settings['topicsperpage'];
                $data['topicsortby']            = empty($settings['topicsortby']) ? 'time' :$settings['topicsortby'];
                $data['topicsortorder']         = empty($settings['topicsortorder']) ? 'DESC' :$settings['topicsortorder'];
                $data['postsperpage']           = empty($settings['postsperpage']) ? 20 : $settings['postsperpage'];
                $data['postsortorder']          = empty($settings['postsortorder']) ? 'ASC' :$settings['postsortorder'];
                $data['hottopic']               = empty($settings['hottopic']) ? 20 : $settings['hottopic'];
                $data['allowhtml']              = !empty($settings['allowhtml']) ? true : false;
                $data['allowbbcode']            = !empty($settings['allowbbcode']) ? true : false;
                $data['editstamp']              = !isset($settings['editstamp']) ? 0 : $settings['editstamp'];
                // TODO: can showcats be removed?
                $data['showcats']               = !empty($settings['showcats']) ? 'checked="checked"' : '';
                $data['nntp']                   = empty($settings['nntp']) ? '' :$settings['nntp'];
            }

            if (!isset($data['topicsperpage'])) {
                $data['topicsperpage'] = 20;
            }
            if (!isset($data['postsperpage'])) {
                $data['postsperpage'] = 20;
            }
            if (!isset($data['postsortorder'])) {
                $data['postsortorder'] = 'ASC';
            }
            if (!isset($data['hottopic'])) {
                $data['hottopic'] = 20;
            }
            if (!isset($data['editstamp'])) {
                $data['editstamp'] = 0;
            }
            if (!isset($data['allowhtml'])) {
                $data['allowhtml'] = '';
            }
            if (!isset($data['allowbbcode'])) {
                $data['allowbbcode'] = '';
            }
            if (!isset($data['showcats'])) {
                $data['showcats'] = '';
            }
            if (!isset($data['nntp'])) {
                $data['nntp'] = '';
            }
            $masternntpsetting=xarModGetVar('xarbb','masternntpsetting');
            $masternntpsetting  = !isset($masternntpsetting) ? false :$masternntpsetting;
            //jojodee- let's only do this if we allow nntp in the master setting else this is loading each time now
            //even if the nntp settings are not available. Review when nntp is available

            if (xarModIsAvailable('newsgroups') && $masternntpsetting){
                // get the current list of newsgroups
                $data['items'] = xarModAPIFunc('newsgroups','user','getgroups', array('nocache' => true));
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

            $data['module'] = 'xarbb';
            $data['itemtype'] = 0; // forum
            $data['itemid'] = $fid;
            $hooks = xarModCallHooks('item', 'modify', $fid, $data);
            if (empty($hooks)) {
                $data['hooks'] = '';
            } elseif (is_array($hooks)) {
                $data['hooks'] = join('', $hooks);
            } else {
                $data['hooks'] = $hooks;
            }

            //Load Template
            $data['authid'] = xarSecGenAuthKey();
            $data['createlabel'] = xarML('Submit');

            // For Tabs:
            // The user API function is called
            $links = xarModAPIFunc('xarbb', 'user', 'getallforums');
            $totlinks=count($links);

            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < $totlinks; $i++) {
                $link = $links[$i];

                if (xarSecurityCheck('EditxarBB', 0)) {
                    $links[$i]['editurl'] = xarModURL('xarbb', 'admin', 'modify', array('fid' => $link['fid']));
                } else {
                    $links[$i]['editurl'] = '';
                }
            }

            // Add the array of items to the template variables
            $data['tabs'] = $links;
            $data['action'] = '1';
            $data['settings']=$settings;
            $data['forumname'] = $data['fname'];
            break;

        case 'update':
            if (!xarVarFetch('fname', 'str:1:', $fname, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('fdesc', 'str:1:', $fdesc, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('fstatus','int', $fstatus, 0)) return;
            if (!xarVarFetch('postsperpage','int:1:',$postsperpage, 20 ,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('postsortorder', 'str:1:', $postsortorder, 'ASC', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('hottopic','int:1:',$hottopic, 20 ,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('topicsperpage','int:1:',$topicsperpage, 20, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('topicsortby', 'str:1:', $topicsortby, 'time', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('topicsortorder', 'str:1:', $topicsortorder, 'DESC', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('allowhtml','checkbox', $allowhtml, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('allowbbcode','checkbox', $allowbbcode, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('editstamp','int:1:', $editstamp, 0 ,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('showcats','checkbox', $showcats, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('nntp', 'str:1:', $nntp, '', XARVAR_NOT_REQUIRED)) return;

            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;

            // The API function is called.
            if(!xarModAPIFunc(
                'xarbb', 'admin', 'update',
                array(
                    'fid'      => $fid,
                    'fname'    => $fname,
                    'fdesc'    => $fdesc,
                    'fstatus'  => $fstatus,
                    'cids'     => $cids
                )
            )) return;

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

            xarModSetVar('xarbb', 'settings.' . $fid, serialize($settings));

            // Enable bbcode hooks for modified forum
            if (xarModIsAvailable('bbcode')) {
                //Make sure the overall module hook is disabled so we can do each forum
                xarModAPIFunc(
                    'modules','admin','disablehooks',
                    array(
                        'callerModName'    => 'xarbb',
                        'callerItemType'   => 0,
                        'hookModName'      => 'bbcode'
                    )
                );

                if ($settings['allowbbcode']) {
                    xarModAPIFunc(
                        'modules', 'admin', 'enablehooks',
                        array(
                            'callerModName'    => 'xarbb',
                            'callerItemType'   => $fid,
                            'hookModName'      => 'bbcode'
                        )
                    );
                } else {
                    xarModAPIFunc(
                        'modules', 'admin', 'disablehooks',
                        array(
                            'callerModName'    => 'xarbb',
                            'callerItemType'   => $fid,
                            'hookModName'      => 'bbcode'
                        )
                    );
                }
            }

            // Redirect
            xarResponseRedirect(xarModURL('xarbb', 'admin', 'modify', array('fid' => $fid)));
            break;
    } // Switch

    return $data;
}

?>