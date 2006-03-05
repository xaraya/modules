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
 * Add new task
 *
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 *
 * @author Tasks module development team
 * @return array
 */
function tasks_admin_new($args)
{
    $data=array();
    if (!xarVarFetch('module', 'str:1:', $module, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('type', 'str:1:', $type, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('func', 'str:1:', $func, NULL, XARVAR_NOT_REQUIRED)) return;

    extract($args);

// DISPLAY ONLY IF COMMENT AUTH FOR BASETASKID, OR MOD AUTH FOR NO BASETASKID
//     if (!pnSecAuthAction(0, 'tasks::task', '$task[modname]:$task[objectid]:$task[basetaskid]', ACCESS_ADD)) {
//         pnSessionSetVar('errormsg', pnGetStatusMsg() . '<br>' . _TASKS_NOAUTH);
//         pnRedirect(pnModURL('tasks','user','view'));
//         return;
//     }

    $statusoptions = xarModAPIFunc('tasks','user','getstatusoptions');
    $data['statusoptions']=$statusoptions;
    $prioritydropdown =xarModAPIFunc('tasks','user','getpriorities');
    $data['prioritydropdown']=$prioritydropdown;

    $data['feedback'] = '';//xarGetStatusMsg(); // Legacy

    $data['parentid']= (empty($parentid))? 0: $parentid;
    $data['modname']= (empty($module)) ? '' : $module;
    $data['objectid']= (empty($objectid))? 0: $objectid;

    $data['submitbutton']=xarML('Add task');
    return $data;
/*
// EXTRANEOUS
    $sendmailoptions = array();
    $sendmailoptions[] = array('id'=>0,'name'=>'Please choose an email option');
    $sendmailoptions[] = array('id'=>1,'name'=>"any changes");
    $sendmailoptions[] = array('id'=>2,'name'=>"major changes");
    $sendmailoptions[] = array('id'=>3,'name'=>"weekly summaries");
    $sendmailoptions[] = array('id'=>4,'name'=>"Do NOT send email");
    $data['sendmailoptions'] = $sendmailoptions;
    $data['sendmails'] = pnVarPrepForDisplay(xarML('Email Group'));
    for($x=0;$x<=9;$x++) {
        $data['importantdaysdropdown'][] = array('id' => $x, 'name' => $x);
    }
    $data['importantdays'] = pnVarPrepForDisplay(xarML('Important Days'));
    for($x=0;$x<=9;$x++) {
        $data['criticaldaysdropdown'][] = array('id' => $x, 'name' => $x);
    }
*/
}

?>