<?php
/**
 * Create a new project
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */

/**
 * Create a new project
 * 
 * Standard function to create a new project
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('example','admin','new') to create a new item
 * 
 * @author Todolist module development team
 * @param  $ 'name' the name of the item to be created
 * @param  $ 'number' the number of the item to be created
 */
function todolist_admin_createproject($args)
{ 
    extract($args);

    if (!xarVarFetch('projectid',     'id', $projectid,     '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',  'str:1:', $invalid,  '', XARVAR_NOT_REQUIRED)) return; 
    if (!xarVarFetch('project_name',   'str:1:', $project_name,   '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('project_description',     'str:1:', $project_description,     '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('project_leader',     'str:1:', $project_leader,     '', XARVAR_NOT_REQUIRED)) return;


    /* Argument check
    $item = xarModAPIFunc('todolist',
                          'user',
                          'validateitem',
                          array('name' => $name));
*/
    // Argument check
    $invalid = array();
    if (empty($project_leader) || !is_numeric($project_leader)) {
        $invalid['project_leader'] = 1;
        $number = '';
    } 
    if (empty($project_name) || !is_string($project_name)) {
        $invalid['project_name'] = 1;
        $name = '';
    } 
    // check if we have any errors
    if (count($invalid) > 0) {
        /* If we get here, we have encountered errors.
         * Send the user back to the admin_new form
         * call the admin_new function and return the template vars
         */
        return xarModFunc('todolist', 'admin', 'newproject',
                          array('project_name' => $project_name,
                                'project_leader' => $project_leader,
                                'project_description' => $project_description,
                                'invalid' => $invalid));
    } 
    /* Confirm authorisation code.  This checks that the form had a valid
     * authorisation code attached to it.  If it did not then the function will
     * proceed no further as it is possible that this is an attempt at sending
     * in false data to the system
     */
    if (!xarSecConfirmAuthKey()) return;
    /* 
     * The API function is called.  Note that the name of the API function and
     */
    $projectid = xarModAPIFunc('todolist',
                          'admin',
                          'createproject',
                          array('project_name' => $project_name,
                        'project_description' => $project_description,
                        'project_leader' => $project_leader)); 

    if (!isset($projectid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // Redirect at success
    xarResponseRedirect(xarModURL('todolist', 'admin', 'viewprojects'));
    /* Return true, in this case */
    return true;
} 
?>