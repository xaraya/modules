<?php
/**
 * Display a todo
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */

/**
 * Display a todo
 *
 * This is the function to provide detailed informtion on a single todo
 * available from this module. The same page contains a form from which the todo can be modified.
 * This depends on privileges set for that todo.
 *
 * @author the Todolist module development team
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['todo_id'] the item id to display
 */

/*
 *
 * details page
 *
 * generates the detail-page for a task
 *
 * @param $id    int    The ID of the task that should be shown
 *
 * @return HTML

function details_page($id){
    global $detail_project;

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();
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

    $users_column = &$pntable['users_column'];
    if (!($result = $dbconn->Execute("SELECT $users_column[uname] FROM $pntable[roles]
        WHERE xar_uid IN ($created_by, $changed_by)")))
        return false;

    for (;!$result->EOF;$result->MoveNext()){
        $usernames[$result->fields[0]] = pnUserGetVar($result->fields[0]);
    }

    $str .=  '<form method="post" name="detailform" action="'.pnModURL('todolist', 'user', 'main', array()).'">
    <input type="hidden" name="module" value="todolist" />
    <input type="hidden" name="route" value="'.ACTIONS.'" />
    <table border="0">';

    $str .= '<tr>
    <td><input type="hidden" name="id" value="'.$id. '" readonly="readonly" />'.xarML('project').'</td>
    <td>' . makeProjectDropdown("project",$project,false,"updatedetails()") . '</td></tr>';
    // $str .= '<tr><td>'.xarML('project').'</td><td>'.$project_name.'</td></tr>';
    $str .= '<tr><td>'.xarML('priority').'</td>';

    $priority = switchPriority($priority);

    $str .= "<td><select name=\"priority\" size=\"1\">";
    if ($priority == xarML('high')) {
      $str .= "<option selected=\"selected\">".xarML('high')."</option>";
    } else {
      $str .= "<option>".xarML('high')."</option>";
    }

    if ($priority == xarML('med')){
      $str .= "<option selected>".xarML('med')."</option>";
    } else {
      $str .= "<option>".xarML('med')."</option>";
    }

    if ($priority == xarML('low')){
      $str .= "<option selected=\"selected\">".xarML('low')."</option>";
    } else {
      $str .= "<option>".xarML('low')."</option>";
    }

    //if ($priority == xarML('done')){
    //  $str .= "<option selected=\"selected\">".xarML('done')."</option>";
    //} else {
    //  $str .= "<option>".xarML('done')."</option>";
    //}


    $str .= '</select></td></tr>';
    $str .= '
    <tr><td>'.xarML('Status').'</td><td>'.makeStatusDropdown("status",$status,false,'updatepercentage()').'</td></tr>';
    $str .='<tr><td>'.xarML('percentage').'</td><td>
    <select name="percentage_completed" size="1" onchange="updatestatus()">';
    for ($i = 0 ; $i <= 100 ; $i += 20)
    {
    if ($percentage_completed == $i)
      $str .= '<option value="'.$i.'" selected="selected">'.$i.'</option>';
    else
      $str .= '<option value="'.$i.'">'.$i.'</option>';
    }
    $str .= '</select></td></tr><tr>';
    $str .= "<td>".xarML('Text')."</td>";
    $str .= '<td><textarea cols="50" rows="5" name="text">'.stripslashes($text).'</textarea></td>';
    $str .= "</tr>";
    $str .= "<tr>";
    $str .= "<td>".xarML('responsible')."</td><td>";
    $str .= makeUserDropdown("responsible_persons", $responsible_users, $project ,0,true);
    $str .= "</td></tr><tr><td>".xarML('due date')."</td>";
    $str .= "<td><input type=\"text\" name=\"due_date\" value=\"" . convDate($due_date) . "\" /><br/><pre>".convDate(xarML('YYYY-MM-DD')).'</pre></td>';

    $str .= "</tr><tr><td>".xarML('created on')."</td>";
    $str .= '<td><input type="hidden" name="datum_erstellt" value="'.strftime("%Y-%m-%d",$date_created).'" />';
    $str .= convDate(strftime("%Y-%m-%d",$date_created)) . "</td></tr><tr>";
    $str .= "<td>".xarML('created by')."</td>";
    $str .= "<td>".$usernames["$created_by"]."</td>";
    $str .= "</tr>";
    $str .= "<tr>";
    $str .= "<td>".xarML('changed by')."</td>";
    $str .= "<td>".$usernames["$changed_by"]."</td>";
    $str .= "</tr>";
    $str .= "<tr>";
    $str .= "<td>".xarML('changed')."</td>";
    $str .= "<td>";
    $str .= convDate(strftime("%Y-%m-%d %H:%M:%S", $date_changed)) . "</td></tr>";


    $str .= "</table>";
    $str .= '<select name="action" size="1">';
    $str .= '<option value="todo_change">'.xarML('change')."</option>";
    $str .= '<option value="todo_delete">'.xarML('delete')."</option>";
    $str .= "</select>";
    $str .= "&nbsp;&nbsp;<input type=\"submit\" value=\"".xarML('submit')."\" />";


    $str .= "<br /><br />";

    $todolist_notes_column = &$pntable['todolist_notes_column'];
    $users_column = &$pntable['users_column'];

    $result = $dbconn->Execute("SELECT $todolist_notes_column[note_id],$todolist_notes_column[text],
              $todolist_notes_column[date],xar_uid
              FROM $pntable[todolist_notes], $pntable[roles]
              WHERE $todolist_notes_column[todo_id]=$id
              AND $todolist_notes_column[usernr]=xar_uid");
    $anzahl = $result->PO_RecordCount();

    $i = 0;

    if ($anzahl > 0){
        $str .= '<table border="1"><tr>';
        $str .= "<th align=\"left\">".xarML('note')."</th>";
        $str .= "<th align=\"left\">".xarML('user')."</th>";
        $str .= "<th align=\"left\">".xarML('date')."</th>";
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
    $str .= "<th align=\"left\">".xarML('note')."</th>";
    $str .= "<th>&nbsp;</th>";
    $str .= "</tr><tr><td>";

    $str .= '<textarea cols="50" rows="4" name="note_text"></textarea></td>';
    $str .= "<td>";
    $str .= "&nbsp;&nbsp;<input type=\"submit\" value=\"".xarML('submit')."\" /></td>";
    $str .= "</tr></table>";
    $str .= "</form>";
    return $str;
}
 */

function todolist_user_display($args)
{
    extract($args);

    if (!xarVarFetch('todo_id', 'id', $todo_id)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    /*if (!xarVarFetch('todo_text', 'str::', $todo_text, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('todo_priority', 'int::', $todo_priority, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('percentage_completed', 'int::', $percentage_completed, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('created_by', 'int', $created_by, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('due_date', 'str', $due_date, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_created', 'str', $date_created, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_changed', 'str', $date_changed, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('changed_by', 'int::', $changed_by, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('todo_status', 'str', $todo_status, '', XARVAR_NOT_REQUIRED)) return;
    */
    if (!empty($objectid)) {
        $todo_id = $objectid;
    }
    /* Get menu
     * This menu should allow for interactive sorting and selecting
     */
    $data = xarModAPIFunc('todolist', 'user', 'menu');
    /* Prepare the variable that will hold some status message if necessary */
    $data['status'] = '';
    // Get the Item
    $item = xarModAPIFunc('todolist',
        'user',
        'get',
        array('todo_id' => $todo_id));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* Get the responsible people */
    $responsibles = xarModAPIFunc('todolist', 'user', 'getallresponsible', array('todo_id' => $todo_id));
    /* Let any transformation hooks know that we want to transform some text.
     */
    $item['transform'] = array('todo_text','date_created','date_changed');
    $item = xarModCallHooks('item',
        'transform',
        $todo_id,
        $item);
    /* Fill in the details of the item.*/
    // The project
    $project = xarModAPIFunc('todolist', 'user', 'getproject', array('project_id' => $item['project_id']));
    $data['project_name'] = $project['project_name'];
    $data['project'] = $project;

    $data['created_by'] = xarUserGetVar(name, $item['created_by']);
    $data['changed_by'] = xarUserGetVar(name, $item['changed_by']);

    $data['todo_id'] = $todo_id;
    $data['item'] = $item;

    /* Build array with percentages
    */
    $perc_compl = $item['percentage_completed'];
    $perc_compl_options = '';
    for($i = 0;$i <= 91; $i+ 10) {
        $j = str_pad($i,2,"0",STR_PAD_LEFT);
        $perc_compl_options.='<option value="'.$i.'"';
        if ($i == $perc_compl) {
            $perc_compl_options .= " selected";
        }
        $perc_compl_options.='>'.$j.'</option>';
    }
    $data['perc_compl_options'] = $perc_compl_options;

    $priority = $item['priority'];
    /* Build array with priorities
    */
    $prio_options = '';
    for($i = 0;$i <= 9; $i++) {
        $j = str_pad($i,2,"0",STR_PAD_LEFT);
        $prio_options.='<option value="'.$i.'"';
        if ($i == $priority) {
            $prio_options .= " selected";
        }
        $prio_options.='>'.$j.'</option>';
    }
    $data['prio_options'] = $prio_options;

    //$data['is_bold'] = xarModGetVar('example', 'bold');
    /* Note : module variables can also be specified directly in the
     * blocklayout template by using &xar-mod-<modname>-<varname>;
     * Note that you could also pass on the $item variable, and specify
     * the labels directly in the blocklayout template. But make sure you
     * use the <xar:ml>, <xar:mlstring> or <xar:mlkey> tags then, so that
     * labels can be translated for other languages...
     * Save the currently displayed item ID in a temporary variable cache
     */
    xarVarSetCached('Blocks.todolist', 'todo_id', $todo_id);
    /* Let any hooks know that we are displaying an item.
     */
    $item['returnurl'] = xarModURL('todolist',
        'user',
        'display',
        array('todo_id' => $todo_id));
    $hooks = xarModCallHooks('item',
        'display',
        $todo_id,
        $item);
    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }
    /* Once again, we are changing the name of the title for better
     * Search engine capability.
     */
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Due on:') $item['due_date']));
    /* Return the template variables defined in this function */
    return $data;
}
?>