<?php

/**
 * add new forum
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

            $formhooks = xarbb_user_formhooks();
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

            $tposter = xarUserGetVar('uid');

            if(isset($tid))    {
                if (!xarModAPIFunc('xarbb',
                               'user',
                               'updatetopic',
                               array('tid' => $tid,
                                        'fid'      => $data['fid'],
                                     'ttitle'   => $ttitle,
                                     'tpost'    => $tpost,
                                     'tposter'  => $tposter))) return;
             } else    {
                if (!xarModAPIFunc('xarbb',
                               'user',
                               'createtopic',
                               array('fid'      => $data['fid'],
                                     'ttitle'   => $ttitle,
                                     'tpost'    => $tpost,
                                     'tposter'  => $tposter))) return;
             }

            if (!xarModAPIFunc('xarbb',
                               'user',
                               'updateforumview',
                               array('fid'      => $data['fid'],
                                     'fposter'  => $tposter))) return;

            if($redirect == 'topic')
                xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));
            else
                xarResponseRedirect(xarModURL('xarbb', 'user', 'viewforum', array('fid' => $data['fid'])));

            break;

    }

    // Return the output
    return $data;
}

//TODO FInish this function.
?>
