<?php
/**
 * File: $Id$
 * 
 * Add new or edit existing forum topic
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
 * add new forum topic
 */
function xarbb_user_newtopic()
{
    list($phase,
         $ttitle,
         $tpost,
         $warning,
         $fid,
         $tid,
         $redirect) = xarVarCleanFromInput('phase',
                                      'ttitle',
                                      'tpost',
                                      'warning',
                                      'fid',
                                      'tid',
                                      'redirect');
    if(isset($tid))    {
        // The user API function is called.
        $data = xarModAPIFunc('xarbb',
                              'user',
                              'gettopic',
                              array('tid' => $tid));
    } else  {
        // The user API function is called.
        $data = xarModAPIFunc('xarbb',
                              'user',
                              'getforum',
                              array('fid' => $fid));
    }


    if (empty($data)) return;

    // Security Check

    if(isset($tid))    {
        if(!xarSecurityCheck('ModxarBB',1,'Forum',$data['catid'].':'.$data['fid'])) return;
    }
    else    {
        if(!xarSecurityCheck('PostxarBB',1,'Forum',$data['catid'].':'.$data['fid'])) return;

    }

    if (empty($phase)){
        $phase = 'form';
    }

    switch(strtolower($phase)) {

        case 'form':
        default:
            if(isset($tid))    {
                $data = xarModAPIFunc('xarbb','user','gettopic',array('tid' => $tid));
                $item = $data;
                $item['module'] = 'xarbb';
                $item['itemtype'] = 2; // Forum Topics
                $item['itemid'] = $tid;
                $data['hooks'] = xarModCallHooks('item','modify',$tid,$item);
            } else  {
                if (empty($tpost)){
                    $data['tpost'] = '';
                } else {
                    $data['tpost'] = $tpost;
                }
                if (empty($ttitle)){
                    $data['ttitle'] = '';
                } else {
                    $data['ttitle'] = $ttitle;
                }
                $item = $data;
                $item['module'] = 'xarbb';
                $item['itemtype'] = 2; // Forum Topics
                $item['itemid'] = '';
                $data['hooks'] = xarModCallHooks('item','new','',$item);
            }
            $data['authid'] = xarSecGenAuthKey();

            if (empty($warning)){
                $data['warning'] = '';
            } else {
                $data['warning'] = $warning;
            }
            if(empty($redirect))
                $data['redirect'] = 'forum';
            else
                $data['redirect'] = $redirect;

            $formhooks = xarModAPIFunc('xarbb','user','formhooks');
            $data['formhooks'] = $formhooks;

            break;

        case 'update':

            // Check arguments
            if (empty($ttitle)) {
                $warning = xarML('No Topic Subject Entered');
                xarResponseRedirect(xarModURL('xarbb', 'user', 'newtopic', array('fid' => $data['fid'], 'tpost' => $tpost, 'warning' => $warning)));
            }
            if (empty($tpost)) {
                $warning = xarML('No Topic Text Entered');
                xarResponseRedirect(xarModURL('xarbb', 'user', 'newtopic', array('fid' => $data['fid'], 'ttitle' => $ttitle, 'warning' => $warning)));
            }

            if(isset($tid))    {
                 $modified_date=date('F d, Y g:i A');
                 $tpost .= "\n";
                 $tpost .=xarML('[Modified by: #(1) (#(2)) on #(3)]',
                     xarUserGetVar('name'),
                     xarUserGetVar('uname'),
                     $modified_date);
                     $tpost .= "\n"; //Have to take this out with xarbb and html now handling paras.
                if (!xarModAPIFunc('xarbb',
                               'user',
                               'updatetopic',
                               array('tid' => $tid,
                                     'fid'      => $data['fid'],
                                     'ttitle'   => $ttitle,
                                     'tpost'    => $tpost))) return;
             } else    {
                 //Only update the user if new topic, not edited
                 $tposter = xarUserGetVar('uid');

                 if (!xarModAPIFunc('xarbb',
                               'user',
                               'createtopic',
                               array('fid'      => $data['fid'],
                                     'ttitle'   => $ttitle,
                                     'tpost'    => $tpost,
                                     'tposter'  => $tposter))) return;

                 // We don't want to update the forum counter on an updated reply.
                 if (!xarModAPIFunc('xarbb',
                                   'user',
                                   'updateforumview',
                                   array('fid'      => $data['fid'],
                                         'fposter'  => $tposter))) return;
             }

            if($redirect == 'topic')
                xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));
            else
                xarResponseRedirect(xarModURL('xarbb', 'user', 'viewforum', array('fid' => $data['fid'])));

            break;

    }
    //Add images
    $data['profile']    = '<img src="' . xarTplGetImage('infoicon.gif') . '" alt="'.xarML('Profile').'" />';

    // Return the output
    return $data;
}

//TODO FInish this function.
?>
