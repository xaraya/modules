<?php // $Id: s.functions.inc.php 1.4 02/07/03 09:50:19-00:00 mikespub $
/**
 * Functions library
 */

/**
 * updates a todo-entry
 *
 * Updates a Todo-Entry into database and generates a mail-notify to subscribed users.
 *
 * @param $due_date        string    the due date
 * @param $priority
 * @param $status
 * @param $percentage_completed
 * @param $text
 * @param $responsible_persons
 * @param $id
 * @param $note_text
 * @param $selected_project
 */
function update_todo($due_date, $priority, $status, $percentage_completed, $text, $responsible_persons, $id,
        $note_text, $selected_project)
{
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();
    /* Das Datum muß evtl. wieder nach US-Format konvertiert werden... */
    if (pnModGetVar('todolist', 'DATEFORMAT') != "1") {
        $due_date=convDateToUS($due_date);
    }
    $priority = switchPriority($priority);

    $todolist_todos_column = &$pntable['todolist_todos_column'];
    $query = "UPDATE $pntable[todolist_todos]
        SET $todolist_todos_column[todo_priority]=$priority,
        $todolist_todos_column[status]=$status,
        $todolist_todos_column[project_id]=$selected_project,
        $todolist_todos_column[percentage_completed]=$percentage_completed,
        $todolist_todos_column[todo_text]='".addslashes($text)."', "
        ."$todolist_todos_column[due_date]='$due_date', $todolist_todos_column[date_changed]=".time().", "
        ."$todolist_todos_column[changed_by]=" .pnUserGetVar('uid')
        ." where $todolist_todos_column[todo_id]='$id'";

    $querystatus = $dbconn->Execute($query);

    // update responsible_persons
    if ((count($responsible_persons) > 0) && ($querystatus == true)) {

        // delete old responsibilities
        $todolist_responsible_persons_column = &$pntable['todolist_responsible_persons_column'];
        if (!$dbconn->Execute("DELETE from $pntable[todolist_responsible_persons]
            WHERE $todolist_responsible_persons_column[todo_id]=$id")) {
            return false;
        }

        $query = "INSERT INTO $pntable[todolist_responsible_persons] VALUES ";

        $anzahl = count($responsible_persons);
        for ($i=0; $i < $anzahl ; $i++) {
            $query .= "($id, $responsible_persons[$i])";
            if (($i+1) < $anzahl)
                $query .= ", ";
        }

        $querystatus = $dbconn->Execute($query);
    }

    // if new note entered insert it into DB
    if (($note_text != "") && ($querystatus == true)) {
        // not needed.
        // $todolist_todos_column = &$pntable['todolist_todos_column'];
        // $dbconn->Execute("UPDATE $pntable[todolist_todos] set $todolist_todos_column[date_changed]=".time()." where $todolist_todos_column[todo_id]='$id'");
        $todolist_notes_column = &$pntable['todolist_notes_column'];
        $sql = "INSERT INTO $pntable[todolist_notes]
            ($todolist_notes_column[todo_id], $todolist_notes_column[text],
            $todolist_notes_column[usernr], $todolist_notes_column[date]) VALUES
            ('$id', '" .addslashes($note_text)."', ".pnUserGetVar('uid').", ".time().")";
        $querystatus = $dbconn->Execute($sql);
    }

    generateMail($id, "todo_change");
    return ($querystatus);
}

/**
 * add a todo-entry
 *
 * Inserts a Todo-Entry into database and generates a mail-notify to subscribed users.
 *
 * @param $due_date        string    the due date
 * @param $priority
 * @param $project
 * @param $percentage_completed
 * @param $text
 * @param $responsible_persons
 * @param $note_text
 */
function add_todo($due_date,$priority,$project,$percentage_completed,$text,$responsible_person,$note_text)
{
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();
    if (pnModGetVar('todolist', 'DATEFORMAT') != "1") {
        // datum is the due date
        $due_date = convDateToUS($due_date);
    }
    $priority = switchPriority($priority);

    if (pnUserGetVar('uid')) {
        $todolist_todos_column = &$pntable['todolist_todos_column'];
        $query = "INSERT INTO $pntable[todolist_todos]
            ($todolist_todos_column[project_id], $todolist_todos_column[todo_text],
            $todolist_todos_column[todo_priority], $todolist_todos_column[percentage_completed],
            $todolist_todos_column[created_by], $todolist_todos_column[due_date],
            $todolist_todos_column[date_created], $todolist_todos_column[date_changed],
            $todolist_todos_column[changed_by]) VALUES ('$project','".
            addslashes($text)."', '$priority', 0, '".pnUserGetVar('uid').
            "','$due_date',".time().",".time().",'".pnUserGetVar('uid')."')";

        if ($dbconn->Execute($query)){
            $insert_success = true;
        } else {
            $insert_success = false;
        }
        if ((count($responsible_person) > 0) && ($insert_success == true)) {
            $query = "INSERT INTO $pntable[todolist_responsible_persons] VALUES ";

            $anzahl = count($responsible_person);
            for ($i=0; $i < $anzahl ; $i++) {
                $query .= "(LAST_INSERT_ID(), $responsible_person[$i])";
                if (($i+1) < $anzahl)
                    $query .= ", ";
            }

            if ($dbconn->Execute($query)){
                $insert_success = true;
            } else {
                $insert_success = false;
            }
            generateMail("LAST_INSERT_ID()", "todo_add");
        }

    } else {
        // not authenticated - don't allow them to add it.
        echo 'Authentication Failure!';
        return false;
    }

    return $insert_success;
} // end of add_todo

/**
 * deletes a todo-entry
 *
 * Deletes a Todo-Entry, and all associated notes. A mail-notify is not generated!
 *
 * @param $todo_id    int    the primary key of the todo-entry
 */
function delete_todo($todo_id)
{
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();
    if (pnUserGetVar('uid')) {
        // TODO: Add mail-notification
        generateMail($todo_id, "todo_delete");
        
        $todolist_notes_column = &$pntable['todolist_notes_column'];
        $todolist_responsible_persons_column = &$pntable['todolist_responsible_persons_column'];
        $todolist_responsible_groups_column = &$pntable['todolist_responsible_groups_column'];
        $todolist_todos_column = &$pntable['todolist_todos_column'];
        
        $dbconn->Execute("DELETE FROM $pntable[todolist_notes]
            WHERE $todolist_notes_column[todo_id]=$todo_id");
        $dbconn->Execute("DELETE FROM $pntable[todolist_responsible_persons]
            WHERE $todolist_responsible_persons_column[todo_id]=$todo_id");
        $dbconn->Execute("DELETE FROM $pntable[todolist_responsible_groups]
            WHERE $todolist_responsible_groups_column[todo_id]=$todo_id");
        $dbconn->Execute("DELETE FROM $pntable[todolist_todos]
            WHERE $todolist_todos_column[todo_id]=$todo_id");
    } else {
        // not authenticated - don't allow them to delete it.
        echo 'Authentication Failure!';
        return false;
    }

    return true;
} // end of delete_todo

/**
 * creates the SQL-Query to retrieve the main todo-table
 *
 * @param $user_id            int        the primary key of the group
 * @param $order_by            string    How should the table be ordered?
 * @param $selected_project    int        which project should be shown
 */
function makeFrontQuery($order_by, $selected_project)
{
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $todolist_todos_column = &$pntable['todolist_todos_column'];
    $todolist_responsible_persons_column = &$pntable['todolist_responsible_persons_column'];
    $todolist_project_members_column = &$pntable['todolist_project_members_column'];
    $todolist_notes_column = &$pntable['todolist_notes_column'];

    $query="SELECT $pntable[todolist_todos].*, count(distinct($todolist_notes_column[note_id])) AS nr_notes 
        FROM $pntable[todolist_todos], $pntable[todolist_responsible_persons], $pntable[todolist_project_members]
        LEFT JOIN $pntable[todolist_notes]
        ON $todolist_todos_column[todo_id]=$todolist_notes_column[todo_id]
        WHERE $todolist_todos_column[status] < 6";

    if ($selected_project != "all") {
        $query .= " AND $todolist_todos_column[project_id]=".$selected_project;
    } else {
        $todolist_project_members_column = &$pntable['todolist_project_members_column'];
        $sql2 = "SELECT $todolist_project_members_column[project_id]
            FROM $pntable[todolist_project_members]
            WHERE $todolist_project_members_column[member_id]=".pnUserGetVar('uid')."";
        $result = $dbconn->Execute($sql2);

        for (;!$result->EOF;$result->MoveNext()){
            $projects[] = $result->fields[0];
        }
        if ($projects[0]!="") {
            $query.=" AND $todolist_todos_column[project_id] in (";
            while ($neu=array_pop($projects)){
                $query .= $neu;
                if (sizeof($projects) > 0)
                    $query .= ',';
                else
                    $query .= ') ';
            }
        }

    }

    if (pnSessionGetVar('todolist_my_tasks') == 1 ) {
        // show only tasks where I'm responsible for
        $query .= " 
            AND $todolist_responsible_persons_column[user_id] = ".pnUserGetVar('uid')."
            AND $todolist_todos_column[todo_id] = $todolist_responsible_persons_column[todo_id]";
    }

    $query .= " GROUP BY $todolist_todos_column[todo_id] ";
    $query .= orderBy($order_by);

    return $query;
}
//end makeFrontQuery


/**
 * creates the SQL-Query for the search-form
 *
 * @param $wildcards                    the primary key of the group
 * @param $priority                        priority to search for
 * @param $status                        the status to search for
 * @param $project
 * @param $responsible_persons
 * @param $order_by                string    How should the table be ordered?
 * @param $date_min
 * @param $date_max
 */
function makeSearchQuery($wildcards,$priority, $status, $project, $responsible_persons,$order_by,$date_min,$date_max)
{
    global $abfrage;

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();
    /* Generate the SQL-Statement */
    $todolist_todos_column = &$pntable['todolist_todos_column'];
    $todolist_responsible_persons_column = &$pntable['todolist_responsible_persons_column'];
    $todolist_notes_column = &$pntable['todolist_notes_column'];

    $query="SELECT $pntable[todolist_todos].*, count($todolist_notes_column[todo_id]) AS nr_notes
        FROM $pntable[todolist_todos], $pntable[todolist_responsible_persons]
        LEFT JOIN $pntable[todolist_notes]
        ON $todolist_todos_column[todo_id]=$todolist_notes_column[todo_id]
        WHERE $todolist_todos_column[todo_text] LIKE ";

    if ($wildcards) {
        $query=$query . "'%$abfrage%' "; 
    } else {
        $query=$query . "'$abfrage' "; 
    }

    if ($priority!=""){
        $query=$query . "AND $todolist_todos_column[todo_priority]=$priority "; 
    }

    if ($status!="" && $status != "all"){
        $query=$query . "AND $todolist_todos_column[status]=$status "; 
    }

    if ($project!=""){
        if ($project != "all") {
            $query=$query . "AND $todolist_todos_column[project_id]=$project "; 
        } else {
            $todolist_project_members_column = &$pntable['todolist_project_members_column'];
            $sql2 = "SELECT $todolist_project_members_column[project_id]
               FROM $pntable[todolist_project_members]
               WHERE $todolist_project_members_column[member_id]=".
               pnUserGetVar('uid')."";
            $result = $dbconn->Execute($sql2);

            for (;!$result->EOF;$result->MoveNext()){
                $projects[] = $result->fields[0];
            }
            if ($projects[0]!="") {
                $query.=" AND $todolist_todos_column[project_id] in (";

                        while ($neu=array_pop($projects)){
                        $query .= $neu;
                        if (sizeof($projects) > 0)
                        $query .= ',';
                        else
                        $query .= ') ';
                        }
            }

        }
    }

    if ( ereg( "([0-9]{1,2})([.-/]{0,1})([0-9]{1,2})([.-/]{0,1})([0-9]{2,4})", trim($date_min), $regs ) ) {
        $date_min = mktime(0,0,0,$regs[1],$regs[2],$regs[0]);
    }
    if ( ereg( "([0-9]{1,2})([.-/]{0,1})([0-9]{1,2})([.-/]{0,1})([0-9]{2,4})", trim($date_max), $regs ) ) {
        $date_max = mktime(0,0,0,$regs[1],$regs[2],$regs[0]);
    }
    if (!$date_min){
        $date_min = "0";
    }

    if (!$date_max){
        $date_max = time();
    }

/*
    if (pnModGetVar('todolist', 'DATEFORMAT') != "1" ) {
        $date_min=convDateToUS($date_min);
        $date_max=convDateToUS($date_max);
    }
    if (!$date_min){ $date_min = "0000-00-00"; }
    if (!$date_max){ $date_max = date("Y-m-d");}
*/

    $query=$query . "AND $todolist_todos_column[date_changed] >= '$date_min'
    AND $todolist_todos_column[date_changed] <= '$date_max' ";

    /* sizeof(array) > 0 doesn't work? */
    if ($responsible_persons[0]!="") {
        $query.=" AND $todolist_responsible_persons_column[user_id] in (";

                while ($neu=array_pop($responsible_persons)){
                $query .= $neu;
                if (sizeof($responsible_persons) > 0)
                $query .= ',';
                else
                $query .= ') ';
                }
    }
    $query .= "AND $todolist_responsible_persons_column[todo_id]=$todolist_todos_column[todo_id]";

    $query=$query . " GROUP BY $todolist_todos_column[todo_id] ";

    // How should the table be ordered?
    $query .= orderBy($order_by);
    return $query;
}
// end makeSearchQuery

/**
 * switches priority-notation from string to int and vice-versa
 *
 * TODO: get rid of this somehow...
 *
 * @param $priority    mixed    The prio represented as string or int
 * @return mixed The switched value
 */
function switchPriority($priority)
{
    switch ($priority)
    {
        case xarML('high'):
            $priority = 1;
        break;

        case xarML('med'):
            $priority = 2;
        break;

        case xarML('low'):
            $priority = 3;
        break;

        /*
           case xarML('done'):
           $priority = 4;
           break;
         */

        case "1":
            $priority = xarML('high');
        break;

        case "2":
            $priority = xarML('med');
        break;

        case "3":
            $priority = xarML('low');
        break;

        /*
           case "4":
           $priority = xarML('done');
           break;
         */
    } //switch 
    return $priority;
}

/**
 * generates a mail to all responsible persons and shows up what happened to a task.
 * @param id of the task
 * @param the performed action
 */
function generateMail($id,$action)
{
    if (!pnModGetVar('todolist', 'SEND_MAILS'))
        return;

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();
    $message_headers="From: ToDo-List <webmaster@$SERVER_NAME>\n";
    $message_headers.="MIME-Version: 1.0\n";
    $message_headers.="Content-type: multipart/mixed; boundary=\"simple boundary\"\n";
    $message_headers.="X-Mailer: PHP/" . phpversion() . "\n";

    $message_preamble="\nThis is a multi-part message in MIME format.\n";
    $message_boundary="--simple boundary\n";

    // Get all responsible users first (needed to get the details of the users (email!) later as MySQL
    // doesn't support subselects... :-(
    $todolist_responsible_persons_column = &$pntable['todolist_responsible_persons_column'];
    $result = $dbconn->Execute("SELECT $todolist_responsible_persons_column[user_id]
        FROM $pntable[todolist_responsible_persons]
        WHERE $todolist_responsible_persons_column[todo_id]=$id");

    for (;!$result->EOF;$result->MoveNext()){
        $responsible_users[] = $result->fields[0];
    }

    $message_html .= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
        <html><head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        </head>
        <body>';
    if ($responsible_users[0]!="") {
        // responsible users found: generate mail-text
        $result->Close();

        
        $todolist_todos_column = &$pntable['todolist_todos_column'];
        $todolist_projects_column = &$pntable['todolist_projects_column'];
        if (!($result = $dbconn->Execute("SELECT $pntable[todolist_todos].*,
                    $todolist_projects_column[project_name]
                    FROM $pntable[todolist_todos], $pntable[todolist_projects]
                    WHERE $todolist_todos_column[project_id]=$todolist_projects_column[id]
                    AND $todolist_todos_column[todo_id]=".$id))) {
            return false;
        }

        $id                   = $result->fields[0];
        $project              = $result->fields[1]." - ".$result->fields[11];
        $text                 = stripslashes($result->fields[2]);
        $priority             = $result->fields[3];
        $percentage_completed = $result->fields[4];
        $due_date             = $result->fields[6];
        $date_created         = $result->fields[7];
        $date_changed         = $result->fields[8];
        $created_by           = $result->fields[5];
        $changed_by           = $result->fields[9];

        $message_text.="\nproject:               $project";
        $message_text.="\npriority:              " . switchPriority($priority);
        $message_text.="\npercentage completed:  $percentage_completed";
        $message_text.="\ndue date:              $due_date";
        $message_text.="\ndate created:          " . strftime("%Y-%m-%d",$date_created);
        $message_text.="\ndate changed:          " . strftime("%Y-%m-%d %H:%M:%S",$date_changed);
        $message_text.="\ntext:                  $text";

        $message_html .='<table>
            <tr><td>project</td><td>' . $project . '</td></tr>
            <tr><td>priority</td><td>' . switchPriority($priority) . '</td></tr>
            <tr><td>percentage completed</td><td>' . $percentage_completed . '</td></tr>
            <tr><td>due date</td><td>' . $due_date . '</td></tr>
            <tr><td>date_created</td><td>' . strftime("%Y-%m-%d",$date_created) . '</td></tr>
            <tr><td>date_changed</td><td>' . strftime("%Y-%m-%d %H:%M:%S",$date_changed) . '</td></tr>
            <tr><td>text</td><td>' . $text . '</td></tr>
            </table>
            ';

        // get the notes:
        $todolist_notes_column = &$pntable['todolist_notes_column'];
        $users_column = &$pntable['users_column'];
        $result = $dbconn->Execute("SELECT $todolist_notes_column[text],
                $todolist_notes_column[date],xar_uid
                FROM $pntable[todolist_notes], $pntable[roles]
                WHERE $todolist_notes_column[todo_id]=$id
                AND $todolist_notes_column[usernr]=xar_uid");

        if ($result->PO_RecordCount() > 0 ) {
            $message_text .= "\n\nNotes:\n";
            $message_html .='<br /><b>Notes:</b><table>';
            $message_html .='<tr><th>Text</th><th>user</th><th>date</th></tr>';

            for (;!$result->EOF;$result->MoveNext()) {
                $text        = stripslashes($result->fields[0]);
                $date        = $result->fields[1];
                $user_name  = stripslashes(pnUserGetVar('name',$result->fields[2]));
                if (empty($user_name)) $user_name  = stripslashes(pnUserGetVar('uname',$result->fields[2]));

                $message_text .= "\n\"$text\"";
                $message_text .= "\nby $user_name on ".strftime("%Y-%m-%d %H:%M:%S",$date);
                $message_html .= "<tr><td>$text</td><td>$user_name</td><td>$date</td><tr>\n";
            }
            $message_html .="</table>";
        }

        // get the receipients 
        $users_column = &$pntable['users_column'];
        $query ="SELECT $users_column[usernr] FROM $pntable[roles]";
        $query.=" WHERE xar_uid in (";

        while ($neu=array_pop($responsible_users)){
            $query .= $neu;
            if (sizeof($responsible_users) > 0)
                $query .= ',';
            else
                $query .= ') ';
        }
        // FIXME
        // $query .= " AND $todolist_users_column[email_notify] != 0";

        $result = $dbconn->Execute($query);

        $message_text .="\n";
        $message_html .="</bODY></HTML>\n";

        $message .= $message_preamble;
        $message .= $message_boundary;
        $message .= "Content-type: text/plain; charset=iso-8859-1\n";
        $message .= "Content-Transfer-Encoding: quoted-printable\n\n";
        // it would be nice to kill html-formating here and save the creation of the text-version...
        // but the HTML-code must be indented nicely then...
        $message .= $message_text;
        $message .= $message_boundary;
        $message .= "Content-Type: text/html; charset=\"iso-8859-1\"\n";
        $message .= "Content-Transfer-Encoding: quoted-printable\n\n";
        $message .= $message_html;
        $message .= $message_boundary;

        switch ($action) {
            case "todo_delete":
                $subject = "Task deleted.";
                break;
            case "todo_change":
                $subject = "Task changed.";
                break;
            case "todo_add":
                $subject = "Task added.";
                break;
            default:
                $subject = "Status for TODO-entry $id";
                break;
        }

        for (;!$result->EOF;$result->MoveNext()){
//                pnMail(pnUserGetVar('email',$result->fields[0]), "$subject", $message, $message_headers);
        }
    } else {
        // no responsible users found so no mail hast to be generated.
        return;
    }
} // end of generateMail();

/**
 * generates the page with all availible options for a user.
 * @return HTML
 */
function userDialog()
{
//    $output = new pnHTML();
    if (!pnModAPILoad('todolist', 'user')) {
//        $output->Text(xarML('Load of module failed'));
//        return $output->GetOutput();
          return xarML('Load of module failed');
    }
    $item = pnModAPIFunc('todolist','user','getuser',
            array('user_id' => pnUserGetVar('uid')));
    if ($item == false) {
//        $output->Text(xarML('No such user'));
//        return $output->GetOutput();
          return xarML('No such user');
    }

    $usernr          = $item['user_id'];
    $email_notify    = $item['user_email_notify'];
    $primary_project = $item['user_primary_project'];
    $new_my_tasks    = $item['user_my_tasks'];
    $showicons       = $item['user_show_icons'];

    $str = '<form action="'.
        pnModURL('todolist', 'user', 'updateuser', array()).'" method="post">
        <input type="hidden" name="authid" value=".'.pnSecGenAuthKey().'" />
        <input type="hidden" name="new_user_id" value="'.$usernr.'" />
        <table><tr><td><b>'.xarML('username').
        "</b></td><td>".pnUserGetVar('uname',$usernr).'</td></tr>
        <tr><td><b>'.xarML('Notify changes via email?').'</b></td>
        <td><input type="checkbox" name="new_user_email_notify" value="1"';
    if ($email_notify==1) {
        $str .= 'checked="checked"';
    }
    $str .= '/></td></tr><tr><td><b>'.xarML('primary project').'</b></td><td>'.
            makeProjectDropdown("new_user_primary_project", $primary_project, true).
            '</td></tr><tr><td><b>'.xarML('Show only my tasks').
            '</b></td><td><input type="checkbox" name="new_user_my_tasks" value="1"';
    if ($new_my_tasks==1) {
        $str .= 'checked="checked"';
    }
    $str .= '/></td></tr><tr><td><b>'.xarML('Show icons?').'</b></td>
            <td><input type="checkbox" name="new_user_show_icons" value="1" ';
    if ($showicons=='1') {
        $str .= 'checked="checked"';
    }
    $str .= '/></td></tr><tr><td>&nbsp;</td>
            <td><input type="submit" value="'.xarML('submit').'" /></td>
            </tr></table></form>';
    return $str;
}

/**
 * creates a HTML-dropdownbox with the availible Users
 *
 * @param $myname            string    Name of the form-variable
 * @param $selected_names    Array    Array containing the usernr
 * @param $emty_choice        Boolean    Should an emty-entry be created? [1,0,true,false]
 * @param $multiple            Boolean    Allow multiple selects? [1,0,true,false]
 * @return HTML containing the dropdownbox
 */
function makeUserDropdown($myname,$selected_names,$selected_project, $emty_choice, $multiple)
{
    global $route, $page;

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();
    $str = "";

    if (empty($selected_names)) {
        $selected_names = array();
    }
    if (empty($selected_project)) {
        $selected_project = 'all';
    }

    $users_column = &$pntable['users_column'];
    $todolist_project_members_column = &$pntable['todolist_project_members_column'];

    $query="SELECT distinct(xar_uid)
            FROM $pntable[roles], $pntable[todolist_project_members]";
    if ($selected_project != "all") {
        $query .= " WHERE xar_uid=$todolist_project_members_column[member_id]
                    AND $todolist_project_members_column[project_id]=".$selected_project;
    } else {
        $query .= " WHERE $todolist_project_members_column[project_id] in ".
                  pnSessionGetVar('todolist_my_projects');
    }

    $result = $dbconn->Execute($query);
    $usercnt = $result->PO_RecordCount();

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
        $str .= '<select multiple="multiple" name="'.$myname.'" size="'.$size.'">
            ';
    } else  {
        $str .= '<select name="'.$myname.'" size="1">
            ';
    }

    if ($emty_choice) {
        if ("$selected_names[0]" == "")  {
            $str .= '<option selected="selected" value="">';
        } else {
            $str .= '<option value="">';
        }
    } 
    $i = 0;
    if ($usercnt > 0)
    {
        for (;!$usercnt;$usercnt--)
        {
            $usernr = $result->fields[0];
            $result->MoveNext();
            $user_name  = stripslashes(pnUserGetVar('name',$usernr));
            if (empty($user_name)) $user_name  = stripslashes(pnUserGetVar('uname',$usernr));

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
            $i++;
        }
    }
    $str .= '</select>';
    return $str;
}

/**
 * creates a HTML-Dropdownbox with the availible projects
 *
 * @param $myname            String    Name of the form-variable
 * @param $selected_project    int        the project currently selected
 * @param $all                Boolean    should the all-entry be there?
 * @param $java                String    which JavaScript-function schould be called?
 * @return String containing the HTML
 *
function makeProjectDropdown($myname,$selected_project,$all=false, $java=false)
{
    global $page, $route;

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();
    $str = "";

    // If we are not the admin do only get the projects we're member of.
    $todolist_projects_column = &$pntable['todolist_projects_column'];
    $todolist_project_members_column = &$pntable['todolist_project_members_column'];
    $sql2 = "SELECT * FROM $pntable[todolist_projects],
                $pntable[todolist_project_members]
                WHERE $todolist_projects_column[id] = $todolist_project_members_column[project_id]
                and $todolist_project_members_column[member_id] = ".pnUserGetVar('uid')."";
    $result = $dbconn->Execute($sql2);

    $str .= '<select name="'.$myname.'" size="1"';
    if ($java) {
        $str .= ' onchange="'.$java.'"';
    }
    $str .= '>';

    // all means all projects the user is member off
    if ($all) {
        if ($selected_project == "all" ) {
            $str .= '<option selected="selected" value="all">'.xarML('all');
        } else {
            $str .= '<option value="all">'.xarML('all');
        }
        $str.='</option>';
    }

    for (;!$result->EOF;$result->MoveNext())
    {
        $project_id   = $result->fields[0];
        $project_name = stripslashes($result->fields[1]);

        if ($project_id == $selected_project) {
            $str .= '<option selected="selected" value="'.$project_id.'">'.$project_name;
        } else {
            $str .= '<option value="'.$project_id.'">'.$project_name;
        }
        $str .= "</option>\n";
    }

    $str .= '</select>';
    return $str;
}
*/
function makeProjectDropdown($myname,$selected_project,$all=false, $java=false)
{
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $todolist_projects_column = &$pntable['todolist_projects_column'];
    $todolist_project_members_column = &$pntable['todolist_project_members_column'];
    if (pnSecAuthAction(0, 'todolist::', '::', ACCESS_ADMIN)) {
        // Admin needs to see all projects.
        $sql2 = "SELECT $todolist_projects_column[id],$todolist_projects_column[project_name]
                FROM $pntable[todolist_projects]";
    } else {
        // If we are not the admin do only get the projects we're member of.
        $sql2 = "SELECT $todolist_projects_column[id],$todolist_projects_column[project_name]
                FROM $pntable[todolist_projects],$pntable[todolist_project_members]
                WHERE $todolist_projects_column[id] = $todolist_project_members_column[project_id]
                and $todolist_project_members_column[member_id] = ".pnUserGetVar('uid')."";
    }
    $result = $dbconn->Execute($sql2);
    if($result->EOF) {
	return false;
    }
    $resarray = array();
    if ($all) {
        $resarray[] = array('id' => "all",'name' => xarML('all'));
    }
    while(list($project_id, $project_name) = $result->fields) {
	$result->MoveNext();
	$project_name = stripslashes($project_name);
	$resarray[] = array('id' => $project_id,'name' => $project_name);
    }
    $result->Close();

    $output = new pnHTML();
    $output->FormSelectMultiple($myname, $resarray, 0, 1, $selected_project);

    return $output->GetOutput();;
}

/**
 * creates a HTML-Dropdownbox with the availible groups
 *
 * @param $myname            String    Name of the form-variable
 * @param $selected_project    int        the project currently selected
 * @param $emty_choice        Boolean    Should an emty-entry be created? [1,0,true,false]
 * @param $multiple            Boolean    Allow multiple selects? [1,0,true,false]
 * @return String HTML
 */
function makeGroupDropdown($myname,$selected_group, $emty_choice, $multiple)
{
    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $todolist_groups_column = &$pntable['todolist_groups_column'];
    $result = $dbconn->Execute("SELECT * FROM $pntable[todolist_groups] ORDER BY $todolist_groups_column[group_name]");

    if($result->EOF) {
	return false;
    }
    $resarray = array();
    while(list($group_id, $group_name) = $result->fields) {
	$result->MoveNext();
	$group_name = stripslashes($group_name);
	$resarray[] = array('id' => $group_id,'name' => $group_name);
    }
    $result->Close();

    $output = new pnHTML();
    $output->FormSelectMultiple($myname, $resarray, 0, 1, $selected_group);

    return $output->GetOutput();;
}

/**
 * creates a HTML-Dropdownbox with the task-stati
 *
 * @param    $myname                string    Name of the form-variable
 * @param    $selected_status    int        the status currently selected
 * @param    $all                Boolean    should the all-entry be there?
 * @param    $java                string    which JavaScript-function schould be called?
 */
function makeStatusDropdown($myname, $selected_status, $all=false, $java=false)
{
    //    if ($java) { $str .= ' onchange="'.$java.'"'; }

    $stati   = array ();
    if ($all) {
        $stati[] = array('id' => "all",'name' => xarML('all'));
    }
    $stati[] = array('id'=>0,  'name' => xarML('open'));
    $stati[] = array('id'=>1,  'name' => xarML('in progress'));
    $stati[] = array('id'=>9,  'name' => xarML('obsolete'));
    $stati[] = array('id'=>10, 'name' => xarML('done'));

    $output = new pnHTML();
    $output->FormSelectMultiple($myname, $stati, 0, 1, $selected_status);

    return $output->GetOutput();;

}

/**
 * converts date to US-dateformat
 *
 * converts date in the format specified DATEFORMAT to US-Dateformat (so that MySQL understands it):
 * 01.12.1999  --->  1999-12-01
 * 01-12-1999  --->  1999-12-01
 * 01121999    --->  1999-12-01
 * 011299        --->  99-12-01
 * mmddyy
 * etc.
 *
 * @param $datestr String the date in the local format
 * @return String date in US Format
 */
function convDateToUS($datestr)
{
    $xTemp = explode(" ", trim($datestr));
    $xTime = "$xTemp[1]"; // Time ist not changed...

    if ( ereg( "([0-9]{1,2})([.-/]{0,1})([0-9]{1,2})([.-/]{0,1})([0-9]{2,4})", $xTemp[0], $regs ) ) {
        if (pnModGetVar('todolist', 'DATEFORMAT') == "2") {
            $date="$regs[5]-$regs[3]-$regs[1]";
        } elseif (pnModGetVar('todolist', 'DATEFORMAT') == "3") {
            $date="$regs[5]-$regs[1]-$regs[3]";
        }
    }
    return $date;
}

/**
 * converts US-dateformat to EU format
 *
 * convert 1999-12-01 ---> 01.12.1999
 *
 * @param $datestring String date in US Format
 * @return String date in EU Format
 */
function convDateToEU($datestr)
{
    $xTemp = explode(" ", trim($datestr)); // dont convert the time...
    if ( eregi( "([0-9yja]{4})([.-]{0,1})([0-9m]{1,2})([.-]{0,1})([0-9td]{1,2})",  $xTemp[0], $regs )) {
        $date="$regs[5].$regs[3].$regs[1]";
    }
    if ($xTemp[1] == "") {
        return $date;
    } else {
        return $date . " " . $xTemp[1];
    }
}                                                                               

/**
 * convert 1999-12-01 ---> 12/01/1999
 *
 * @param $datestring String date in US Format
 * @return String date in mm/dd/yyyy Format
 */
function convDateToMMDDYY($datestr)
{
    $xTemp = explode(" ", trim($datestr)); // dont convert the time...
    if ( eregi( "([0-9yja]{4})([.-]{0,1})([0-9m]{1,2})([.-]{0,1})([0-9td]{1,2})",  $xTemp[0], $regs )) {
        $date="$regs[3]/$regs[5]/$regs[1]";
    }
    if ($xTemp[1] == "") {
        return $date;
    } else {
        return $date . " " . $xTemp[1];
    }
}                                                                               

/**
 * Date-conversion depending on DATEFORMAT
 * 
 * @param $datestring String date in US Format
 * @return String date in format specified in DATEFORMAT
 */
function convDate($datestr)
{
    if (pnModGetVar('todolist', 'DATEFORMAT') == "2" ) {
        $xdate=convDateToEU("$datestr");
    } elseif (pnModGetVar('todolist', 'DATEFORMAT') == "3") {
        $xdate=convDateToMMDDYY("$datestr");
    } else {
        $xdate=$datestr;
    }

    return $xdate;
}

/**
 * How should the table be ordered?
 *
 * @param string order-indicator
 */
function orderBy($order_by)
{
    $str = " ";
    switch ($order_by) {
        case "status_asc":
            $str=$str . "ORDER BY pn_status ASC"; 
        break;

        case "status_desc":
            $str=$str . "ORDER BY pn_status DESC"; 
        break;

        case "prio_asc":
            $str .= "ORDER BY pn_todo_priority ASC"; 
        break;
        case "prio_desc":
            $str .= "ORDER BY pn_todo_priority DESC";
        break;
        case "due_asc":
            $str .= "ORDER BY pn_due_date ASC"; 
        break;
        case "due_desc":
            $str .= "ORDER BY pn_due_date DESC"; 
        break;
        /*
           case "responsible_asc":
           $str .= "ORDER BY pn_responsible_person ASC"; 
           break;
           case "responsible_desc":
           $str .= "ORDER BY pn_responsible_person DESC"; 
           break;
         */
        case "changed_on_asc":
            $str .= "ORDER BY pn_date_changed ASC"; 
        break;
        case "changed_on_desc":
            $str .= "ORDER BY pn_date_changed DESC"; 
        break;
        case "":
            $str .= "ORDER BY pn_todo_priority ASC, pn_due_date ASC"; 
        break;
    } // end of switch
    return $str;
}

?>