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
function xproject_user_view($args)
{
    extract($args);
    if (!xarVarFetch('startnum',   'int:1:', $startnum,   1, XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('xproject','user','menu');

    $data['items'] = array();

    if (!xarSecurityCheck('ViewXProject')) {
        return;
    }

    $xprojects = xarModAPIFunc('xproject',
                          'user',
                          'getall',
                          array('startnum' => $startnum,
                                'numitems' => 10));//TODO: numitems

    if (!isset($xprojects) && xarExceptionMajor() != XAR_NO_EXCEPTION) return;

    for ($i = 0; $i < count($xprojects); $i++) {
        $project = $xprojects[$i];
//		if (xarSecAuthAction(0, 'xproject::Projects', "$project[name]::$project[projectid]", ACCESS_READ)) {
        if (xarSecurityCheck('ReadXProject', 0, 'Item', "$project[name]:All:$project[projectid]")) {//TODO: security
            $xprojects[$i]['link'] = xarModURL('xproject',
                                               'user',
                                               'display',
                                               array('projectid' => $project['projectid']));
        }
        //if (xarSecAuthAction(0, 'xproject::Projects', "$project[name]::$project[projectid]", ACCESS_EDIT)) {
        if (xarSecurityCheck('EditXProject', 0, 'Item', "$project[name]:All:$project[projectid]")) {//TODO: security
            $xprojects[$i]['editurl'] = xarModURL('xproject',
                                               'admin',
                                               'modify',
                                               array('projectid' => $project['projectid']));
        } else {
            $xprojects[$i]['editurl'] = '';
        }
        if (xarSecurityCheck('DeleteXProject', 0, 'Item', "$project[name]:All:$project[projectid]")) {
            $xprojects[$i]['deleteurl'] = xarModURL('xproject',
                                               'admin',
                                               'delete',
                                               array('projectid' => $project['projectid']));
        } else {
            $xprojects[$i]['deleteurl'] = '';
        }
    }

    $data['xprojects'] = $xprojects;
    $data['pager'] = '';
    return $data;
}

?>