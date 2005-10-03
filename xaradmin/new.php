<?php
/**
 * Add new item
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
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
 * @param $project
 * @param $percentage_completed
 * @param $text
 * @param $responsible_persons
 * @param $note_text
 *
 * @author Todolist module development team
 * @return array
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

    /* Get parameters from whatever input we need.  All arguments to this
     * function should be obtained from xarVarFetch(). xarVarFetch allows
     * the checking of the input variables as well as setting default
     * values if needed.  Getting vars from other places such as the
     * environment is not allowed, as that makes assumptions that will
     * not hold in future versions of Xaraya
     */
    if (!xarVarFetch('number',  'str:1:', $number,  $number,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name',    'str:1:', $name,    $name,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array',  $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    /* Initialise the $data variable that will hold the data to be used in
     * the blocklayout template, and get the common menu configuration - it
     * helps if all of the module pages have a standard menu at the top to
     * support easy navigation
     */
    $data = xarModAPIFunc('example', 'admin', 'menu');
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AddExample')) return;

    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();
    $data['invalid'] = $invalid;

    $item = array();
    $item['module'] = 'example';
    $hooks = xarModCallHooks('item', 'new', '', $item);

    if (empty($hooks)) {
        $data['hookoutput'] = '';
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
        $data['name'] = '';
    } else {
        $data['name'] = $name;
    }

    if (empty($number)) {
        $data['number'] = '';
    } else {
        $data['number'] = $number;
    }
    /* Return the template variables defined in this function */
    return $data;
}
?>