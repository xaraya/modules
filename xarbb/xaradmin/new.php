<?php

/**
 * add new forum
 */
function xarbb_admin_new()
{
    list($fname,
         $fdesc,
         $warning,
         $phase) = xarVarCleanFromInput('fname',
                                        'fdesc',
                                        'warning',
                                        'phase');

    // Security Check
    if(!xarSecurityCheck('AddxarBB',1,'Forum')) return;

    if (empty($phase)){
        $phase = 'form';
    }

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

            if (empty($fname)){
                $data['fname'] = '';
            } else {
                $data['fname'] = $fname;
            }
            if (empty($fdesc)){
                $data['fdesc'] = '';
            } else {
                $data['fdesc'] = $fdesc;
            }
            if (empty($warning)){
                $data['warning'] = '';
            } else {
                $data['warning'] = $warning;
            }

            $data['authid'] = xarSecGenAuthKey();
            break;

        case 'update':

            // Check arguments
            if (empty($fname)) {
                $warning = xarML('Forum Name is empty');
                xarResponseRedirect(xarModURL('xarbb', 'admin', 'new', array('fdesc' => $fdesc, 'warning' => $warning)));
            }
            if (empty($fdesc)) {
                $warning = xarML('Forum Description is empty');
                xarResponseRedirect(xarModURL('xarbb', 'admin', 'new', array('fname' => $fname, 'warning' => $warning)));
            }

            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;

            $tposter = xarUserGetVar('uid');

            // The API function is called
            if (!xarModAPIFunc('xarbb',
                               'admin',
                               'create',
                               array('fname'    => $fname,
                                     'fdesc'    => $fdesc,
                                     'fposter'  => $tposter))) return;

            // Get New Forum ID
            $forum = xarModAPIFunc('xarbb',
                                   'user',
                                   'getforum',
                                   array('fname' => $fname));

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