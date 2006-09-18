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
function xproject_admin_display($args)
{
    extract($args);
    if (!xarVarFetch('projectid', 'id', $projectid)) return;
    if (!xarVarFetch('mode', 'str', $mode, $mode, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;

    $data['projects_objectid'] = xarModGetVar('xproject', 'projects_objectid');

    if (!xarModAPILoad('xproject', 'user')) return;
    if (!xarModLoad('addressbook', 'user')) return;

    if (!empty($objectid)) {
        $projectid = $objectid;
    }

    $data = xarModAPIFunc('xproject','admin','menu');
    $data['projectid'] = $projectid;
    $data['mode'] = $mode;
    $data['status'] = '';

    $data['mymemberid'] = "0";

    $item = xarModAPIFunc('xproject',
                          'user',
                          'get',
                          array('projectid' => $projectid));

    if (!isset($item)) return;

    if (xarSecurityCheck('EditXProject', 0, 'Item', "$item[project_name]:All:$item[projectid]")) {
        $item['editurl'] = xarModURL('xproject',
                                    'admin',
                                    'modify',
                                    array('projectid' => $item['projectid']));
    } else {
        $item['editurl'] = '';
    }
    if (xarSecurityCheck('DeleteXProject', 0, 'Item', "$item[project_name]:All:$item[projectid]")) {
        $item['deleteurl'] = xarModURL('xproject',
                                        'admin',
                                        'delete',
                                        array('projectid' => $item['projectid']));
    } else {
        $item['deleteurl'] = '';
    }

    list($item['project_name']) = xarModCallHooks('item',
                                         'transform',
                                         $item['projectid'],
                                         array($item['project_name']));

    $teamlist = xarModAPIFunc('xproject',
                            'team',
                            'getall',
                            array('projectid' => $projectid));

    $features = xarModAPIFunc('xproject',
                          'features',
                          'getall',
                          array('projectid' => $projectid));

    if (!isset($features)) return;

    for ($i = 0; $i < count($features); $i++) {
        $feature = $features[$i];
        if (xarSecurityCheck('ReadXProject', 0, 'Item', "$feature[project_name]:All:$feature[projectid]")) {//TODO: security
            $features[$i]['link'] = xarModURL('xproject',
                                               'features',
                                               'display',
                                               array('featureid' => $feature['featureid'],
                                                    'inline' => 1));
        }
        if (xarSecurityCheck('EditXProject', 0, 'Item', "$feature[project_name]:All:$feature[projectid]")) {//TODO: security
            $features[$i]['editurl'] = xarModURL('xproject',
                                               'features',
                                               'modify',
                                               array('featureid' => $feature['featureid'],
                                                    'inline' => 1));
        } else {
            $features[$i]['editurl'] = '';
        }
        if (xarSecurityCheck('DeleteXProject', 0, 'Item', "$feature[project_name]:All:$feature[projectid]")) {
            $features[$i]['deleteurl'] = xarModURL('xproject',
                                               'features',
                                               'delete',
                                               array('featureid' => $feature['featureid'],
                                                    'inline' => 1));
        } else {
            $features[$i]['deleteurl'] = '';
        }
    }

    $data['features_onclick'] = "onClick=\"return loadContent(this.href,'features_form');\"";

    $projectpages = xarModAPIFunc('xproject',
                          'pages',
                          'getall',
                          array('projectid' => $projectid));

    if (!isset($projectpages)) return;

    $data['pages_formclick'] = "onClick=\"return loadContent(this.href,'pages_form');\"";

    $logs = xarModAPIFunc('xproject',
                          'log',
                          'getall',
                          array('projectid' => $projectid));

    if (!isset($logs)) return;

    $data['item'] = $item;
    $data['teamlist'] = $teamlist;
    $data['features'] = $features;
    $data['projectpages'] = $projectpages;
    $data['logs'] = $logs;
    $data['authid'] = xarSecGenAuthKey();
    $data['project_name'] = $item['project_name'];
    $data['description'] = $item['description'];

    $modid = xarModGetIDFromName(xarModGetName());
    $data['modid'] = $modid;
    $data['itemtype'] = 1;
    $data['objectid'] = $projectid;

    $hooks = xarModCallHooks('item',
                             'display',
                             $projectid,
                             array('module'    => 'xproject',
                                   'returnurl' => xarModURL('xproject',
                                                           'admin',
                                                           'display',
                                                           array('projectid' => $projectid))
                                  ),
                            'xproject');

    if (empty($hooks)) {
        $data['hooks'] = array();
    } else {
        $data['hooks'] = $hooks;
    }

    return $data;
}
?>