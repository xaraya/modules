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
	if (!xarVarFetch('phase', 'str:1:10', $phase, 'form', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
	if (!xarVarFetch('ttitle', 'str:1:100', $ttitle, '', XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('tpost', 'str', $tpost, '', XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('tstatus', 'int', $tstatus, '', XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('fid', 'id', $fid, NULL, XARVAR_DONT_SET)) return;
	if (!xarVarFetch('tid', 'id', $tid, NULL, XARVAR_DONT_SET)) return;
	if (!xarVarFetch('fid', 'id', $fid, NULL, XARVAR_DONT_SET)) return;
	if (!xarVarFetch('redirect', 'str', $redirect, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('preview',  'isset', $preview,  NULL, XARVAR_DONT_SET)) return;

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
    if (isset($fid)){
        $data['fid']            = $fid;
    }
    $settings               = unserialize(xarModGetVar('xarbb', 'settings.'.$data['fid']));
    $data['allowhtml']      = $settings['allowhtml'];
    if (empty($data)) return;

    // Security Check

    if(isset($tid))    {
        $data['tid']=$tid;
        if(!xarSecurityCheck('ModxarBB',1,'Forum',$data['catid'].':'.$data['fid'])) return;
    }
    else    {
        if(!xarSecurityCheck('PostxarBB',1,'Forum',$data['catid'].':'.$data['fid'])) return;

    }

    if (isset($preview)){
       $phase = 'form';
    }
 
    switch(strtolower($phase)) {

        case 'form':
        default:
            if (isset($tid))  {
                if (isset($preview)) {
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
                }
                $item = $data;
                $item['module'] = 'xarbb';
                $item['itemtype'] =2;// Forum Topics
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
                $item['itemtype'] = 2;// Forum Topics
                $item['itemid'] = '';
                $data['hooks'] = xarModCallHooks('item','new','',$item);
            }

            $data['authid'] = xarSecGenAuthKey();

            if (empty($warning)){
                $data['warning'] = '';
            } else {
                $data['warning'] = $warning;
            }

            if(empty($redirect)) {
                $data['redirect'] = 'forum';
            } else {
                $data['redirect'] = $redirect;
            }

            $formhooks = xarModAPIFunc('xarbb','user','formhooks');
            $data['formhooks']      = $formhooks;
            $data['submitlabel']    = xarML('Submit');
            $data['previewlabel']   = xarML('Preview');

            break;

        case 'update':

            if(isset($tid))    {
                 $modified_date= xarLocaleFormatDate('%d %B %Y %H:%M:%S %Z',time());
                 $tpost .= "\n";
                 $tpost .=xarML('[Modified by: #(1) (#(2)) on #(3)]',
                     xarUserGetVar('name'),
                     xarUserGetVar('uname'),
                     $modified_date);
                     $tpost .= "\n"; //Have to take this out with xarbb and html now handling paras.

                if (!xarModAPIFunc('xarbb',
                                   'user',
                                   'updatetopic',
                               array('tid'      => $tid,
                                     'fid'      => $data['fid'],
                                     'ttitle'   => $ttitle,
                                     'tpost'    => $tpost,
                                     'tstatus'  => $tstatus))) return;
             } else {
                 //Only update the user if new topic, not edited
                 $tposter = xarUserGetVar('uid');

                 if (!xarModAPIFunc('xarbb',
                               'user',
                               'createtopic',
                               array('fid'      => $data['fid'],
                                     'ttitle'   => $ttitle,
                                     'tpost'    => $tpost,
                                     'tposter'  => $tposter,
                                     'tstatus'  => $tstatus))) return;
                 // NNTP?
                 $settings   = unserialize(xarModGetVar('xarbb', 'settings.'.$fid));
                 if ($settings['linknntp']){
                     if (!xarModAPIFunc('xarbb',
                                   'user',
                                   'sendnntp',
                                   array('fid'      => $data['fid'],
                                         'ttitle'   => $ttitle,
                                         'tpost'    => $tpost,
                                         'tposter'  => $tposter))) return;
                 }

                 // We don't want to update the forum counter on an updated reply.
                 if (!xarModAPIFunc('xarbb',
                                   'user',
                                   'updateforumview',
                                   array('fid'      => $data['fid'],
                                         'topics'   => 1,
                                         'move'     => 'positive',
                                         'replies'  => 1,
                                         'fposter'  => $tposter))) return;
             }


            if($redirect == 'topic')
                xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));
            else
                xarResponseRedirect(xarModURL('xarbb', 'user', 'viewforum', array('fid' => $data['fid'])));

            break;

    }
     //Now we have everything, transform
            list($data['transformedtext'],
            $data['transformedtitle']) = xarModCallHooks('item',
                                                         'transform',
                                                         $tid,
                                                     array($data['tpost'],
                                                           $data['ttitle']),
                                                           'xarbb',
                                                           $fid);
    //Make sure we return the preview state
    $data['preview']=$preview;
    // Return the output
   return $data;
}
?>
