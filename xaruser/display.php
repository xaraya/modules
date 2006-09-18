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
 * @author St.Ego
 */
function xproject_user_display($args)
{
    extract($args);
    if (!xarVarFetch('projectid', 'id', $projectid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $projectid = $objectid;
    }

    if (!xarModAPILoad('xproject', 'features')) return;

    $data = xarModAPIFunc('xproject','user','menu');
    $data['projectid'] = $projectid;
    $data['status'] = '';

    $data['projects_objectid'] = xarModGetVar('xproject', 'projects_objectid');

    $project = xarModAPIFunc('xproject',
                          'user',
                          'get',
                          array('projectid' => $projectid));

    if (!isset($project)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'project', 'user', 'display', 'xproject');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    list($project['project_name']) = xarModCallHooks('item',
                                         'transform',
                                         $project['projectid'],
                                         array($project['project_name']));

    $data['project_name'] = $project['project_name'];
    $data['project_link'] = xarModURL('xproject',
                                    'admin',
                                    'display',
                                    array('projectid' => $project['projectid']));

    $data['description'] = $project['description'];
    $data['item'] = $project;

    $features = xarModAPIFunc('xproject',
                          'features',
                          'getall',
                          array('projectid' => $projectid));

    if (!isset($features)) return;

    $data['features'] = $features;

    $hooks = xarModCallHooks('item',
                             'display',
                             $projectid,
                             xarModURL('xproject',
                                       'user',
                                       'display',
                                       array('projectid' => $projectid)));
    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }

    return $data;
}
?>