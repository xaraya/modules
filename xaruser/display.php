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