<?php // $Id: s.xaruser.php 1.4 02/12/01 14:26:19+01:00 marcel@hsdev.com $
/*  main page - switch routines etc */


function todolist_user_updateuser($args)
{
    list($user_id, $user_email_notify, $user_primary_project, $user_my_tasks, $user_show_icons) = 
        pnVarCleanFromInput('new_user_id', 'new_user_email_notify', 'new_user_primary_project', 'new_user_my_tasks','new_user_show_icons');

    extract($args);
                            
    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', _BADAUTHKEY);
        pnRedirect(pnModURL('todolist', 'user', 'main'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'user')) {
        pnSessionSetVar('errormsg', _LOADFAILED);
        return $output->GetOutput();
    }

    if(pnModAPIFunc('todolist','user','updateuser',
                        array('user_email_notify' => $user_email_notify,
                        'user_primary_project' => $user_primary_project,
                        'user_my_tasks' => $user_my_tasks,
                        'user_show_icons' => $user_show_icons))) {
        pnSessionSetVar('statusmsg', xarML('Updated'));
    }
    pnRedirect(pnModURL('todolist', 'user', 'main'));

    return true;
}


?>