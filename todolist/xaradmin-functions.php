<?php // $Id: s.xaradmin-functions.php 1.3 02/12/01 14:39:54+00:00 miko@miko.homelinux.org $
/**
 * creates a HTML-dropdownbox with the availible Users
 *
 * @param $myname            string    Name of the form-variable
 * @param $selected_names    Array    Array containing the usernr
 * @param $emty_choice        Boolean    Should an emty-entry be created? [1,0,true,false]
 * @param $multiple            Boolean    Allow multiple selects? [1,0,true,false]
 * @param $multiple            string    'all' - users from nuke_users, '' - users from nuke_todo_users
 * @return HTML containing the dropdownbox
 */
function makeUserDropdownList($myname,$selected_names,$selected_project, $emty_choice, $multiple, $all) {
    global $route, $page;

    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    if ($all == 'all') {
       $result = $dbconn->Execute("SELECT pn_uid, pn_uname FROM $pntable[users] ORDER BY pn_uname");
       $usercnt = $result->PO_RecordCount();
    } else {
       $todolist_users_column = &$pntable['todolist_users_column'];    
       $result = $dbconn->Execute("SELECT $todolist_users_column[usernr] FROM $pntable[todolist_users]");
       $usercnt = $result->PO_RecordCount();
    }

    $str = "";
    if ($multiple) {
        if ($usercnt > 100) {
            $size=15;
        } elseif ($usercnt > 50) {
            $size=10;
        } elseif ($usercnt > 25) {
            $size=7;
        } elseif ($usercnt > 5) {
            $size=6;
        } elseif ($usercnt <= 5) {
            $size=$usercnt;
        }
        $myname=$myname . "[]";
        $str .= '<select multiple="multiple" name="'.$myname.'" size="'.$size.'">';
    } else  {
        $str .= '<select name="'.$myname.'" size="1">';
    }

    if ($emty_choice) {
        if ("$selected_names[0]" == "")  {
            $str .= '<option selected="selected" VALUE="">';
        } else {
            $str .= '<option value="">';
        }
    } 
    if ($usercnt > 0)
    {
        for (;!$result->EOF;$result->MoveNext())
        {

            if ($all == 'all') {
                $usernr = $result->fields[0];
                $user_name = $result->fields[1];
            } else {
                $usernr = $result->fields[0];
                $user_name  = stripslashes(pnUserGetVar('name',$usernr));
                if (empty($user_name)) $user_name  = stripslashes(pnUserGetVar('uname',$usernr));
            }

            $inlist = 0;
            @reset($selected_names);
            while (@list(, $value) = @each ($selected_names)) {
                if ($value == "$usernr"){
                    $inlist = 1;
                }
            }
            if ($inlist == 1) {
                $str .= '<option selected="selected" value="'.$usernr.'">'.$user_name;
            } else {
                $str .= '<option value="'.$usernr.'">'.$user_name;
            }
            $str .= "</option>\n";
        }
    }
    $str .= '</select>';
    return $str;
}
?>