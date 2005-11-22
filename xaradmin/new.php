<?php
/**
 * Add new todo
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */

/**
 * add a todo-entry
 *
 * Inserts a Todo-Entry into database and generates a mail-notify to subscribed users.
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 *
 * @param $due_date        string    the due date
 * @param $priority
 * @param $project id
 * @param $percentage_completed int
 * @param $text
 * @param $responsible_persons
 * @param $note_text depr, now in comments
 *
 * @author Todolist module development team
 * @return array
 * @TODO MichelV <1> Add privs
 */
function todolist_admin_new($args)
{
/*
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

     */
    extract($args);

    if (!xarVarFetch('text',  'str:1:', $text,  $text,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('priority',    'int:1:10', $priority,    $priority,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('percentage_completed',    'int:1:100', $percentage_completed,    $percentage_completed,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('due_date',  'str:1:', $due_date,  $due_date,  XARVAR_NOT_REQUIRED)) return; //Date validation?
    if (!xarVarFetch('project_id',  'id', $project_id,  $project_id,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('todostatus',  'int:1:', $todostatus,  $todostatus,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array',  $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    /* Initialise the $data variable that will hold the data to be used in */
    $data = xarModAPIFunc('todolist', 'admin', 'menu');
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AddTodolist')) return; // TODO

    // Get the possible groups for this new todo


    // Get the possible project

    /* Build array with percentages
    */
    $perc_compl_options = '';
    for($i = 0;$i <= 91; $i+ 10) {
        $j = str_pad($i,2,"0",STR_PAD_LEFT);
        $perc_compl_options.='<option value="'.$i.'"';
        $perc_compl_options.='>'.$j.'</option>';
    }
    $data['perc_compl_options'] = $perc_compl_options;

    /* Build array with priorities
    */
    $prio_options = '';
    for($i = 0;$i <= 9; $i++) {
        $j = str_pad($i,2,"0",STR_PAD_LEFT);
        $prio_options.='<option value="'.$i.'"';
        $prio_options.='>'.$j.'</option>';
    }
    $data['prio_options'] = $prio_options;

    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();
    $data['invalid'] = $invalid;

    $item = array();
    $item['module'] = 'todolist';
    $hooks = xarModCallHooks('item', 'new', '', $item);

    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        /* You can use the output from individual hooks in your template too, e.g. with
         * $hookoutput['categories'], $hookoutput['dynamicdata'], $hookoutput['keywords'] etc.
         */
        $data['hookoutput'] = $hooks;
    }
    $data['hooks'] = '';
    /* For E_ALL purposes, we need to check to make sure the vars are set.
     * If they are not set, then we need to set them empty to surpress errors
     */
    if (empty($name)) {
        $data['text'] = '';
    } else {
        $data['text'] = $text;
    }

    if (empty($due_date)) {
        $data['due_date'] = '';
    } else {
        $data['due_date'] = $due_date;
    }
    /* Return the template variables defined in this function */
    return $data;
}
?>