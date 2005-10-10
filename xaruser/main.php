<?php
/**
 * The main user function
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */

/**
 * The main user function
 *
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments.  As such it can be used for a number
 * of things, but most commonly it either just shows the module menu and
 * returns or calls whatever the module designer feels should be the default
 * function (often this is the view() function)
 *
 * @author the Todolist module development team
 */
function example_user_main()
{ 
    /* Security check - important to do this as early as possible to avoid
function todolist_user_main()
{
list($route, $id, $order_by, $abfrage, $priority, $wildcards, $responsible_person, $action, 
     $project, $due_date, $percentage_completed, $text, $note_text, $printlayout, $status,
     $search_status, $search_project, $date_min, $date_max) = 
     pnVarCleanFromInput('route', 'id', 'order_by', 'abfrage', 'priority', 'wildcards',
     'responsible_person', 'action','project', 'due_date', 'percentage_completed', 'text',
     'note_text', 'printlayout', 'status', 'search_status', 'search_project', 'date_min', 'date_max');

$modinfo = pnModGetInfo(pnModGetIDFromName('todolist'));
$dbconn =& xarDBGetConn();;
$pntable =& xarDBGetTables();

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
    $output->Text(xarML('You are anonymous user!'));
    $output->SetInputMode(_PNH_PARSEINPUT);
    return $output->GetOutput();
} else {
    $userpref = xarModGetUserVar('todolist','userpref',pnUserGetVar('uid'));
    if (empty($userpref)) {
        // user doesn't exist
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->Text(xarML('User unknown!'));
        $output->SetInputMode(_PNH_PARSEINPUT);
        return $output->GetOutput();
    }

    list($user_email_notify,$user_primary_project,$user_my_tasks, $user_show_icons) = explode(';',$userpref);
    pnSessionSetVar('todolist_show_icons',$user_show_icons);
    pnSessionSetVar('todolist_my_tasks',$user_my_tasks);

    // Cache the projects a user is member in. Format '(proj1,proj2,..)'
    $todolist_project_members_column = &$pntable['todolist_project_members_column'];
    $sql2 = "SELECT $todolist_project_members_column[project_id]
        FROM $pntable[todolist_project_members]
        WHERE $todolist_project_members_column[member_id]=".pnUserGetVar('uid')."";
    $result = $dbconn->Execute($sql2);

    if ($result->PO_RecordCount() == 0 ) {
        $feedback = xarML("You are not a member in any projects, so you can't use this software. Contact administrator.");
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

        $userpref = xarModGetUserVar('todolist','userpref');
        if (!empty($userpref)) {
            list($user_email_notify,$user_primary_project,$user_my_tasks, $user_show_icons) = explode(';',$userpref);
        } else {
            list($user_email_notify,$user_primary_project,$user_my_tasks, $user_show_icons) = explode(';','1;all;0;1');
        }
        $user_primary_project = 'all';
        $userpref = $user_email_notify.';'.$user_primary_project.';'.$user_my_tasks.';'.$user_show_icons;
        xarModSetUserVar('todolist','userpref',$userpref);
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
            $feedback = xarML('dataset deleted!');
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
            $feedback = xarML('dataset changed.');
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
            $feedback = xarML('ToDo').'"'.stripslashes($text).'"'.xarML(' added to database');
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

     * generally either 'overview' or 'read'
     */
    if (!xarSecurityCheck('ViewExample')) return;
    /* If you want to go directly to some default function, instead of
     * having a separate main function, you can simply call it here, and
     * use the same template for user-main.xard as for user-view.xard
     * return xarModFunc('example','user','view');
     * Initialise the $data variable that will hold the data to be used in
     * the blocklayout template, and get the common menu configuration - it
     * helps if all of the module pages have a standard menu at the top to
     * support easy navigation
     */
    $data = xarModAPIFunc('example', 'user', 'menu');
    /* Specify some other variables used in the blocklayout template */
    $data['welcome'] = xarML('Welcome to this Example module...');
    /* We also may want to change the title of the page for a little
     * better search results from the spiders.  All we are doing below
     * Is telling Xaraya what the title of the page should be, and
     * Xaraya controls the rest.
     */
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Welcome')));
    /* Return the template variables defined in this function */
    return $data;
    /* Note : instead of using the $data variable, you could also specify
     * the different template variables directly in your return statement :

     * return array('menutitle' => ...,
     * 'welcome' => ...,
     * ... => ...);
     */
}
?>