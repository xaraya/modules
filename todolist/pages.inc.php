<?php // $Id$
// pages.inc - the main bulk of the output from the todolist script

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

    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();
    $stati = array (
            0    => _TODOLIST_STATUS_OPEN,
            1    => _TODOLIST_STATUS_IN_PROGRESS,
            9    => _TODOLIST_STATUS_OBSOLETE,
            10   => _TODOLIST_STATUS_DONE,
            );
    $very_important_date = date("Y-m-d H:i", mktime() + (86400 * pnModGetVar('todolist', 'VERY_IMPORTANT_DAYS')));
    $most_important_date = date("Y-m-d H:i", mktime() - (86400 * pnModGetVar('todolist', 'MOST_IMPORTANT_DAYS')));

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
        $project = pnSessionGetVar('todolist_selected_project');
    }

    $todolist_users_column = &$pntable['todolist_users_column'];
    $todolist_responsible_persons_column = &$pntable['todolist_responsible_persons_column'];
    $todolist_todos_column = &$pntable['todolist_todos_column'];
    $todolist_project_members_column = &$pntable['todolist_project_members_column'];

    $query = "SELECT distinct($todolist_responsible_persons_column[todo_id]),
        $todolist_users_column[usernr]
        FROM $pntable[todolist_users], $pntable[todolist_responsible_persons],
        $pntable[todolist_todos], $pntable[todolist_project_members]";

    if (pnSessionGetVar('todolist_selected_project') != "all") {
        $query .= " WHERE $todolist_todos_column[project_id]=".pnSessionGetVar('todolist_selected_project');
    } else {
        $query .= " WHERE $todolist_todos_column[project_id]=$todolist_project_members_column[project_id]
            AND $todolist_project_members_column[member_id] =" . pnUserGetVar('uid');
    }
    $query .= " 
        AND $todolist_todos_column[todo_id] = $todolist_responsible_persons_column[todo_id]
        AND $todolist_users_column[usernr] = $todolist_responsible_persons_column[user_id]";

    $result = $dbconn->Execute($query);
    for (;!$result->EOF;$result->MoveNext()) {
        $responsible_users[] = array ($result->fields[0], $result->fields[1]);
    }

    $result = $dbconn->Execute("$xquery");
    
    if ($result->PO_RecordCount() == 0 ){
        return (_TODOLIST_NO_DATA_FOUND);
    }
    
    $i = 0;
    $str .= '<table border="0" cellspacing="1" cellpadding="0" rules="cols" width="100%"><tr>';
    
    if (pnModGetVar('todolist', 'SHOW_LINE_NUMBERS')){
        $str .= "<th>#</th>";
    }
    if (pnModGetVar('todolist', 'SHOW_PRIORITY_IN_TABLE')){
        if ($order_by=="prio_desc"){
            $arrayurl['order_by'] = 'prio_asc';
            $xREFRESH_URL = pnModURL('todolist', 'user', 'main', $arrayurl);
            $str .= '<th width="60" align="left">
            <a href="'.$xREFRESH_URL.'">'._TODOLIST_PRIORITY.'</a></th>';
        } else { 
            $arrayurl['order_by'] = 'prio_desc';
            $xREFRESH_URL = pnModURL('todolist', 'user', 'main', $arrayurl);
            $str .= '<th width="60" align="left">
            <a href="'.$xREFRESH_URL.'">'._TODOLIST_PRIORITY.'</a></th>';
        }
    }

    if ($order_by=="status_desc"){
        $arrayurl['order_by'] = 'status_asc';
        $xREFRESH_URL = pnModURL('todolist', 'user', 'main', $arrayurl);
        $str .= '<th align="left">
        <a href="'.$xREFRESH_URL.'">'._TODOLIST_STATUS.'</a></th>';
    } else { 
        $arrayurl['order_by'] = 'status_desc';
        $xREFRESH_URL = pnModURL('todolist', 'user', 'main', $arrayurl);
        $str .= '<th align="left">
        <a href="'.$xREFRESH_URL.'">'._TODOLIST_STATUS.'</a></th>';
    }

    if (pnModGetVar('todolist', 'SHOW_PERCENTAGE_IN_TABLE')){
        $str .= "<th>%</th>";
    }
    $str .= "<th align=\"left\">"._TODOLIST_TEXT."</th>";
    
    /*
    if ($order_by=="responsible_asc"){
        $arrayurl['order_by'] = 'responsible_desc';
        $xREFRESH_URL = pnModURL('todolist', 'user', 'main', $arrayurl);
        $str .= "<th align=\"left\"><a href=\"$xREFRESH_URL\">"._TODOLIST_RESPONSIBLE."</a></th>\n";
    } else {
        $arrayurl['order_by'] = 'responsible_asc';
        $xREFRESH_URL = pnModURL('todolist', 'user', 'main', $arrayurl);
        $str .= "<th align=\"left\"><a href=\"$xREFRESH_URL\">"._TODOLIST_RESPONSIBLE."</a></th>\n";
    }
    */
        $str .= "<th align=\"left\">"._TODOLIST_RESPONSIBLE."</th>\n";
    
    if ($order_by=="due_asc"){
        $arrayurl['order_by'] = 'due_desc';
        $xREFRESH_URL = pnModURL('todolist', 'user', 'main', $arrayurl);
        $str .= '<th width="60" align="left"><a href="'.$xREFRESH_URL.'">'._TODOLIST_DUE.'</a></th>';
    } else { 
        $arrayurl['order_by'] = 'due_asc';
        $xREFRESH_URL = pnModURL('todolist', 'user', 'main', $arrayurl);
        $str .= '<th width="60" align="left"><a href="'.$xREFRESH_URL.'">'._TODOLIST_DUE.'</a></th>';
    }
    
    if ($order_by=="changed_on_asc") {
        $arrayurl['order_by'] = 'changed_on_desc';
        $xREFRESH_URL = pnModURL('todolist', 'user', 'main', $arrayurl);
        $str .= '<th width="60" align="left"><a href="'.$xREFRESH_URL.'">'._TODOLIST_CHANGED_ON.'</a></th>';
    } else { 
        $arrayurl['order_by'] = 'changed_on_asc';
        $xREFRESH_URL = pnModURL('todolist', 'user', 'main', $arrayurl);
        $str .= '<th width="60" align="left"><a href="'.$xREFRESH_URL.'">'._TODOLIST_CHANGED_ON.'</a></th>';
    }
    $str .= '<th width="100" align="left">'._TODOLIST_DETAILS.'</th>';
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
    
        // Abstand vor den erledigten Einträgen. --> bessere Übersicht. 
        if ($done_start==true && $status == 10){
            $str .= '<tr><td height="15"></td></tr>';
            $done_start=false;
        }
    
        if ($due_date < $very_important_date && $due_date != "0000-00-00" && $status != 10 &&
              pnModGetVar('todolist', 'VERY_IMPORTANT_DAYS') != 0){
            $ROW_COLOR = pnModGetVar('todolist', 'VERY_IMPORTANT_COLOR');
        } elseif ($priority == 1){
            $ROW_COLOR = pnModGetVar('todolist', 'HIGH_COLOR');
        } elseif ($priority == 2){
            $ROW_COLOR = pnModGetVar('todolist', 'MED_COLOR');
        } elseif ($priority == 3){
            $ROW_COLOR = pnModGetVar('todolist', 'LOW_COLOR');
        } elseif ($status > 5){
            $ROW_COLOR = pnModGetVar('todolist', 'DONE_COLOR');
        }

        $str .= "<tr bgcolor=\"$ROW_COLOR\">";
        if (pnModGetVar('todolist', 'SHOW_LINE_NUMBERS')) {
            $str .= '<td align="right">';
            if (pnModGetVar('todolist', 'SHOW_EXTRA_ASTERISK') == 1 && $nr_notes > 0 ){
                    $str .= "<b>*</b> ";
            }
            $str .= ($i+1) . ".</td>";
        }

        $priority = switchPriority($priority);
    
        if (pnModGetVar('todolist', 'SHOW_PRIORITY_IN_TABLE')){
            if (pnModGetVar('todolist', 'SHOW_EXTRA_ASTERISK') == 2 && $nr_notes > 0 ){
                $str .= "<td>$priority <b>*</b></td>";
            } else {
                $str .= "<td>$priority</td>";
            }
        }

        $str .= "<td>$stati[$status]</td>";
    
        if (pnModGetVar('todolist', 'SHOW_PERCENTAGE_IN_TABLE')) {
            $str .= "<td align=\"center\">";
            if (pnModGetVar('todolist', 'SHOW_EXTRA_ASTERISK') == 3 && $nr_notes > 0){
                $str .= "<b>*</b> ";
            }
            $str .= "$percentage_completed</td>";
        }
        $str .= '<td>';
        if (pnModGetVar('todolist', 'SHOW_EXTRA_ASTERISK') == 4 && $nr_notes > 0){
            $str .= '<b>*</b> ';
              $str .= '<a href="'.pnModURL('todolist', 'user', 'main', array('route' => DETAILS, 'id' => $id)).'">'.stripslashes($text).'</a>';
        } else {
              $str .= '<a href="'.pnModURL('todolist', 'user', 'main', array('route' => DETAILS, 'id' => $id)).'">'.stripslashes($text).'</a>';
        }
        $str .= '</td>';

        $str .= "<td>";
        reset ($responsible_users);
        $respstr = "";
        while (@list($key,$value) = @each($responsible_users)){
            if ($value[0] == $id) {
               $user_name  = stripslashes(pnUserGetVar('name',$value[1]));
               if (empty($user_name)) $user_name  = stripslashes(pnUserGetVar('uname',$value[1]));
               $respstr .= $user_name. ", ";
            }
        }
        $respstr = substr($respstr,0,-2);
        $str .= $respstr."</td>";
        
        if ($due_date < $most_important_date && $due_date != "0000-00-00" &&
            pnModGetVar('todolist', 'MOST_IMPORTANT_DAYS') != 0) {
            $str .= "<td nowrap=\"nowrap\"><font color=\"".pnModGetVar('todolist', 'MOST_IMPORTANT_COLOR')."\">" . convDate($due_date) . "</font></td>";
        } else {
            $str .= '<td nowrap="nowrap">' . convDate($due_date) . "</td>";
        }
        $str .= "<td>" . convDate(strftime("%Y-%m-%d %H:%M:%S",$date_changed)) . "</td>";
    
        // Anzahl der Notes anzeigen. Wenn mehr als 5 vorhanden sind, dann soll
        // die Zahl angezeigt werden, sonnst die entsprechende Anzahl Sternchen.
        if ($nr_notes > 0) {
              $str .= '<td>&nbsp;<a href="'.
                      pnModURL('todolist', 'user', 'main', array('route' => DETAILS, 'id' => $id)).
                      '">'._TODOLIST_DETAILS.'</a><b>';
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
                      pnModURL('todolist', 'user', 'main', array('route' => DETAILS, 'id' => $id)).
                      '">'._TODOLIST_DETAILS.'</a></td>'; 
        }
        $str .= '</tr>';
        $i++;
    }

    $nr_datasets = ++$i;

    if ($page == FRONTPAGE || $page == ACTIONS) { // list completed entries also
        $todolist_todos_column = &$pntable['todolist_todos_column'];
        $todolist_responsible_persons_column = &$pntable['todolist_responsible_persons_column'];
        $todolist_project_members_column = &$pntable['todolist_project_members_column'];
        $todolist_notes_column = &$pntable['todolist_notes_column'];
        $query="SELECT $pntable[todolist_todos].*, count(distinct($todolist_notes_column[note_id])) AS nr_notes
            FROM $pntable[todolist_todos], $pntable[todolist_responsible_persons], $pntable[todolist_project_members]
            LEFT JOIN $pntable[todolist_notes] ON $todolist_todos_column[todo_id]=$todolist_notes_column[todo_id]";

        if (pnSessionGetVar('todolist_selected_project') != "all") {
            $query .= " WHERE $todolist_todos_column[project_id]=".pnSessionGetVar('todolist_selected_project');
        } else {
            $query .= " WHERE $todolist_todos_column[project_id]=$todolist_project_members_column[project_id]
                AND ". pnUserGetVar('uid'). " = $todolist_project_members_column[member_id]";
        }

        // list all tasks with status >5 as this are end-status.
        $query .= "    AND $todolist_todos_column[status]>5";

        if (pnSessionGetVar('todolist_my_tasks') == 1 ) {
            // show only tasks where I'm responsible for
            $query .= " 
                AND $todolist_responsible_persons_column[user_id] = ". pnUserGetVar('uid')."
                AND $todolist_todos_column[todo_id] = $todolist_responsible_persons_column[todo_id]";
        }

        $query .= "  GROUP BY $todolist_todos_column[todo_id]
            ORDER BY $todolist_todos_column[date_changed] DESC
            LIMIT ". pnModGetVar('todolist', 'MAX_DONE'); 

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
    
            // Abstand vor den erledigten Einträgen. --> bessere Übersicht. 
            if ($done_start==0) {
                $str .= '<tr><td height="15"></td></tr>';
                $done_start=1;
            }
    
            $str .= "<tr bgcolor=\"".pnModGetVar('todolist', 'DONE_COLOR')."\">";
    
            $priority = switchPriority($priority);
    
            if (pnModGetVar('todolist', 'SHOW_LINE_NUMBERS')) {
                $str .= '<td align="right">' . ($i+$nr_datasets) . ".</td>";
            }
    
            if (pnModGetVar('todolist', 'SHOW_PRIORITY_IN_TABLE')) {
                if ($nr_notes > 0) {
                      $str .= "<td>$priority <b>*</b></td>";
                } else {
                      $str .= "<td>$priority</td>";
                }
            }

            $str .= "<td>$stati[$status]</td>";
    
            if (pnModGetVar('todolist', 'SHOW_PERCENTAGE_IN_TABLE')) {
                $str .= "<td align=\"center\">$percentage_completed</td>";
            }

            if (pnModGetVar('todolist', 'SHOW_EXTRA_ASTERISK') == 4 && $nr_notes > 0){
                $str .= '<td><b>*</b> <a href="'.
                        pnModURL('todolist', 'user', 'main', array('route' => DETAILS, 'id' => $id)).
                        '>'.stripslashes($text).'</a></td>';
            } else {
                $str .= '<td><a href="'.
                        pnModURL('todolist', 'user', 'main', array('route' => DETAILS, 'id' => $id)).
                        '">'.stripslashes($text).'</a></td>';
            }

            $str .= "<td>";
            reset ($responsible_users);
            $respstr = "";
            while (@list($key,$value) = @each($responsible_users)){
                if ($value[0] == $id) {
                    $user_name  = stripslashes(pnUserGetVar('name',$value[1]));
                    if (empty($user_name)) $user_name  = stripslashes(pnUserGetVar('uname',$value[1]));
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
                pnModURL('todolist', 'user', 'main', array('route' => DETAILS, 'id' => $id)).
                "\">"._TODOLIST_DETAILS."</a><b>"; 
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
                  pnModURL('todolist', 'user', 'main', array('route' => DETAILS, 'id' => $id)).
                  "\">"._TODOLIST_DETAILS."</a></td>"; 
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
 * form to add a new entry
 *
 * @return HTML
 */
function add_box(){
    global $add_project;

    if (!isset($add_project)) {
        if (pnSessionGetVar('todolist_selected_project') == "all") {
            if (strpos(pnSessionGetVar('todolist_my_projects'),',') === false) {
                // The user is member of <= 1 Project. TODO: no projects?!?!
                $add_project = substr(pnSessionGetVar('todolist_my_projects'), 1, -1);
            } else {
                $add_project = substr(pnSessionGetVar('todolist_my_projects'), 1, strpos(pnSessionGetVar('todolist_my_projects'),',')-1);
            }
        } else {
            $add_project = pnSessionGetVar('todolist_selected_project');
        }
    }

    $str = "";
    $str .= '
        <a name="todoAddForm"/><hr noshade="noshade"/>
        <form method="post" name="addform" action="'.pnModURL('todolist', 'user', 'main', array()).'">
        <input type="hidden" name="module" value="todolist" />
        <input type="hidden" name="route" value="'.ACTIONS.'"/>
        <input type="hidden" name="action" value="todo-add"/>
        <table width="100%">';
    $str .= "<tr><th align=\"left\">"._TODOLIST_TEXT."</th>";
    $str .= "<th align=\"left\">"._TODOLIST_PRIORITY."</th>";
    $str .= "<th align=\"left\">"._TODOLIST_PROJECT."</th>";
    $str .= "<th align=\"left\">"._TODOLIST_DUE."</th>";
    $str .= "<th align=\"left\">"._TODOLIST_RESPONSIBLE."</th></tr>";
    $str .='
        <tr align="left" valign="top"><td>
        <textarea cols="40" rows="5" name="text"></textarea>
        </td>';
    $str .='<td><select name="priority" size="1">';
    $str .= "<option>"._TODOLIST_PRIORITY_HIGH."</option>";
    $str .= "<option>"._TODOLIST_PRIORITY_MEDIUM."</option>";
    $str .= "<option>"._TODOLIST_PRIORITY_LOW."</option>";
    $str .='</select></td>';
    $str .='<td>'. makeProjectDropdown("project",$add_project, false, "updateaddbox()"). '</td>';
    $str .= '<td>
        <input type="text" name="due_date" size="10" value="'.convDate(date("Y-m-d")).'" maxlength="10"/>
        <pre>
        '.convDate(_TODOLIST_DATEFORMAT).'</pre>';
    $str .= "<a href=\"javascript:showCalendar()\"> "._TODOLIST_SHOW_CAL."</a>";
    $str .='</td><td>';
    $str .=  makeUserDropdown("responsible_person", array(pnUserGetVar('uid')), $add_project ,0,true); //empty array to keep it clear
    $str .='<p /><div align="right"><input type="submit" value="'._TODOLIST_SUBMIT.'" /></div>
        </td></tr></table></form>';
    return $str;
}
// end add_box

/**
 * a box for searching, believe it or not 
 *
 * @param $priority
 * @param $search_status
 * @param $search_project
 * @param $responsible_person
 * @param $date_min
 * @param $date_max
 *
 * @return HTML
 */
function search_box($priority,$search_status, $search_project,$responsible_person,$date_min,$date_max) {
    global $abfrage, $route, $wildcards;

    $str = '
        <a name="todoSearchForm"/>';

    if ($route!=SEARCH) {
        $str .= '<hr noshade="noshade"/>';
    }
    $str .= '<form method="get" action="'.pnModURL('todolist', 'user', 'main', array()).'"> 
        <input type="hidden" name="module" value="todolist" />
        <input type="hidden" name="route" value="'.SEARCH.'" />';
    if ($route!=SEARCH) {
        $str.='<input type="hidden" name="page" value="'.DETPAGE.'" />'; // TODO: dirty hack... not nice.
    } else {
        $str.='<input type="hidden" name="page" value="'.SEARCHPAGE.'" />';
    }
    $str .='<table width="100%">
        <tr>
        ';
    $str .= "<th align=\"left\">"._TODOLIST_THE_SEARCH.":</th>";
    $str .= "<th align=\"left\">"._TODOLIST_PRIORITY.":</th>";
    $str .= "<th align=\"left\">"._TODOLIST_STATUS.":</th>";
    $str .= "<th align=\"left\">"._TODOLIST_PROJECT.":</th>";
    $str .= "<th align=\"left\">"._TODOLIST_RESPONSIBLE.":</th>";
    $str .= "<th align=\"left\">"._TODOLIST_DATE_FROM.":</th>";
    $str .= "<th align=\"left\">"._TODOLIST_DATE_TO.":</th>";
    $str .='
        </tr><tr valign="top"><td>
        ';
    $str .= "<input type=\"text\" size=\"20\" maxlength=\"45\" name=\"abfrage\" value=\"$abfrage\"/>";
    $str .= '<br />
        <input type="checkbox" name="wildcards"';
    if ( ($wildcards==1) || ($route==FRONTPAGE) || ($route==ACTIONS)) {
        $str .= ' checked="checked"';
    }
    $str .= 'value="1"/> Wildcards<br />';
    $str .='
        </td>
        <td>
        <select name="priority" size="1">
        ';
    if ($priority == ""){
        $str .= '<option selected="selected" value="">'._TODOLIST_ALL.'</option>';
    }else{
        $str .= '<option value="">'._TODOLIST_ALL.'</option>';
    }
    if ($priority == "1"){
        $str .= '<option value="1" selected="selected">'._TODOLIST_PRIORITY_HIGH.'</option>';
    }else{
        $str .= '<option value="1">'._TODOLIST_PRIORITY_HIGH.'</option>';
    }
    if ($priority == "2"){
        $str .= '<option value="2" selected="selected">'._TODOLIST_PRIORITY_MEDIUM.'</option>';
    }else{
        $str .= '<option value="2">'._TODOLIST_PRIORITY_MEDIUM.'</option>';
    }
    if ($priority == "3"){
        $str .= '<option value="3" selected="selected">'._TODOLIST_PRIORITY_LOW.'</option>';
    }else{
        $str .= '<option value="3">'._TODOLIST_PRIORITY_LOW.'</option>';
    }
    /*
    if ($priority == "4"){
        $str .= '<option value="4" selected="selected">'._TODOLIST_PRIORITY_DONE.'</option>';
    }else{
        $str .= '<option value="4">'._TODOLIST_PRIORITY_DONE.'</option>';
    }
    */
    $str .= '</select></td>
        ';
    $str .= '<td>'.makeStatusDropdown("search_status",$search_status, true).'</td>';
    $str .= '<td>'.makeProjectDropdown("search_project",$search_project, true);
    $str .= '</td><td>';
    $str .= makeUserDropdown("responsible_person", $responsible_person, "all" , 0, true);

    if ($date_min=="") {
        $date_min="0000-00-00";
    } else {
        if (pnModGetVar('todolist', 'DATEFORMAT') != "1" ) {
            $date_min=convDateToUS($date_min);
        }
    }

    if ($date_max=="") {
        $date_max=date("Y-m-d");
    } else {
        if (pnModGetVar('todolist', 'DATEFORMAT') != "1" ) {
            $date_max=convDateToUS($date_max);
        }
    }

    $str .='</td><td>
        <input type="text" name="date_min" size="10" value="' . convDate($date_min) . '" maxlength="10"/><br/><pre>'.convDate(_TODOLIST_DATEFORMAT).'</pre>
        </td><td>
        <input type="text" name="date_max" size="10" value="' . convDate($date_max) . '" maxlength="10"/><pre>'.convDate(_TODOLIST_DATEFORMAT).'</pre>
        <p/>
        <div align="right">
        <input type="submit" value="'._TODOLIST_SEARCH.'"/>
        </div>
        </td></tr></table>
        </form>';
    return $str;
} 

// END FRONT_PAGE


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
    global $detail_project;

    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();
    $str = "";

    if (isset ($detail_project)){

        $todolist_responsible_persons_column = &$pntable['todolist_responsible_persons_column'];
        $result = $dbconn->Execute("SELECT $todolist_responsible_persons_column[user_id]
            FROM $pntable[todolist_responsible_persons]
            WHERE $todolist_responsible_persons_column[todo_id]=$id");

        if ($result->PO_RecordCount() > 0 ) {
            for (;!$result->EOF;$result->MoveNext()) {
                $responsible_users[] = $result->fields[0];
            }

            $result->Close();

            $todolist_project_members_column = &$pntable['todolist_project_members_column'];
            $result = $dbconn->Execute("SELECT $todolist_project_members_column[member_id]
                FROM $pntable[todolist_project_members]
                WHERE $todolist_project_members_column[project_id]=$detail_project");

            for (;!$result->EOF;$result->MoveNext()){
                $project_members[] = $result->fields[0];
            }

            $result->Close();

            $todolist_responsible_persons_column = &$pntable['todolist_responsible_persons_column'];
            $dbconn->Execute("DELETE FROM $pntable[todolist_responsible_persons]
                 WHERE $todolist_responsible_persons_column[todo_id]=$id");

            $query = "INSERT INTO $pntable[todo_responsible_persons] VALUES ";

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
        $todolist_todos_column = &$pntable['todolist_todos_column'];
        $dbconn->Execute("UPDATE $pntable[todolist_todos]
            SET $todolist_todos_column[project_id]=$detail_project
            WHERE $todolist_todos_column[todo_id]=$id");
    }

    $todolist_todos_column = &$pntable['todolist_todos_column'];
    $todolist_projects_column = &$pntable['todolist_projects_column'];
    
    if (!($result = $dbconn->Execute("SELECT $pntable[todolist_todos].*,$todolist_projects_column[project_name]
        FROM $pntable[todolist_todos], $pntable[todolist_projects]
                WHERE $todolist_todos_column[todo_id]=$id
                AND $todolist_todos_column[project_id]=$todolist_projects_column[id]")))
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

    $todolist_responsible_persons_column = &$pntable['todolist_responsible_persons_column'];
    $result = $dbconn->Execute("SELECT $todolist_responsible_persons_column[user_id]
        FROM $pntable[todolist_responsible_persons]
        WHERE $todolist_responsible_persons_column[todo_id]=$id");

    for (;!$result->EOF;$result->MoveNext()){
        $responsible_users[] = $result->fields[0];
    }

    $result->Close();

    $todolist_users_column = &$pntable['todolist_users_column'];
    if (!($result = $dbconn->Execute("SELECT * FROM $pntable[todolist_users]
        WHERE $todolist_users_column[usernr] IN ($created_by, $changed_by)")))
        return false;

    for (;!$result->EOF;$result->MoveNext()){
        $usernames[$result->fields[0]] = pnUserGetVar($result->fields[0]);
    }

    $str .=  '<form method="post" name="detailform" action="'.pnModURL('todolist', 'user', 'main', array()).'">
    <input type="hidden" name="module" value="todolist" />
    <input type="hidden" name="route" value="'.ACTIONS.'" />
    <table border="0">';

    $str .= '<tr>
    <td><input type="hidden" name="id" value="'.$id. '" readonly="readonly" />'._TODOLIST_PROJECT.'</td>
    <td>' . makeProjectDropdown("project",$project,false,"updatedetails()") . '</td></tr>';
    // $str .= '<tr><td>'._TODOLIST_PROJECT.'</td><td>'.$project_name.'</td></tr>';
    $str .= '<tr><td>'._TODOLIST_PRIORITY.'</td>';

    $priority = switchPriority($priority);

    $str .= "<td><select name=\"priority\" size=\"1\">";
    if ($priority == _TODOLIST_PRIORITY_HIGH) {
      $str .= "<option selected=\"selected\">"._TODOLIST_PRIORITY_HIGH."</option>";
    } else {
      $str .= "<option>"._TODOLIST_PRIORITY_HIGH."</option>";
    }

    if ($priority == _TODOLIST_PRIORITY_MEDIUM){
      $str .= "<option selected>"._TODOLIST_PRIORITY_MEDIUM."</option>";
    } else {
      $str .= "<option>"._TODOLIST_PRIORITY_MEDIUM."</option>";
    }

    if ($priority == _TODOLIST_PRIORITY_LOW){
      $str .= "<option selected=\"selected\">"._TODOLIST_PRIORITY_LOW."</option>";
    } else {
      $str .= "<option>"._TODOLIST_PRIORITY_LOW."</option>";
    }

    /*
    if ($priority == _TODOLIST_PRIORITY_DONE){
      $str .= "<option selected=\"selected\">"._TODOLIST_PRIORITY_DONE."</option>";
    } else {
      $str .= "<option>"._TODOLIST_PRIORITY_DONE."</option>";
    }
    */

    $str .= '</select></td></tr>';
    $str .= '
    <tr><td>'._TODOLIST_STATUS.'</td><td>'.makeStatusDropdown("status",$status,false,'updatepercentage()').'</td></tr>';
    $str .='<tr><td>'._TODOLIST_PERCENTAGE.'</td><td>
    <select name="percentage_completed" size="1" onchange="updatestatus()">';
    for ($i = 0 ; $i <= 100 ; $i += 20)
    {
    if ($percentage_completed == $i)
      $str .= '<option value="'.$i.'" selected="selected">'.$i.'</option>';
    else
      $str .= '<option value="'.$i.'">'.$i.'</option>';
    }
    $str .= '</select></td></tr><tr>';
    $str .= "<td>"._TODOLIST_TEXT."</td>";
    $str .= '<td><textarea cols="50" rows="5" name="text">'.stripslashes($text).'</textarea></td>';
    $str .= "</tr>";
    $str .= "<tr>";
    $str .= "<td>"._TODOLIST_RESPONSIBLE."</td><td>";
    $str .= makeUserDropdown("responsible_persons", $responsible_users, $project ,0,true);
    $str .= "</td></tr><tr><td>"._TODOLIST_DUE."</td>";
    $str .= "<td><input type=\"text\" name=\"due_date\" value=\"" . convDate($due_date) . "\" /><br/><pre>".convDate(_TODOLIST_DATEFORMAT).'</pre></td>';

    $str .= "</tr><tr><td>"._TODOLIST_CREATED_ON."</td>";
    $str .= '<td><input type="hidden" name="datum_erstellt" value="'.strftime("%Y-%m-%d",$date_created).'" />';
    $str .= convDate(strftime("%Y-%m-%d",$date_created)) . "</td></tr><tr>";
    $str .= "<td>"._TODOLIST_CREATED_BY."</td>";
    $str .= "<td>".$usernames["$created_by"]."</td>";
    $str .= "</tr>";
    $str .= "<tr>";
    $str .= "<td>"._TODOLIST_CHANGED_BY."</td>";
    $str .= "<td>".$usernames["$changed_by"]."</td>";
    $str .= "</tr>";
    $str .= "<tr>";
    $str .= "<td>"._TODOLIST_CHANGED_ON."</td>";
    $str .= "<td>";
    $str .= convDate(strftime("%Y-%m-%d %H:%M:%S", $date_changed)) . "</td></tr>";


    $str .= "</table>";
    $str .= '<select name="action" size="1">';
    $str .= '<option value="todo_change">'._TODOLIST_CHANGE."</option>";
    $str .= '<option value="todo_delete">'._TODOLIST_DELETE."</option>";
    $str .= "</select>";
    $str .= "&nbsp;&nbsp;<input type=\"submit\" value=\""._TODOLIST_SUBMIT."\" />";


    $str .= "<br /><br />";

    $todolist_notes_column = &$pntable['todolist_notes_column'];
    $todolist_users_column = &$pntable['todolist_users_column'];

    $result = $dbconn->Execute("SELECT $todolist_notes_column[note_id],$todolist_notes_column[text],
              $todolist_notes_column[date],$todolist_users_column[usernr]
              FROM $pntable[todolist_notes], $pntable[todolist_users]
              WHERE $todolist_notes_column[todo_id]=$id
              AND $todolist_notes_column[usernr]=$todolist_users_column[usernr]");
    $anzahl = $result->PO_RecordCount();
  
    $i = 0;

    if ($anzahl > 0){
        $str .= '<table border="1"><tr>';
        $str .= "<th align=\"left\">"._TODOLIST_NOTE."</th>";
        $str .= "<th align=\"left\">"._TODOLIST_USER."</th>";
        $str .= "<th align=\"left\">"._TODOLIST_DATE."</th>";
        $str .= "</tr>";
    }

    $todo_id=$id;

    for (;!$result->EOF;$result->MoveNext()) {
        $note_id    = $result->fields[0];
        $note_text= stripslashes($result->fields[1]);
        $datum    = $result->fields[2];
        $user_name    = pnUserGetVar('uname',$result->fields[3]);
        if (empty($user_name)) $user_name  = stripslashes(pnUserGetVar('uname',$usernr));

        $str .= "<tr><td>$note_text</td><td align=\"center\">$user_name</td><td>".strftime("%Y-%m-%d %H:%M:%S",$datum)."</td></tr>";
    }

    if ($anzahl > 0){
        $str .= "</table>";
    }
    $str .= "<hr noshade=\"noshade\"/>";

    $str .= "<table><tr>";
    $str .= "<th align=\"left\">"._TODOLIST_NOTE."</th>";
    $str .= "<th>&nbsp;</th>";
    $str .= "</tr><tr><td>";

    $str .= '<textarea cols="50" rows="4" name="note_text"></textarea></td>';
    $str .= "<td>";
    $str .= "&nbsp;&nbsp;<input type=\"submit\" value=\""._TODOLIST_SUBMIT."\" /></td>";
    $str .= "</tr></table>";
    $str .= "</form>";
    return $str;
} 
?>