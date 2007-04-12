<?php
/**
 * xTasks Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xTasks Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
function xtasks_admin_create($args)
{
    extract($args);
    
    if (!xarVarFetch('showajax',   'str::', $showajax,    NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('modid',   'isset', $modid,    NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('itemtype',  'isset', $itemtype,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('objectid',  'isset', $objectid,   NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('parentid', 'id', $parentid, $parentid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dependentid', 'id', $dependentid, $dependentid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectid', 'id', $projectid, $projectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('task_name', 'str:1:', $task_name, $task_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('private', 'str:1:', $private, $private, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('description', 'html:basic', $description, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('creator', 'id', $creator, $creator, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('owner', 'id', $owner, $owner, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('assigner', 'id', $assigner, $assigner, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('groupid', 'id', $groupid, $groupid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str::', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('priority', 'int:1:', $priority, $priority, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('importance', 'str::', $importance, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_created', 'str::', $date_created, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_approved', 'str::', $date_approved, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_changed', 'str::', $date_changed, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_start_planned', 'str::', $date_start_planned, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_start_actual', 'str::', $date_start_actual, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_end_planned', 'str::', $date_end_planned, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_end_actual', 'str::', $date_end_actual, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours_planned', 'float::', $hours_planned, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours_spent', 'float::', $hours_spent, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours_remaining', 'float::', $hours_remaining, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;
    
//    if(!$returnurl) $returnurl = $_SERVER['HTTP_REFERER'];
    if(!$returnurl) $returnurl = xarServerGetVar('HTTP_REFERER');
    if(!$returnurl) $returnurl = xarModURL('xtasks', 'admin', 'view');

    $taskid = xarModAPIFunc('xtasks',
                        'admin',
                        'create',
                        array('objectid'        => $objectid,
                            'modid'             => $modid,
                            'itemtype'          => $itemtype,
                            'parentid'          => $parentid,
                            'dependentid'       => $dependentid,
                            'projectid'         => $projectid,
                            'task_name'         => $task_name,
                            'status'            => $status,
                            'priority'          => $priority,
                            'importance'        => $importance,
                            'description'       => $description,
                            'private'           => $private,
                            'creator'           => $creator,
                            'owner'             => $owner,
                            'assigner'          => $assigner,
                            'groupid'           => $groupid,
                            'date_created'      => $date_created,
                            'date_approved'     => $date_approved,
                            'date_changed'      => $date_changed,
                            'date_start_planned' => $date_start_planned,
                            'date_start_actual' => $date_start_actual,
                            'date_end_planned'  => $date_end_planned,
                            'date_end_actual'   => $date_end_actual,
                            'hours_planned'     => $hours_planned,
                            'hours_spent'       => $hours_spent,
                            'hours_remaining'   => $hours_remaining));


    if (!isset($taskid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('TASKCREATED'));
//return "showajax: ".$showajax." - ".$returnurl;

    if($showajax) {
        return xarModFunc('xtasks', 'admin', 'workspace', array('modid' => $modid, 'itemtype' => $itemtype));
    } else {
//        xarResponseRedirect($returnurl);
    
        return true;
    } 
}

?>
