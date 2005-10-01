<?php
/**
 * Standard function to update a current project
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */
/**
 * Standard function to update a current item
 *
 * This function is called with the results of the
 * form supplied by xarModFunc('example','admin','modify') to update a current item
 * 
 * @author Todolist module development team
 * @param  $ 'exid' the id of the item to be updated
 * @param  $ 'name' the name of the item to be updated
 * @param  $ 'number' the number of the item to be updated
 */
function todolist_admin_updateproject($args)
{ 
    extract($args);

    /* Get parameters from whatever input we need.
     */
    if (!xarVarFetch('project_id',      'int:1:', $project_id,     $project_id,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid',        'int:1:', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',         'str:1:', $invalid,  '',        XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('project_description',   'str:1:', $project_description,   $project_description,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('project_name',    'str:1:', $project_name,     $project_name,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('project_leader',  'int:1:', $project_leader, $oproject_leader, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('project_members', 'array', $project_members, $project_members, XARVAR_NOT_REQUIRED)) return;
    

    if (!empty($objectid)) {
        $project_id = $objectid;
    }

    if (!xarSecConfirmAuthKey()) return;

    $invalid = array();
    if (empty($project_name) || !is_string($name)) {
        $invalid['project_name'] = 1;
        $project_name = '';
    }
    if (empty($project_description) || !is_string($project_description)) {
        $invalid['project_description'] = 1;
        $project_description = '';
    }

    /* check if we have any errors */
    if (count($invalid) > 0) {
        /* call the admin_new function and return the template vars
         * (you need to copy admin-new.xd to admin-create.xd here)
         */
        return xarModFunc('todolist', 'admin', 'modifyproject',
                          array('project_id'        => $project_id,
                                'project_name'      => $project_name,
                                'project_description' => $project_description,
                                'project_leader'    => $project_leader,
                                'project_members'   => $project_members
                                'invalid'           => $invalid));
    }

    /* The API function is called.
     *
     * What about the members here?
     */
    if (!xarModAPIFunc('todolist',
                       'admin',
                       'updateproject',
                       array('project_id'           => $project_id,
                             'project_name'         => $project_name,
                             'project_description'  => $project_description,
                             'project_leader'       => $project_leader,
                             'project_members'      => $project_members))) {
        return; 
    }

    xarSessionSetVar('statusmsg', xarML('Project was successfully updated!'));
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('todolist', 'admin', 'viewprojects'));
    /* Return */
    return true;
}
?>