<?php // $Id$
/*  main page - switch routines etc */

function todolist_user_main()
{
list($route, $id, $order_by, $abfrage, $priority, $wildcards, $responsible_person, $action, 
     $project, $due_date, $percentage_completed, $text, $note_text, $printlayout, $status,
     $search_status, $search_project, $date_min, $date_max) = 
     pnVarCleanFromInput('route', 'id', 'order_by', 'abfrage', 'priority', 'wildcards',
     'responsible_person', 'action','project', 'due_date', 'percentage_completed', 'text',
     'note_text', 'printlayout', 'status', 'search_status', 'search_project', 'date_min', 'date_max');

$modinfo = pnModGetInfo(pnModGetIDFromName('todolist'));
list($dbconn) = pnDBGetConn();
$pntable = pnDBGetTables();

// overall routes
define ('FRONTPAGE', 0);
define ('DETAILS', 1);
define ('SEARCH', 2);
define ('ACTIONS', 4);
define ('PREFERENCES', 6);
// pages for the header info - negated mostly
define ('DETPAGE', 14);
define ('PREFPAGE', 18);
define ('SEARCHPAGE', 19);
define ('THELIST', 20);

$output = new pnHTML();

include_once('modules/'.pnVarPrepForOS($modinfo['directory']).'/functions.inc.php'); 
include_once('modules/'.pnVarPrepForOS($modinfo['directory']).'/header.inc.php');
include_once('modules/'.pnVarPrepForOS($modinfo['directory']).'/pages.inc.php');

if (!isset($page)) {
    $page = FRONTPAGE;
}
if ($page==PREFPAGE) {
    if (isset($showicons)) {
        pnSessionSetVar("todolist_show_icons",$showicons);
    } else {
        pnSessionSetVar("todolist_show_icons",0);
    }
}

// Is this really a valid user?
if (!pnUserLoggedIn()) {
    // user doesn't exist
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(_TODOLIST_LOGIN_USER_UNKNOWN1);
    $output->SetInputMode(_PNH_PARSEINPUT);
    return $output->GetOutput();
} else {
    $todolist_users_column = &$pntable['todolist_users_column'];
    $result = $dbconn->Execute("SELECT * FROM $pntable[todolist_users] WHERE
              $todolist_users_column[usernr]='".pnUserGetVar('uid')."'");
    if(!$result->PO_RecordCount()) {
        // user doesn't exist
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->Text(_TODOLIST_LOGIN_USER_UNKNOWN2);
        $output->SetInputMode(_PNH_PARSEINPUT);
        return $output->GetOutput();
    }

    pnSessionSetVar('todolist_show_icons',$result->fields[4]);
    pnSessionSetVar('todolist_my_tasks',$result->fields[3]);

    // Cache the projects a user is member in. Format '(proj1,proj2,..)'
    $todolist_project_members_column = &$pntable['todolist_project_members_column'];
    $sql2 = "SELECT $todolist_project_members_column[project_id]
        FROM $pntable[todolist_project_members]
        WHERE $todolist_project_members_column[member_id]=".pnUserGetVar('uid')."";
    $result = $dbconn->Execute($sql2);

    if ($result->PO_RecordCount() == 0 ) {
        $feedback = _TODOLIST_LOGIN_NO_PROJECT;
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->Text($feedback);
        $output->SetInputMode(_PNH_PARSEINPUT);
        return $output->GetOutput();
    }

    $my_projects = '(';
    for (;!$result->EOF;$result->MoveNext()){
        $my_projects .= $result->fields[0] . ",";
    }
    $my_projects = substr($my_projects, 0, -1) . ')';
    pnSessionSetVar('todolist_my_projects',$my_projects);

    // Check if the user is still member of his preferred project! Set to all if he isn't.
    if (!in_array(pnSessionGetVar('todolist_selected_project'),
        explode (",", substr(pnSessionGetVar('todolist_my_projects'), 1, -1)))) {
        pnSessionSetVar('todolist_selected_project',"all");
        
        $todolist_users_column = &$pntable['todolist_users_column'];
        $sql2 = "UPDATE $pntable[todolist_users]
              SET $todolist_users_column[primary_project]='all'
              WHERE $todolist_users_column[usernr]=".pnUserGetVar('uid')."";
        $dbconn->Execute($sql2);
    }
}

$date=date("Y-m-d H:i");

// We had a value from the form? Tell it the user.
if (isset($selected_project))
    pnSessionSetVar('todolist_selected_project',$selected_project);

if (!$route) {
    $route = FRONTPAGE;
}

// main switch - what are we trying to do?
switch($route) {

    // front page
    case FRONTPAGE:
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->Text(page_top(THELIST,$printlayout));
        $query = makeFrontQuery($order_by, pnSessionGetVar('todolist_selected_project'));
        $output->Text(printToDoTable($query,$order_by,$route));
        if (!$printlayout)  {
            $output->Text(add_box());
            $output->Text(search_box($priority,$search_status, $search_project,$responsible_person,$date_min,$date_max));
        }
        $output->Text(page_foot(THELIST,$printlayout));
        $output->SetInputMode(_PNH_PARSEINPUT);
        return $output->GetOutput();
    break;
    
    // viewing a record's details
    case DETAILS:
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->Text(page_top(DETPAGE,$printlayout));
        $output->Text(details_page($id));
        $output->Text(page_foot(DETPAGE,$printlayout));
        $output->SetInputMode(_PNH_PARSEINPUT);
        return $output->GetOutput();
    break;
    
    // searching
    case SEARCH:
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->Text(page_top($page,$printlayout));
        if (!is_array($responsible_person)) {
            $responsible_person = array($responsible_person);
        }
        $query = makeSearchQuery($wildcards,$priority, $search_status,$search_project, $responsible_person,$order_by,$date_min,$date_max);
        if (!$printlayout)  {
            $output->Text(search_box($priority, $search_status, $search_project,$responsible_person,$date_min,$date_max));
        }
        $output->Text(printToDoTable($query,$order_by,$route));
        $output->Text(page_foot($page,$printlayout));
        $output->SetInputMode(_PNH_PARSEINPUT);
        return $output->GetOutput();
        
    break;
    
    case PREFERENCES:
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->Text(page_top(PREFPAGE,$printlayout));
        $output->Text(userDialog());
        $output->Text(page_foot(PREFPAGE,$printlayout));
        $output->SetInputMode(_PNH_PARSEINPUT);
        return $output->GetOutput();
    break;
    
    // actually making some change
    case ACTIONS: // actually do stuff

        // switch on action to see what precisely that is
        switch ($action){
        
        // deleting a task
        case "todo_delete":
            delete_todo($id);
            $feedback = _TODOLIST_DATASET_DELETED;
            $output->SetInputMode(_PNH_VERBATIMINPUT);
            $output->Text(page_top(THELIST,$printlayout));
            $query = makeFrontQuery($order_by, pnSessionGetVar('todolist_selected_project'));
            $output->Text(printToDoTable($query,$order_by,$route));
            $output->Text(add_box());
            $output->Text(search_box($priority,"",$search_project,$responsible_person,$date_min,$date_max));
            $output->Text(page_foot(THELIST,$printlayout));
            $output->SetInputMode(_PNH_PARSEINPUT);
            return $output->GetOutput();
        break;
    
        // update a task
        case "todo_change":
            update_todo($due_date, $priority, $status, $percentage_completed, $text, $responsible_persons,
                        $id, $note_text, $project);
            $feedback = _TODOLIST_DATASET_CHANGED;
            $output->SetInputMode(_PNH_VERBATIMINPUT);
            $output->Text(page_top(THELIST,$printlayout));
            $query = makeFrontQuery($order_by, pnSessionGetVar('todolist_selected_project'));
            $output->Text(printToDoTable($query,$order_by,$route));
            $output->Text(add_box());
            $output->Text(search_box($priority, "", $search_project,$responsible_person,$date_min,$date_max));
            $output->Text(page_foot(THELIST,$printlayout));
            $output->SetInputMode(_PNH_PARSEINPUT);
            return $output->GetOutput();
        break;
    
        // add a new task
        case "todo-add":
            add_todo($due_date,$priority,$project,$percentage_completed,$text,$responsible_person,$note_text);
            $feedback = _TODOLIST_TODO_ADDED1.'"'.stripslashes($text).'"'._TODOLIST_TODO_ADDED2;
            $output->SetInputMode(_PNH_VERBATIMINPUT);
            $output->Text(page_top(THELIST,$printlayout));
            $query = makeFrontQuery($order_by, pnSessionGetVar('todolist_selected_project'));
            $output->Text(printToDoTable($query,$order_by,$route));
            $output->Text(add_box());
            $output->Text(search_box($priority, "", $search_project,$responsible_person,$date_min,$date_max));
            $output->Text(page_foot(THELIST,$printlayout));
            $output->SetInputMode(_PNH_PARSEINPUT);
            return $output->GetOutput();
        break;
    }
    break;
}
}

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
        pnSessionSetVar('statusmsg', _TODOLIST_UPDATED);
    }
    pnRedirect(pnModURL('todolist', 'user', 'main'));

    return true;
}


?>
