<?php
/**
 * Standard function to Delete and item
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */
/**
 * Standard function to Delete an item
 *
 * This is a standard function that is called whenever an administrator
 * wishes to delete a current module item.  Note that this function is
 * the equivalent of both of the modify() and update() functions above as
 * it both creates a form and processes its output.  This is fine for
 * simpler functions, but for more complex operations such as creation and
 * modification it is generally easier to separate them into separate
 * functions.  There is no requirement in the Xaraya MDG to do one or the
 * other, so either or both can be used as seen appropriate by the module
 * developer
 *
 * @author Todolist Module Development Team
 * @param  $ 'exid' the id of the item to be deleted
 * @param  $ 'confirm' confirm that this item can be deleted
 */
function todolist_admin_deleteproject($args)
{ 
    extract($args);

    if (!xarVarFetch('project_id',     'int:1:', $project_id)) return;
    if (!xarVarFetch('objectid', 'int:1:', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',  'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $exid = $objectid;
    }
    /* Get project */
    $item = xarModAPIFunc('todolist',
        'user',
        'getproject',
        array('exid' => $project_id));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* Security check */
    if (!xarSecurityCheck('DeleteTodolist', 1, 'Item', "All:All:All")) { // TODO
        return;
    }
    /* Check for confirmation. */
    if (empty($confirm)) {
        /* No confirmation yet - display a suitable form to obtain confirmation
         * of this action from the user
         */
        $data = xarModAPIFunc('todolist', 'admin', 'menu');

        /* Specify for which item you want confirmation */
        $data['project_id'] = $project_id;

        /* Add some other data you'll want to display in the template */
        $data['project_id'] = xarML('Item ID');
        $data['project_name'] = xarVarPrepForDisplay($item['project_name']);
        /* TODO: get attachments like members, todos... */
        
        /* Generate a one-time authorisation code for this operation */
        $data['authid'] = xarSecGenAuthKey();

        /* Return the template variables defined in this function */
        return $data;
    }

    if (!xarSecConfirmAuthKey()) return;

    if (!xarModAPIFunc('todolist',
            'admin',
            'deleteproject',
            array('project_id' => $project_id))) {
        return; // throw back
    }
    
    xarSessionSetVar('statusmsg', xarML('Project has been deleted'));

    xarResponseRedirect(xarModURL('todolist', 'admin', 'view')); //??
    
    /* Return */
    return true;
}
?>