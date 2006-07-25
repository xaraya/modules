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
    list($parentid,
         $projectid,
         $task_name,
         $status,
         $priority,
         $importance,
         $description,
         $private,
         $creator,
         $owner,
         $assigner,
         $groupid,
         $date_created,
         $date_approved,
         $date_changed,
         $date_start_planned,
         $date_start_actual,
         $date_end_planned,
         $date_end_actual,
         $hours_planned,
         $hours_spent,
         $hours_remaining,
         $returnurl) =	xarVarCleanFromInput('parentid',
                                            'projectid',
                                            'task_name',
                                            'status',
                                            'priority',
                                            'importance',
                                            'description',
                                            'private',
                                            'creator',
                                            'owner',
                                            'assigner',
                                            'groupid',
                                            'date_created',
                                            'date_approved',
                                            'date_changed',
                                            'date_start_planned',
                                            'date_start_actual',
                                            'date_end_planned',
                                            'date_end_actual',
                                            'hours_planned',
                                            'hours_spent',
                                            'hours_remaining',
                                            'returnurl');

    extract($args);
    if (!xarSecConfirmAuthKey()) return;
    
    if(!$returnurl) $returnurl = $_SERVER['HTTP_REFERER'];
    if(!$returnurl) $returnurl = xarModURL('xtasks', 'admin', 'view');

    $taskid = xarModAPIFunc('xtasks',
                        'admin',
                        'create',
                        array('parentid'        => $parentid,
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

    xarResponseRedirect($returnurl);

    return true;
}

?>
