<?php
/**
 * Create a new item
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */

/**
 * Create a new item
 *
 * Standard function to create a new item
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('example','admin','new') to create a new item
 *
 * @author Example module development team
 * @param  $ 'name' the name of the item to be created
 * @param  $ 'number' the number of the item to be created
 */
function todolist_admin_create($args)
{
    extract($args);

    /* Get parameters from whatever input we need. */
    if (!xarVarFetch('todo_id',     'id', $todo_id,     '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('text',  'str:1:', $text,  '',  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('priority',    'int:1:10', $priority,    5,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('percentage_completed',    'int:1:100', $percentage_completed,    0,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('due_date',  'str:1:', $due_date,  '',  XARVAR_NOT_REQUIRED)) return; //Date validation?
    if (!xarVarFetch('project_id',  'id', $project_id,  0,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('todostatus',  'int:1:', $todostatus,  1,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',  'array', $invalid,  '', XARVAR_NOT_REQUIRED)) return;


    /* Argument check - we only need some values for this to work.
     Most checking is done by xarVarFetch
     Just enter the todo, and process with standard parameters

    $item = xarModAPIFunc('todolist',
                          'user',
                          'validateitem',
                          array('name' => $name));
     */
    // Argument check
    $invalid = array();
    if (empty($number) || !is_numeric($number)) {
        $invalid['number'] = 1;
        $number = '';
    }
    if (empty($name) || !is_string($name)) {
        $invalid['name'] = 1;
        $name = '';
    }

    if (!empty($name) && $item['name'] == $name) {
        $invalid['duplicatename'] = 1;
        $duplicatename = '';
    }
    // check if we have any errors
    // We want to be very cool with error, mainly just take the todo
    if (count($invalid) > 0) {
        return xarModFunc('todolist', 'admin', 'new',
                          array('name' => $name,
                                'number' => $number,
                                'invalid' => $invalid));
    }
    /* Confirm authorisation code. */
    if (!xarSecConfirmAuthKey()) return;
    // Create item
    $todo_id = xarModAPIFunc('todolist',
                          'admin',
                          'create',
                          array('name' => $name,
                                'number' => $number));
    /* The return value of the function is checked here, and if the function
     * suceeded then an appropriate message is posted.  Note that if the
     * function did not succeed then the API function should have already
     * posted a failure message so no action is required
     */
    if (!isset($exid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('todolist', 'user', 'view'));
    /* Return true, in this case */
    return true;
}
?>