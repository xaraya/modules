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

    // Security Check
    if(!xarSecurityCheck('ReadxarBB',1,'Forum',"$fid:All")) return;

    if (empty($phase)){
        $phase = 'form';
    }

    switch(strtolower($phase)) {

        case 'form':
        default:
            if(isset($tid))	{
            	$data = xarModAPIFunc('xarbb','user','gettopic',array('tid' => $tid));
            } else  {
                $data['fid'] = $fid;
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
                xarResponseRedirect(xarModURL('xarbb', 'user', 'newtopic', array('fid' => $fid, 'tpost' => $tpost, 'warning' => $warning)));
            }
            if (empty($tpost)) {
                $warning = xarML('No Topic Text Entered');
                xarResponseRedirect(xarModURL('xarbb', 'user', 'newtopic', array('fid' => $fid, 'ttitle' => $ttitle, 'warning' => $warning)));
            }

            $tposter = xarUserGetVar('uid');

            if(isset($tid))	{
            	if (!xarModAPIFunc('xarbb',
                               'user',
                               'updatetopic',
                               array('tid' => $tid,
                               		 'fid'      => $fid,
                                     'ttitle'   => $ttitle,
                                     'tpost'    => $tpost,
                                     'tposter'  => $tposter))) return;
             } else	{
	            if (!xarModAPIFunc('xarbb',
                               'user',
                               'createtopic',
                               array('fid'      => $fid,
                                     'ttitle'   => $ttitle,
                                     'tpost'    => $tpost,
                                     'tposter'  => $tposter))) return;
             }

            if (!xarModAPIFunc('xarbb',
                               'user',
                               'updateforumview',
                               array('fid'      => $fid,
                                     'fposter'  => $tposter))) return;

            if($redirect == 'topic')
            	xarResponseRedirect(xarModURL('xarbb', 'user', 'viewtopic', array('tid' => $tid)));
            else
	            xarResponseRedirect(xarModURL('xarbb', 'user', 'viewforum', array('fid' => $fid)));

            break;

    }

    // Return the output
    return $data;
}

//TODO FInish this function.
?>