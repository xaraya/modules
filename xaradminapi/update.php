<?php
/**
 * Update an example item
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */

/**
 * updates a todo-entry
 *
 * Updates a Todo-Entry into database and generates a mail-notify to subscribed users.
 * 
 * @author the Example module development team 
 * @param $due_date        string    the due date
 * @param $priority
 * @param $status
 * @param $percentage_completed
 * @param $text
 * @param $responsible_persons
 * @param $id
 * @param $note_text
 * @param $selected_project
 * @param  $args ['exid'] the ID of the item
 * @param  $args ['name'] the new name of the item
 * @param  $args ['number'] the new number of the item
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function todolist_adminapi_update($args)
{ 
    /* Das Datum muß evtl. wieder nach US-Format konvertiert werden... 
    if (pnModGetVar('todolist', 'DATEFORMAT') != "1") {
        $due_date=convDateToUS($due_date);
    }
    $priority = switchPriority($priority);


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

     */
    extract($args);
    /* Argument check - make sure that all required arguments are present
$due_date, $priority, $status, $percentage_completed, $text, $responsible_persons, $id,
        $note_text, $selected_project
     */
    $invalid = array();
    if (!isset($exid) || !is_numeric($exid)) {
        $invalid[] = 'item ID';
    }
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($number) || !is_numeric($number)) {
        $invalid[] = 'number';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'Example');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Get Todo
     */
    $item = xarModAPIFunc('todolist',
        'user',
        'get',
        array('todo_id' => $todo_id)); 
    /*Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing.
     * However, in this case we had to wait until we could obtain the item
     * name to complete the instance information so this is the first
     * chance we get to do the check
     * Note that at this stage we have two sets of item information, the
     * pre-modification and the post-modification.  We need to check against
     * both of these to ensure that whoever is doing the modification has
     * suitable permissions to edit the item otherwise people can potentially
     * edit areas to which they do not have suitable access
     */
    if (!xarSecurityCheck('EditExample', 1, 'Item', "$item[name]:All:$exid")) {
        return;
    }
    if (!xarSecurityCheck('EditExample', 1, 'Item', "$name:All:$exid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $todostable = $xartable['todolist_todos'];
    /* Update the item - the formatting here is not mandatory, but it does
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

     */
    $query = "UPDATE $todostable
            SET
            xar_project_id           = ?,
            xar_todo_text            = ?,
            xar_todo_priority        = ?,
            xar_percentage_completed = ?,
            xar_created_by           = ?,
            xar_due_date             = ?,
            xar_date_created         = ?,
            xar_date_changed         = ?,
            xar_changed_by           = ?,
            xar_status               = ?
            WHERE xar_todo_id        = ?";
    $bindvars = array($project_id, $text, $priority, $percentage_completed, $created_by, $date_created, 
    $date_changed, $changed_by, $status, $todo_id);
    $result = &$dbconn->Execute($query,$bindvars);
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Let any hooks know that we have updated an item.  As this is an
     * update hook we're passing the updated $item array as the extra info
     */
    $item['module'] = 'todolist';
    $item['itemid'] = $todo_id;
    $item['name'] = $name;
    $item['number'] = $number;
    xarModCallHooks('item', 'update', $todo_id, $item);
    
    /* Let the calling process know that we have finished successfully */
    return true;
} 
?>