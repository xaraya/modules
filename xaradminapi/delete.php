<?php
/**
 * Delete an example item
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 */
/**
 * deletes a todo-entry
 *
 * Deletes a Todo-Entry, and all associated notes. A mail-notify is not generated!
 *
 * @author the Example module development team 
 * @param $todo_id    int    the primary key of the todo-entry
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function example_adminapi_delete($args)
{ 
    /* Get arguments from argument array - all arguments to this function

    if (pnUserGetVar('uid')) {
        // TODO: Add mail-notification
        generateMail($todo_id, "todo_delete");
        



     */
    extract($args);
    /* Argument check - make sure that all required arguments are present and
     * in the right format, if not then set an appropriate error message
     * and return
     */
    if (!isset($exid) || !is_numeric($exid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'admin', 'delete', 'Example');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* The user API function is called.  This takes the item ID which
     * we obtained from the input and gets us the information on the
     * appropriate item.  If the item does not exist we post an appropriate
     * message and return
     */
    $item = xarModAPIFunc('todolist',
        'user',
        'get',
        array('todo_id' => $todo_id));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    if (!xarSecurityCheck('DeleteTodolist', 1, 'Item', "All:All:All")) {//TODO
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $todostable = $xartable['todolist_todos'];
    /* Get tables
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
     */

    /* Delete the todo finally
     */
    $query = "DELETE FROM $todostable WHERE xar_todo_id = ?";

    /* The bind variable $exid is directly put in as a parameter. */
    $result = &$dbconn->Execute($query,array($todo_id));
    
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Let any hooks know that we have deleted an item.  As this is a
     * delete hook we're not passing any extra info
     * xarModCallHooks('item', 'delete', $exid, '');
     */
    $item['module'] = 'todolist';
    $item['itemid'] = $todo_id;
    xarModCallHooks('item', 'delete', $todo_id, $item);
    
    /* Let the calling process know that we have finished successfully */
    return true;
}
?>