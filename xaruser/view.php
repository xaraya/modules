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
function xproject_user_view($args)
{
    extract($args);

    $data = xarModAPIFunc('xproject','user','menu');

    $xprojects = array();

    if (!xarSecurityCheck('ViewXProject')) {
        return;
    }

    $xprojects = xarModAPIFunc('xproject',
                          'user',
                          'getall',
                          array('private' => "public",
                                'status' => "Active",
                                'sortby' => "planned_end_date",
                                'numitems' => xarModGetVar('xproject', 'itemsperpage')));//TODO: numitems

    if (!isset($xprojects) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    for ($i = 0; $i < count($xprojects); $i++) {
        $project = $xprojects[$i];
        if (xarSecurityCheck('ReadXProject', 0, 'Item', "$project[project_name]:All:$project[projectid]")) {//TODO: security
            $xprojects[$i]['link'] = xarModURL('xproject',
                                               'admin',
                                               'display',
                                               array('projectid' => $project['projectid']));
        }
        if (xarSecurityCheck('EditXProject', 0, 'Item', "$project[project_name]:All:$project[projectid]")) {//TODO: security
            $xprojects[$i]['editurl'] = xarModURL('xproject',
                                               'admin',
                                               'modify',
                                               array('projectid' => $project['projectid']));
        } else {
            $xprojects[$i]['editurl'] = '';
        }
        if (xarSecurityCheck('DeleteXProject', 0, 'Item', "$project[project_name]:All:$project[projectid]")) {
            $xprojects[$i]['deleteurl'] = xarModURL('xproject',
                                               'admin',
                                               'delete',
                                               array('projectid' => $project['projectid']));
        } else {
            $xprojects[$i]['deleteurl'] = '';
        }
    }

    $displaytitle = xarModGetVar('xproject', 'displaytitle');
    $data['displaytitle'] = $displaytitle ? $displaytitle : xarML("xProject - Active Public Projects");
    $data['projects'] = $xprojects;
    $data['pager'] = '';
    return $data;
}

?>
