<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_admin_create($args)
{
    list($project_name,
        $reference,
        $private,
        $description,
        $clientid,
        $ownerid,
        $status,
        $priority,
        $importance,
        $projecttype,
        $date_approved,
        $planned_start_date,
        $planned_end_date,
        $actual_start_date,
        $actual_end_date,
        $hours_planned,
        $hours_spent,
        $hours_remaining,
        $associated_sites) =	xarVarCleanFromInput('project_name',
                                            'reference',
                                            'private',
                                            'description',
                                            'clientid',
                                            'ownerid',
                                            'status',
                                            'priority',
                                            'importance',
                                            'projecttype',
                                            'date_approved',
                                            'planned_start_date',
                                            'planned_end_date',
                                            'actual_start_date',
                                            'actual_end_date',
                                            'hours_planned',
                                            'hours_spent',
                                            'hours_remaining',
                                            'associated_sites');

    extract($args);
    if (!xarSecConfirmAuthKey()) return;

    $projectid = xarModAPIFunc('xproject',
                        'admin',
                        'create',
                        array('project_name' 	=> $project_name,
                            'reference' 	    => $reference,
                            'private'	        => $private,
                            'description'	    => $description,
                            'clientid'	        => $clientid,
                            'ownerid'	        => $ownerid,
                            'status'	        => $status,
                            'priority'		    => $priority,
                            'importance'		=> $importance,
                            'projecttype'	    => $projecttype,
                            'date_approved'	    => $date_approved,
                            'planned_start_date'=> $planned_start_date,
                            'planned_end_date'	=> $planned_end_date,
                            'actual_start_date' => $actual_start_date,
                            'actual_end_date'	=> $actual_end_date,
                            'hours_planned'     => $hours_planned,
                            'hours_spent'		=> $hours_spent,
                            'hours_remaining'	=> $hours_remaining,
                            'associated_sites'	=> $associated_sites));


    if (!isset($projectid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('PROJECTCREATED'));

    xarResponseRedirect(xarModURL('xproject', 'admin', 'view'));

    return true;
}

?>