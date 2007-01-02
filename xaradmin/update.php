<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_admin_update($args)
{
    extract($args);
    
    if (!xarVarFetch('projectid', 'id', $projectid)) return;
    if (!xarVarFetch('reference', 'str::', $reference, $reference, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('project_name', 'str:1:', $project_name, $project_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('private', 'str:1:', $private, $private, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cached_private', 'str:1:', $cached_private, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('summary', 'str::', $summary, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('description', 'str::', $description, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('clientid', 'id', $clientid, $clientid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('ownerid', 'id', $ownerid, $ownerid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('memberid', 'id', $memberid, $memberid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projectrole', 'str::', $projectrole, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str::', $status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cached_status', 'str::', $cached_status, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('priority', 'int:1:', $priority, $priority, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cached_priority', 'int:1:', $cached_priority, $priority, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('importance', 'str::', $importance, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cached_importance', 'str::', $cached_importance, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('projecttype', 'str::', $projecttype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('haspages', 'checkbox::', $haspages, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('thumbnail', 'str::', $thumbnail, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('previewimage', 'str::', $previewimage, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('previewurl', 'str::', $previewurl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('date_approved', 'str::', $date_approved, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cached_date_approved', 'str::', $cached_date_approved, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('planned_start_date', 'str::', $planned_start_date, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('planned_end_date', 'str::', $planned_end_date, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cached_planned_end_date', 'str::', $cached_planned_end_date, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('actual_start_date', 'str::', $actual_start_date, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('actual_end_date', 'str::', $actual_end_date, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cached_actual_end_date', 'str::', $cached_actual_end_date, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours_planned', 'float::', $hours_planned, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cached_hours_planned', 'float::', $cached_hours_planned, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours_spent', 'float::', $hours_spent, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cached_hours_spent', 'float::', $cached_hours_spent, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hours_remaining', 'float::', $hours_remaining, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cached_hours_remaining', 'float::', $cached_hours_remaining, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('estimate', 'float::', $estimate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('probability', 'int::', $probability, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('budget', 'float::', $budget, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('associated_sites', 'array::', $associated_sites, $associated_sites, XARVAR_NOT_REQUIRED)) return;

    if(!xarModLoad('addressbook', 'user')) return;

    if(empty($returnurl)) $returnurl = xarModURL('xproject', 'admin', 'view');

    if (!xarSecConfirmAuthKey()) return;

    $projectinfo = xarModAPIFunc('xproject',
                            'user',
                            'get',
                            array('projectid' => $projectid));
                            
    if($projectinfo['private'] != $cached_private) {
        // field has changed elsewhere
        if($cached_private != $private) {
            // but has changed here as well, allow update as-is
        } else {
            // but was changed to different value than current, update to stored projectinfo
            $private = $projectinfo['private'];
        }
    }
                            
    if($projectinfo['status'] != $cached_status) {
        // field has changed elsewhere
        if($cached_status != $status) {
            // but has changed here as well, allow update as-is
        } else {
            // but was changed to different value than current, update to stored projectinfo
            $status = $projectinfo['status'];
        }
    }
                            
    if($projectinfo['priority'] != $cached_priority) {
        // field has changed elsewhere
        if($cached_priority != $priority) {
            // but has changed here as well, allow update as-is
        } else {
            // but was changed to different value than current, update to stored projectinfo
            $priority = $projectinfo['priority'];
        }
    }
                            
    if($projectinfo['importance'] != $cached_importance) {
        // field has changed elsewhere
        if($cached_importance != $importance) {
            // but has changed here as well, allow update as-is
        } else {
            // but was changed to different value than current, update to stored projectinfo
            $importance = $projectinfo['importance'];
        }
    }
                            
    if($projectinfo['date_approved'] != $cached_date_approved) {
        // field has changed elsewhere
        if($cached_date_approved != $date_approved) {
            // but has changed here as well, allow update as-is
        } else {
            // but was changed to different value than current, update to stored projectinfo
            $date_approved = $projectinfo['date_approved'];
        }
    }
                            
    if($projectinfo['planned_end_date'] != $cached_planned_end_date) {
        // field has changed elsewhere
        if($cached_planned_end_date != $planned_end_date) {
            // but has changed here as well, allow update as-is
        } else {
            // but was changed to different value than current, update to stored projectinfo
            $planned_end_date = $projectinfo['planned_end_date'];
        }
    }
                            
    if($projectinfo['actual_end_date'] != $cached_actual_end_date) {
        // field has changed elsewhere
        if($cached_actual_end_date != $actual_end_date) {
            // but has changed here as well, allow update as-is
        } else {
            // but was changed to different value than current, update to stored projectinfo
            $actual_end_date = $projectinfo['actual_end_date'];
        }
    }
                            
    if($projectinfo['hours_planned'] != $cached_hours_planned) {
        // field has changed elsewhere
        if($cached_hours_planned != $hours_planned) {
            // but has changed here as well, allow update as-is
        } else {
            // but was changed to different value than current, update to stored projectinfo
            $hours_planned = $projectinfo['hours_planned'];
        }
    }
                            
    if($projectinfo['hours_spent'] != $cached_hours_spent) {
        // field has changed elsewhere
        if($cached_hours_spent != $hours_spent) {
            // but has changed here as well, allow update as-is
        } else {
            // but was changed to different value than current, update to stored projectinfo
            $hours_spent = $projectinfo['hours_spent'];
        }
    }
                            
    if($projectinfo['hours_remaining'] != $cached_hours_remaining) {
        // field has changed elsewhere
        if($cached_hours_remaining != $hours_remaining) {
            // but has changed here as well, allow update as-is
        } else {
            // but was changed to different value than current, update to stored projectinfo
            $hours_remaining = $projectinfo['hours_remaining'];
        }
    }

    if(!xarModAPIFunc('xproject',
                    'admin',
                    'update',
                    array('projectid'       => $projectid,
                        'project_name'      => $project_name,
                        'reference'         => $reference,
                        'private'           => $private,
                        'summary'           => $summary,
                        'description'       => $description,
                        'clientid'          => $clientid,
                        'ownerid'           => $ownerid,
                        'status'            => $status,
                        'priority'          => $priority,
                        'importance'        => $importance,
                        'projecttype'       => $projecttype,
                        'thumbnail'         => $thumbnail,
                        'previewimage'      => $previewimage,
                        'previewurl'        => $previewurl,
                        'date_approved'     => $date_approved,
                        'planned_start_date'=> $planned_start_date,
                        'planned_end_date'  => $planned_end_date,
                        'actual_start_date' => $actual_start_date,
                        'actual_end_date'   => $actual_end_date,
                        'hours_planned'     => $hours_planned,
                        'hours_spent'       => $hours_spent,
                        'hours_remaining'   => $hours_remaining,
                        'estimate'          => $estimate,
                        'probability'       => $probability,
                        'budget'            => $budget,
                        'associated_sites'  => $associated_sites))) {
        return;
    }

    xarSessionSetVar('statusmsg', xarML('Project Updated'));

    if(is_numeric($memberid) && $memberid > 0) {
        if(!xarModAPIFunc('xproject',
                        'team',
                        'create',
                        array('projectid' => $projectid,
                            'memberid' => $memberid,
                            'projectrole' => $projectrole))) {
            // team member added
            xarSessionSetVar('statusmsg', xarML('Team Member added to Project'));
        }
    }

    xarModCallHooks('item', 'update', $projectid, $args);

    xarResponseRedirect($returnurl);

    return true;
}

?>