<?
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
// DATE CONVERTER, EMAIL GENERATOR, TASK VIEW, TASK DISPLAY, AND TREE/LEAF FUNCTIONS

/**
 * Date-conversion depending on DATEFORMAT
 *
 * @param $datestring String date in US Format
 * @return String date in format specified in DATEFORMAT
 */
function convDate($datestr,$dateformat = "m-d-y")
{
    $datestamp = strtotime($datestr);
    if(empty($dateformat)) $dateformat = xarModGetVar('xproject', 'DATEFORMAT');
    $returndate = @date($dateformat, $datestamp);
    if(empty($returndate)) $returndate = $datestr;
    return $returndate;
}

/**
 * generates a mail to all responsible persons and shows up what happened to a task.
 * @param id of the task
 * @param the performed action
 */
function generateMail($id,$action)
{
    if (!xarModGetVar('xproject', 'SEND_MAILS'))
        return;

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();
    $message_headers="From: ToDo-List <webmaster@$SERVER_NAME>\n";
    $message_headers.="MIME-Version: 1.0\n";
    $message_headers.="Content-type: multipart/mixed; boundary=\"simple boundary\"\n";
    $message_headers.="X-Mailer: PHP/" . phpversion() . "\n";

    $message_preamble="\nThis is a multi-part message in MIME format.\n";
    $message_boundary="--simple boundary\n";

    // Get all responsible users first (needed to get the details of the users (email!) later as MySQL
    // doesn't support subselects... :-(
    $xproject_responsible_persons_column = &$xartable['xproject_responsible_persons_column'];
    $result = $dbconn->Execute("SELECT $xproject_responsible_persons_column[user_id]
        FROM $xartable[xproject_responsible_persons]
        WHERE $xproject_responsible_persons_column[todo_id]=$id");

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


        $xproject_todos_column = &$xartable['xproject_todos_column'];
        $xproject_xproject_column = &$xartable['xproject_xproject_column'];
        if (!($result = $dbconn->Execute("SELECT $xartable[xproject_todos].*,
                    $xproject_xproject_column[project_name]
                    FROM $xartable[xproject_todos], $xartable[xproject_tasks]
                    WHERE $xproject_todos_column[project_id]=$xproject_xproject_column[id]
                    AND $xproject_todos_column[todo_id]=".$id))) {
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
        $xproject_notes_column = &$xartable['xproject_notes_column'];
        $xproject_users_column = &$xartable['xproject_users_column'];
        $result = $dbconn->Execute("SELECT $xproject_notes_column[text],
                $xproject_notes_column[date],$xproject_users_column[usernr]
                FROM $xartable[xproject_notes], $xartable[xproject_users]
                WHERE $xproject_notes_column[todo_id]=$id
                AND $xproject_notes_column[usernr]=$xproject_users_column[usernr]");

        if ($result->PO_RecordCount() > 0 ) {
            $message_text .= "\n\nNotes:\n";
            $message_html .='<br /><b>Notes:</b><table>';
            $message_html .='<tr><th>Text</th><th>user</th><th>date</th></tr>';

            for (;!$result->EOF;$result->MoveNext()) {
                $text        = stripslashes($result->fields[0]);
                $date        = $result->fields[1];
                $user_name  = stripslashes(xarUserGetVar('name',$result->fields[2]));
                if (empty($user_name)) $user_name  = stripslashes(xarUserGetVar('uname',$result->fields[2]));

                $message_text .= "\n\"$text\"";
                $message_text .= "\nby $user_name on ".strftime("%Y-%m-%d %H:%M:%S",$date);
                $message_html .= "<tr><td>$text</td><td>$user_name</td><td>$date</td><tr>\n";
            }
            $message_html .="</table>";
        }

        // get the receipients
        $xproject_users_column = &$xartable['xproject_users_column'];
        $query ="SELECT $xproject_users_column[usernr] FROM $xartable[xproject_users]";
        $query.=" WHERE $xproject_users_column[usernr] in (";

        while ($neu=array_pop($responsible_users)){
            $query .= $neu;
            if (sizeof($responsible_users) > 0)
                $query .= ',';
            else
                $query .= ') ';
        }
        $query .= " AND $xproject_users_column[email_notify] != 0";

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
//                xarMail(xarUserGetVar('email',$result->fields[0]), "$subject", $message, $message_headers);
        }
    } else {
        // no responsible users found so no mail hast to be generated.
        return;
    }
} // end of generateMail();

/**
 * Handles the user-administration and calls the user-details-dialog if needed
 *
 * @param $xquery      string    the SQL-Query that should fill the table
 * @param $xis_search  boolean   Is this a search?
 * @param $page        int       the current page-context
 *
 * @return HTML
 */
function printToDoTable($xquery, $xis_search, $page) {
    global $abfrage, $order_by;
    global $priority, $responsible_person, $search_project, $wildcards;

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();
    $stati = array (
            0    => _XPROJECT_STATUS_OPEN,
            1    => _XPROJECT_STATUS_IN_PROGRESS,
            9    => _XPROJECT_STATUS_OBSOLETE,
            10   => _XPROJECT_STATUS_DONE,
            );
    $very_important_date = date("Y-m-d H:i", mktime() + (86400 * xarModGetVar('xproject', 'VERY_IMPORTANT_DAYS')));
    $most_important_date = date("Y-m-d H:i", mktime() - (86400 * xarModGetVar('xproject', 'MOST_IMPORTANT_DAYS')));

    $str = "";
    switch ($page)
    {
        case SEARCH:
            // here's a little nuisance I haven't fixed yet - will only pass one user name
            // if you change the sort order. Can probably work a solution to this using
            // urlencode(serialize()) but it's a headache.
            if (is_array($responsible_person)){
                $responsible = $responsible_person[0];
            } else {
                $responsible = $responsible_person;
            }
            $arrayurl = array('route'=>SEARCH, 'abfrage'=>$abfrage, 'priority'=>$priority,
                              'responsible_person'=>$responsible, 'wildcards'=>wildcards);
            break;

        default:
            $arrayurl = array();
            break;
    }

    if (isset($search_project)) {
        $project = $search_project;
    } else {
        $project = xarSessionGetVar('xproject_selected_project');
    }

    $xproject_users_column = &$xartable['xproject_users_column'];
    $xproject_responsible_persons_column = &$xartable['xproject_responsible_persons_column'];
    $xproject_todos_column = &$xartable['xproject_todos_column'];
    $xproject_project_members_column = &$xartable['xproject_project_members_column'];

    $query = "SELECT distinct($xproject_responsible_persons_column[todo_id]),
        $xproject_users_column[usernr]
        FROM $xartable[xproject_users], $xartable[xproject_responsible_persons],
        $xartable[xproject_todos], $xartable[xproject_project_members]";

    if (xarSessionGetVar('xproject_selected_project') != "all") {
        $query .= " WHERE $xproject_todos_column[project_id]=".xarSessionGetVar('xproject_selected_project');
    } else {
        $query .= " WHERE $xproject_todos_column[project_id]=$xproject_project_members_column[project_id]
            AND $xproject_project_members_column[member_id] =" . xarUserGetVar('uid');
    }
    $query .= "
        AND $xproject_todos_column[todo_id] = $xproject_responsible_persons_column[todo_id]
        AND $xproject_users_column[usernr] = $xproject_responsible_persons_column[user_id]";

    $result = $dbconn->Execute($query);
    for (;!$result->EOF;$result->MoveNext()) {
        $responsible_users[] = array ($result->fields[0], $result->fields[1]);
    }

    $result = $dbconn->Execute("$xquery");

    if ($result->PO_RecordCount() == 0 ){
        return (_XPROJECT_NO_DATA_FOUND);
    }

    $i = 0;
    $str .= '<table border="0" cellspacing="1" cellpadding="0" rules="cols" width="100%"><tr>';

    if (xarModGetVar('xproject', 'SHOW_LINE_NUMBERS')){
        $str .= "<th>#</th>";
    }
    if (xarModGetVar('xproject', 'SHOW_PRIORITY_IN_TABLE')){
        if ($order_by=="prio_desc"){
            $arrayurl['order_by'] = 'prio_asc';
            $xREFRESH_URL = xarModURL('xproject', 'user', 'main', $arrayurl);
            $str .= '<th width="60" align="left">
            <a href="'.$xREFRESH_URL.'">'._XPROJECT_PRIORITY.'</a></th>';
        } else {
            $arrayurl['order_by'] = 'prio_desc';
            $xREFRESH_URL = xarModURL('xproject', 'user', 'main', $arrayurl);
            $str .= '<th width="60" align="left">
            <a href="'.$xREFRESH_URL.'">'._XPROJECT_PRIORITY.'</a></th>';
        }
    }

    if ($order_by=="status_desc"){
        $arrayurl['order_by'] = 'status_asc';
        $xREFRESH_URL = xarModURL('xproject', 'user', 'main', $arrayurl);
        $str .= '<th align="left">
        <a href="'.$xREFRESH_URL.'">'._XPROJECT_STATUS.'</a></th>';
    } else {
        $arrayurl['order_by'] = 'status_desc';
        $xREFRESH_URL = xarModURL('xproject', 'user', 'main', $arrayurl);
        $str .= '<th align="left">
        <a href="'.$xREFRESH_URL.'">'._XPROJECT_STATUS.'</a></th>';
    }

    if (xarModGetVar('xproject', 'SHOW_PERCENTAGE_IN_TABLE')){
        $str .= "<th>%</th>";
    }
    $str .= "<th align=\"left\">"._XPROJECT_TEXT."</th>";

    /*
    if ($order_by=="responsible_asc"){
        $arrayurl['order_by'] = 'responsible_desc';
        $xREFRESH_URL = xarModURL('xproject', 'user', 'main', $arrayurl);
        $str .= "<th align=\"left\"><a href=\"$xREFRESH_URL\">"._XPROJECT_RESPONSIBLE."</a></th>\n";
    } else {
        $arrayurl['order_by'] = 'responsible_asc';
        $xREFRESH_URL = xarModURL('xproject', 'user', 'main', $arrayurl);
        $str .= "<th align=\"left\"><a href=\"$xREFRESH_URL\">"._XPROJECT_RESPONSIBLE."</a></th>\n";
    }
    */
        $str .= "<th align=\"left\">"._XPROJECT_RESPONSIBLE."</th>\n";

    if ($order_by=="due_asc"){
        $arrayurl['order_by'] = 'due_desc';
        $xREFRESH_URL = xarModURL('xproject', 'user', 'main', $arrayurl);
        $str .= '<th width="60" align="left"><a href="'.$xREFRESH_URL.'">'._XPROJECT_DUE.'</a></th>';
    } else {
        $arrayurl['order_by'] = 'due_asc';
        $xREFRESH_URL = xarModURL('xproject', 'user', 'main', $arrayurl);
        $str .= '<th width="60" align="left"><a href="'.$xREFRESH_URL.'">'._XPROJECT_DUE.'</a></th>';
    }

    if ($order_by=="changed_on_asc") {
        $arrayurl['order_by'] = 'changed_on_desc';
        $xREFRESH_URL = xarModURL('xproject', 'user', 'main', $arrayurl);
        $str .= '<th width="60" align="left"><a href="'.$xREFRESH_URL.'">'._XPROJECT_CHANGED_ON.'</a></th>';
    } else {
        $arrayurl['order_by'] = 'changed_on_asc';
        $xREFRESH_URL = xarModURL('xproject', 'user', 'main', $arrayurl);
        $str .= '<th width="60" align="left"><a href="'.$xREFRESH_URL.'">'._XPROJECT_CHANGED_ON.'</a></th>';
    }
    $str .= '<th width="100" align="left">'._XPROJECT_DETAILS.'</th>';
    $str .= "</tr>\n\n";

    $done_start=true;

    for (;!$result->EOF;$result->MoveNext()) {
        $id   = $result->fields[0];
        $text = $result->fields[2];
        if ($xis_search) {
            $nr_notes=0;
        } else {
            $nr_notes = $result->fields[11];
        }
        $priority = $result->fields[3];
        $status = $result->fields[10];
        $percentage_completed = $result->fields[4];
        $due_date = $result->fields[6];
        $date_changed = $result->fields[8];

        // Abstand vor den erledigten Eintr?gen. --> bessere ?bersicht.
        if ($done_start==true && $status == 10){
            $str .= '<tr><td height="15"></td></tr>';
            $done_start=false;
        }

        if ($due_date < $very_important_date && $due_date != "0000-00-00" && $status != 10 &&
              xarModGetVar('xproject', 'VERY_IMPORTANT_DAYS') != 0){
            $ROW_COLOR = xarModGetVar('xproject', 'VERY_IMPORTANT_COLOR');
        } elseif ($priority == 1){
            $ROW_COLOR = xarModGetVar('xproject', 'HIGH_COLOR');
        } elseif ($priority == 2){
            $ROW_COLOR = xarModGetVar('xproject', 'MED_COLOR');
        } elseif ($priority == 3){
            $ROW_COLOR = xarModGetVar('xproject', 'LOW_COLOR');
        } elseif ($status > 5){
            $ROW_COLOR = xarModGetVar('xproject', 'DONE_COLOR');
        }

        $str .= "<tr bgcolor=\"$ROW_COLOR\">";
        if (xarModGetVar('xproject', 'SHOW_LINE_NUMBERS')) {
            $str .= '<td align="right">';
            if (xarModGetVar('xproject', 'SHOW_EXTRA_ASTERISK') == 1 && $nr_notes > 0 ){
                    $str .= "<b>*</b> ";
            }
            $str .= ($i+1) . ".</td>";
        }

        $priority = switchPriority($priority);

        if (xarModGetVar('xproject', 'SHOW_PRIORITY_IN_TABLE')){
            if (xarModGetVar('xproject', 'SHOW_EXTRA_ASTERISK') == 2 && $nr_notes > 0 ){
                $str .= "<td>$priority <b>*</b></td>";
            } else {
                $str .= "<td>$priority</td>";
            }
        }

        $str .= "<td>$stati[$status]</td>";

        if (xarModGetVar('xproject', 'SHOW_PERCENTAGE_IN_TABLE')) {
            $str .= "<td align=\"center\">";
            if (xarModGetVar('xproject', 'SHOW_EXTRA_ASTERISK') == 3 && $nr_notes > 0){
                $str .= "<b>*</b> ";
            }
            $str .= "$percentage_completed</td>";
        }
        $str .= '<td>';
        if (xarModGetVar('xproject', 'SHOW_EXTRA_ASTERISK') == 4 && $nr_notes > 0){
            $str .= '<b>*</b> ';
              $str .= '<a href="'.xarModURL('xproject', 'user', 'main', array('route' => DETAILS, 'id' => $id)).'">'.stripslashes($text).'</a>';
        } else {
              $str .= '<a href="'.xarModURL('xproject', 'user', 'main', array('route' => DETAILS, 'id' => $id)).'">'.stripslashes($text).'</a>';
        }
        $str .= '</td>';

        $str .= "<td>";
        reset ($responsible_users);
        $respstr = "";
        while (@list($key,$value) = @each($responsible_users)){
            if ($value[0] == $id) {
               $user_name  = stripslashes(xarUserGetVar('name',$value[1]));
               if (empty($user_name)) $user_name  = stripslashes(xarUserGetVar('uname',$value[1]));
               $respstr .= $user_name. ", ";
            }
        }
        $respstr = substr($respstr,0,-2);
        $str .= $respstr."</td>";

        if ($due_date < $most_important_date && $due_date != "0000-00-00" &&
            xarModGetVar('xproject', 'MOST_IMPORTANT_DAYS') != 0) {
            $str .= "<td nowrap=\"nowrap\"><font color=\"".xarModGetVar('xproject', 'MOST_IMPORTANT_COLOR')."\">" . convDate($due_date) . "</font></td>";
        } else {
            $str .= '<td nowrap="nowrap">' . convDate($due_date) . "</td>";
        }
        $str .= "<td>" . convDate(strftime("%Y-%m-%d %H:%M:%S",$date_changed)) . "</td>";

        // Anzahl der Notes anzeigen. Wenn mehr als 5 vorhanden sind, dann soll
        // die Zahl angezeigt werden, sonnst die entsprechende Anzahl Sternchen.
        if ($nr_notes > 0) {
              $str .= '<td>&nbsp;<a href="'.
                      xarModURL('xproject', 'user', 'main', array('route' => DETAILS, 'id' => $id)).
                      '">'._XPROJECT_DETAILS.'</a><b>';
              if ($nr_notes < 5) {
                  for ($zaehler=0;($zaehler < $nr_notes) && ($zaehler < 5) && ($nr_notes < 5) ; $zaehler++) {
                      $str .= "*";
                  }
              } else {
                  $str .= "&nbsp;&nbsp;&nbsp;$nr_notes";
              }
              $str .= "</b></td>";
        } else {
              $str .= '<td>&nbsp;<a href="'.
                      xarModURL('xproject', 'user', 'main', array('route' => DETAILS, 'id' => $id)).
                      '">'._XPROJECT_DETAILS.'</a></td>';
        }
        $str .= '</tr>';
        $i++;
    }

    $nr_datasets = ++$i;

    if ($page == FRONTPAGE || $page == ACTIONS) { // list completed entries also
        $xproject_todos_column = &$xartable['xproject_todos_column'];
        $xproject_responsible_persons_column = &$xartable['xproject_responsible_persons_column'];
        $xproject_project_members_column = &$xartable['xproject_project_members_column'];
        $xproject_notes_column = &$xartable['xproject_notes_column'];
        $query="SELECT $xartable[xproject_todos].*, count(distinct($xproject_notes_column[note_id])) AS nr_notes
            FROM $xartable[xproject_todos], $xartable[xproject_responsible_persons], $xartable[xproject_project_members]
            LEFT JOIN $xartable[xproject_notes] ON $xproject_todos_column[todo_id]=$xproject_notes_column[todo_id]";

        if (xarSessionGetVar('xproject_selected_project') != "all") {
            $query .= " WHERE $xproject_todos_column[project_id]=".xarSessionGetVar('xproject_selected_project');
        } else {
            $query .= " WHERE $xproject_todos_column[project_id]=$xproject_project_members_column[project_id]
                AND ". xarUserGetVar('uid'). " = $xproject_project_members_column[member_id]";
        }

        // list all tasks with status >5 as this are end-status.
        $query .= "    AND $xproject_todos_column[status]>5";

        if (xarSessionGetVar('xproject_my_tasks') == 1 ) {
            // show only tasks where I'm responsible for
            $query .= "
                AND $xproject_responsible_persons_column[user_id] = ". xarUserGetVar('uid')."
                AND $xproject_todos_column[todo_id] = $xproject_responsible_persons_column[todo_id]";
        }

        $query .= "  GROUP BY $xproject_todos_column[todo_id]
            ORDER BY $xproject_todos_column[date_changed] DESC
            LIMIT ". xarModGetVar('xproject', 'MAX_DONE');

        $result = $dbconn->Execute("$query");
        $anzahl = $result->PO_RecordCount();

        $i = 0;

        $done_start=0;

        for (;!$result->EOF;$result->MoveNext()) {
            $id                = $result->fields[0];
            $text            = $result->fields[2];
            // $responsible_person        = $db->f("responsible_person");
            if ($xis_search) {
                $nr_notes=0;
            } else {
                $nr_notes        = $result->fields[11];
            }
            $priority            = $result->fields[3];
            $status            = $result->fields[10];
            $percentage_completed    = $result->fields[4];
            $due_date            = $result->fields[6];
            $date_changed        = $result->fields[8];

            // Abstand vor den erledigten Eintr?gen. --> bessere ?bersicht.
            if ($done_start==0) {
                $str .= '<tr><td height="15"></td></tr>';
                $done_start=1;
            }

            $str .= "<tr bgcolor=\"".xarModGetVar('xproject', 'DONE_COLOR')."\">";

            $priority = switchPriority($priority);

            if (xarModGetVar('xproject', 'SHOW_LINE_NUMBERS')) {
                $str .= '<td align="right">' . ($i+$nr_datasets) . ".</td>";
            }

            if (xarModGetVar('xproject', 'SHOW_PRIORITY_IN_TABLE')) {
                if ($nr_notes > 0) {
                      $str .= "<td>$priority <b>*</b></td>";
                } else {
                      $str .= "<td>$priority</td>";
                }
            }

            $str .= "<td>$stati[$status]</td>";

            if (xarModGetVar('xproject', 'SHOW_PERCENTAGE_IN_TABLE')) {
                $str .= "<td align=\"center\">$percentage_completed</td>";
            }

            if (xarModGetVar('xproject', 'SHOW_EXTRA_ASTERISK') == 4 && $nr_notes > 0){
                $str .= '<td><b>*</b> <a href="'.
                        xarModURL('xproject', 'user', 'main', array('route' => DETAILS, 'id' => $id)).
                        '>'.stripslashes($text).'</a></td>';
            } else {
                $str .= '<td><a href="'.
                        xarModURL('xproject', 'user', 'main', array('route' => DETAILS, 'id' => $id)).
                        '">'.stripslashes($text).'</a></td>';
            }

            $str .= "<td>";
            reset ($responsible_users);
            $respstr = "";
            while (@list($key,$value) = @each($responsible_users)){
                if ($value[0] == $id) {
                    $user_name  = stripslashes(xarUserGetVar('name',$value[1]));
                    if (empty($user_name)) $user_name  = stripslashes(xarUserGetVar('uname',$value[1]));
                    $respstr .= $user_name. ", ";
                }
            }
            $respstr = substr($respstr,0,-2);
            $str .= $respstr."</td>";
            $str .= '<td nowrap="nowrap">' . convDate($due_date) . "</td>";
            $str .= "<td>" . convDate(strftime("%Y-%m-%d %H:%M:%S", $date_changed)) . "</td>";

            // Anzahl der Notes anzeigen. Wenn mehr als 5 vorhanden sind, dann soll
            // die Zahl angezeigt werden, sonnst die entsprechende Anzahl Sternchen.
            if ($nr_notes > 0) {
                $str .= "<td>&nbsp;<a href=\"".
                xarModURL('xproject', 'user', 'main', array('route' => DETAILS, 'id' => $id)).
                "\">"._XPROJECT_DETAILS."</a><b>";
                if ($nr_notes < 5) {
                    for ($zaehler=0;($zaehler < $nr_notes) && ($zaehler < 5) && ($nr_notes < 5) ; $zaehler++) {
                    $str .= "*";
                }
            } else {
                $str .= "&nbsp;&nbsp;&nbsp;$nr_notes";
            }
            $str .= "</b></td>";
        } else {
          // not todo-liste
          $str .= "<td>&nbsp;<a href=\"".
                  xarModURL('xproject', 'user', 'main', array('route' => DETAILS, 'id' => $id)).
                  "\">"._XPROJECT_DETAILS."</a></td>";
        }
        $str .= '</tr>
            ';
        $i++;
    }
    }
    $str .= "</table>";

    return $str;
}

/**
 * details page
 *
 * generates the detail-page for a task
 *
 * @param $id    int    The ID of the task that should be shown
 *
 * @return HTML
 */
function details_page($id){

// MOVE TO TASKS_USER_DISPLAY

    global $detail_project;

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();
    $str = "";

    if (isset ($detail_project)){

        $xproject_responsible_persons_column = &$xartable['xproject_responsible_persons_column'];
        $result = $dbconn->Execute("SELECT $xproject_responsible_persons_column[user_id]
            FROM $xartable[xproject_responsible_persons]
            WHERE $xproject_responsible_persons_column[todo_id]=$id");

        if ($result->PO_RecordCount() > 0 ) {
            for (;!$result->EOF;$result->MoveNext()) {
                $responsible_users[] = $result->fields[0];
            }

            $result->Close();

            $xproject_project_members_column = &$xartable['xproject_project_members_column'];
            $result = $dbconn->Execute("SELECT $xproject_project_members_column[member_id]
                FROM $xartable[xproject_project_members]
                WHERE $xproject_project_members_column[project_id]=$detail_project");

            for (;!$result->EOF;$result->MoveNext()){
                $project_members[] = $result->fields[0];
            }

            $result->Close();

            $xproject_responsible_persons_column = &$xartable['xproject_responsible_persons_column'];
            $dbconn->Execute("DELETE FROM $xartable[xproject_responsible_persons]
                 WHERE $xproject_responsible_persons_column[todo_id]=$id");

            $query = "INSERT INTO $xartable[todo_responsible_persons] VALUES ";

            while (list ($key, $val) = each ($responsible_users)) {
              if (in_array($val, $project_members)) {
                  $query2 .= "($id, $val)," ;
              }
            }

            // if there are new responsible persons that are also in the new project we don't need to update.
            // TODO: But of course there are _no_ responsible users for that task now!
            if (strlen($query2) > 0) {
                $query .= $query2;
                $query = substr($query, 0 , -1);
                $dbconn->Execute($query);
            }
        }
        $xproject_todos_column = &$xartable['xproject_todos_column'];
        $dbconn->Execute("UPDATE $xartable[xproject_todos]
            SET $xproject_todos_column[project_id]=$detail_project
            WHERE $xproject_todos_column[todo_id]=$id");
    }

    $xproject_todos_column = &$xartable['xproject_todos_column'];
    $xproject_xproject_column = &$xartable['xproject_xproject_column'];

    if (!($result = $dbconn->Execute("SELECT $xartable[xproject_todos].*,$xproject_xproject_column[project_name]
        FROM $xartable[xproject_todos], $xartable[xproject_tasks]
                WHERE $xproject_todos_column[todo_id]=$id
                AND $xproject_todos_column[project_id]=$xproject_xproject_column[id]")))
        return false;

    $id                   = $result->fields[0];
    $project              = $result->fields[1];
    $project_name         = $result->fields[11];
    $text                 = $result->fields[2];
    $priority             = $result->fields[3];
    $percentage_completed = $result->fields[4];
    $due_date             = $result->fields[6];
    $date_created         = $result->fields[7];
    $date_changed         = $result->fields[8];
    $created_by           = $result->fields[5];
    $changed_by           = $result->fields[9];
    $status               = $result->fields[10];

    $result->Close();

    $xproject_responsible_persons_column = &$xartable['xproject_responsible_persons_column'];
    $result = $dbconn->Execute("SELECT $xproject_responsible_persons_column[user_id]
        FROM $xartable[xproject_responsible_persons]
        WHERE $xproject_responsible_persons_column[todo_id]=$id");

    for (;!$result->EOF;$result->MoveNext()){
        $responsible_users[] = $result->fields[0];
    }

    $result->Close();

    $xproject_users_column = &$xartable['xproject_users_column'];
    if (!($result = $dbconn->Execute("SELECT * FROM $xartable[xproject_users]
        WHERE $xproject_users_column[usernr] IN ($created_by, $changed_by)")))
        return false;

    for (;!$result->EOF;$result->MoveNext()){
        $usernames[$result->fields[0]] = xarUserGetVar($result->fields[0]);
    }

    $str .=  '<form method="post" name="detailform" action="'.xarModURL('xproject', 'user', 'main', array()).'">
    <input type="hidden" name="module" value="xproject" />
    <input type="hidden" name="route" value="'.ACTIONS.'" />
    <table border="0">';

    $str .= '<tr>
    <td><input type="hidden" name="id" value="'.$id. '" readonly="readonly" />'._XPROJECT_PROJECT.'</td>
    <td>' . makeProjectDropdown("project",$project,false,"updatedetails()") . '</td></tr>';
    // $str .= '<tr><td>'._XPROJECT_PROJECT.'</td><td>'.$project_name.'</td></tr>';
    $str .= '<tr><td>'._XPROJECT_PRIORITY.'</td>';

    $priority = switchPriority($priority);

    $str .= "<td><select name=\"priority\" size=\"1\">";
    if ($priority == _XPROJECT_PRIORITY_HIGH) {
      $str .= "<option selected=\"selected\">"._XPROJECT_PRIORITY_HIGH."</option>";
    } else {
      $str .= "<option>"._XPROJECT_PRIORITY_HIGH."</option>";
    }

    if ($priority == _XPROJECT_PRIORITY_MEDIUM){
      $str .= "<option selected>"._XPROJECT_PRIORITY_MEDIUM."</option>";
    } else {
      $str .= "<option>"._XPROJECT_PRIORITY_MEDIUM."</option>";
    }

    if ($priority == _XPROJECT_PRIORITY_LOW){
      $str .= "<option selected=\"selected\">"._XPROJECT_PRIORITY_LOW."</option>";
    } else {
      $str .= "<option>"._XPROJECT_PRIORITY_LOW."</option>";
    }

    /*
    if ($priority == _XPROJECT_PRIORITY_DONE){
      $str .= "<option selected=\"selected\">"._XPROJECT_PRIORITY_DONE."</option>";
    } else {
      $str .= "<option>"._XPROJECT_PRIORITY_DONE."</option>";
    }
    */

    $str .= '</select></td></tr>';
    $str .= '
    <tr><td>'._XPROJECT_STATUS.'</td><td>'.makeStatusDropdown("status",$status,false,'updatepercentage()').'</td></tr>';
    $str .='<tr><td>'._XPROJECT_PERCENTAGE.'</td><td>
    <select name="percentage_completed" size="1" onchange="updatestatus()">';
    for ($i = 0 ; $i <= 100 ; $i += 20)
    {
    if ($percentage_completed == $i)
      $str .= '<option value="'.$i.'" selected="selected">'.$i.'</option>';
    else
      $str .= '<option value="'.$i.'">'.$i.'</option>';
    }
    $str .= '</select></td></tr><tr>';
    $str .= "<td>"._XPROJECT_TEXT."</td>";
    $str .= '<td><textarea cols="50" rows="5" name="text">'.stripslashes($text).'</textarea></td>';
    $str .= "</tr>";
    $str .= "<tr>";
    $str .= "<td>"._XPROJECT_RESPONSIBLE."</td><td>";
    $str .= makeUserDropdown("responsible_persons", $responsible_users, $project ,0,true);
    $str .= "</td></tr><tr><td>"._XPROJECT_DUE."</td>";
    $str .= "<td><input type=\"text\" name=\"due_date\" value=\"" . convDate($due_date) . "\" /><br/><pre>".convDate(_XPROJECT_DATEFORMAT).'</pre></td>';

    $str .= "</tr><tr><td>"._XPROJECT_CREATED_ON."</td>";
    $str .= '<td><input type="hidden" name="datum_erstellt" value="'.strftime("%Y-%m-%d",$date_created).'" />';
    $str .= convDate(strftime("%Y-%m-%d",$date_created)) . "</td></tr><tr>";
    $str .= "<td>"._XPROJECT_CREATED_BY."</td>";
    $str .= "<td>".$usernames["$created_by"]."</td>";
    $str .= "</tr>";
    $str .= "<tr>";
    $str .= "<td>"._XPROJECT_CHANGED_BY."</td>";
    $str .= "<td>".$usernames["$changed_by"]."</td>";
    $str .= "</tr>";
    $str .= "<tr>";
    $str .= "<td>"._XPROJECT_CHANGED_ON."</td>";
    $str .= "<td>";
    $str .= convDate(strftime("%Y-%m-%d %H:%M:%S", $date_changed)) . "</td></tr>";


    $str .= "</table>";
    $str .= '<select name="action" size="1">';
    $str .= '<option value="todo_change">'._XPROJECT_CHANGE."</option>";
    $str .= '<option value="todo_delete">'._XPROJECT_DELETE."</option>";
    $str .= "</select>";
    $str .= "&nbsp;&nbsp;<input type=\"submit\" value=\""._XPROJECT_SUBMIT."\" />";


    $str .= "<br /><br />";

    $xproject_notes_column = &$xartable['xproject_notes_column'];
    $xproject_users_column = &$xartable['xproject_users_column'];

    $result = $dbconn->Execute("SELECT $xproject_notes_column[note_id],$xproject_notes_column[text],
              $xproject_notes_column[date],$xproject_users_column[usernr]
              FROM $xartable[xproject_notes], $xartable[xproject_users]
              WHERE $xproject_notes_column[todo_id]=$id
              AND $xproject_notes_column[usernr]=$xproject_users_column[usernr]");
    $anzahl = $result->PO_RecordCount();

    $i = 0;

    if ($anzahl > 0){
        $str .= '<table border="1"><tr>';
        $str .= "<th align=\"left\">"._XPROJECT_NOTE."</th>";
        $str .= "<th align=\"left\">"._XPROJECT_USER."</th>";
        $str .= "<th align=\"left\">"._XPROJECT_DATE."</th>";
        $str .= "</tr>";
    }

    $todo_id=$id;

    for (;!$result->EOF;$result->MoveNext()) {
        $note_id    = $result->fields[0];
        $note_text= stripslashes($result->fields[1]);
        $datum    = $result->fields[2];
        $user_name    = xarUserGetVar('uname',$result->fields[3]);
        if (empty($user_name)) $user_name  = stripslashes(xarUserGetVar('uname',$usernr));

        $str .= "<tr><td>$note_text</td><td align=\"center\">$user_name</td><td>".strftime("%Y-%m-%d %H:%M:%S",$datum)."</td></tr>";
    }

    if ($anzahl > 0){
        $str .= "</table>";
    }
    $str .= "<hr noshade=\"noshade\"/>";

    $str .= "<table><tr>";
    $str .= "<th align=\"left\">"._XPROJECT_NOTE."</th>";
    $str .= "<th>&nbsp;</th>";
    $str .= "</tr><tr><td>";

    $str .= '<textarea cols="50" rows="4" name="note_text"></textarea></td>';
    $str .= "<td>";
    $str .= "&nbsp;&nbsp;<input type=\"submit\" value=\""._XPROJECT_SUBMIT."\" /></td>";
    $str .= "</tr></table>";
    $str .= "</form>";
    return $str;
}


// ************************************************************************
// Get all tasks - organized in a tree
// ************************************************************************


function xproject_userapi_gettree($args) {

    $tree     = array();
    $alltasks = xproject_userapi_getall($args);

    if ( $alltasks == false ) {
        return false;
    }

    $tree['subtasks'] = array();
    // Pass-1 : Find all taks that have no parent in this list
    foreach ($alltasks as $task) {
        $hasparent = false;
        foreach($alltasks as $t) {
            if ( $task['parentid'] == $t['taskid'] ) {
                $hasparent = true;
                break;
        }
    }
        if (!$hasparent) {
            $tree['subtasks'][$task['taskid']] = add_leaf($task,$alltasks);
        }
    }
    return $tree;
}

function add_leaf($t,$alltasks) {
    $t['subtasks'] = array();
    // Pass-2 (recursive) add tasks that have this one as parent
    foreach ($alltasks as $task) {
        if ( $task['parentid'] == $t['taskid'] ) {
            $t['subtasks'][$task['taskid']] = add_leaf($task,$alltasks);
    }
    }
    return $t;
}
?>