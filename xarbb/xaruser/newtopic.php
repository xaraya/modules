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
         $fid) = xarVarCleanFromInput('phase',
                                      'ttitle',
                                      'tpost',
                                      'warning',
                                      'fid');    

    // Security Check
    if(!xarSecurityCheck('ReadxarBB')) return;

    if (empty($phase)){
        $phase = 'form';
    }

    switch(strtolower($phase)) {

        case 'form':
        default:
            $data['fid'] = $fid;
            $data['authid'] = xarSecGenAuthKey();

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
            if (empty($warning)){
                $data['warning'] = '';
            } else {
                $data['warning'] = $warning;
            }

            $formhooks = xarbb_user_formhooks();
            $data['formhooks'] = $formhooks;

            break;

        case 'update':

            list($ttitle,
                 $tpost) = xarVarCleanFromInput('ttitle',
                                                 'tpost');

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
            
            if (!xarModAPIFunc('xarbb',
                               'user',
                               'createtopic',
                               array('fid'      => $fid,
                                     'ttitle'   => $ttitle,
                                     'tpost'    => $tpost,
                                     'tposter'  => $tposter))) return;

            if (!xarModAPIFunc('xarbb',
                               'user',
                               'updateforumview',
                               array('fid'      => $fid,
                                     'fposter'  => $tposter))) return;


            xarResponseRedirect(xarModURL('xarbb', 'user', 'viewforum', array('fid' => $fid)));

            break;
     
    }

    // Return the output
    return $data;
}

//TODO FInish this function.
?>
