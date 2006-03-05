<?php
/**
 * Tasks module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Tasks Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Tasks Module Development Team
 */
/**
 * @author Chad Kraeft
 */
/**
 * Publish a task
 *
 */
function tasks_admin_publish($args)
{
    $id = xarVarCleanFromInput('id');

    extract($args);

    // SECAUTH KEY CHECK REMOVED DUE TO MULTIPLE FORM OCCURRENCES CONFLICTING ON KEY USAGE
    // PERMISSIONS CHECK SHOULD BE SUFFICIENT TO PREVENT MALICIOUS USAGE

    if($returnid = xarModAPIFunc('tasks',
                                'admin',
                                'publish',
                                array('id'    => $id))) {
        xarSessionSetVar('errormsg', xarGetStatusMsg() . '<br>' . xarML("Tasks updated"));
    }

    xarResponseRedirect(xarModURL('tasks', 'user', 'display', array('id' => $returnid,
                                                            '' => '#tasklist')));

    return true;
}

?>