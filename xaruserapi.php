<?php // $Id$
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WIthOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Volodymyr Metenchuk
// Purpose of file:  Todolist user API
// ----------------------------------------------------------------------





function todolist_userapi_updateuser($args)
{
    extract($args);
    
    if ((!isset($user_id)) && (!isset($user_email_notify)) && (!isset($user_primary_project)) &&
        (!isset($user_my_tasks)) && (!isset($user_show_icons))) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }
    //HTML-Forms submit nothing if a checkbox isn't checked... :-(
    if (!isset($user_email_notify)) { $user_email_notify = 0; }
    if (!isset($user_primary_project)) { $user_primary_project = 0; }
    if (!isset($user_my_tasks)) { $user_my_tasks = 0; }
    if (!isset($user_show_icons)) { $user_show_icons = 0; }

    if ($user_id != pnUserGetVar('uid')) {
        pnSessionSetVar('errormsg', xarML('Not authorised to access Todolist module'));
        return false;
    }

    $userpref = $new_email_notify.';'.$new_primary_project.';'.$new_my_tasks.';'.$showicons;
    xarModSetUserVar('todolist','userpref',$userpref,$user_id);

    pnSessionSetVar('errormsg', xarML('User info was updated'));
    return true;
}




?>