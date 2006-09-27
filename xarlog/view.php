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
function xproject_log_view($args)
{
    extract($args);

    $data = xarModAPIFunc('xproject','user','menu');

    $xprojects = array();

    if (!xarSecurityCheck('ModerateXProject')) {
        return;
    }

    $projectinfo = xarModAPIFunc('xproject',
                          'user',
                          'getall',
                          array('projectid' => $projectid));

    if (!isset($projectinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $xproject_logs = xarModAPIFunc('xproject',
                          'user',
                          'getall',
                          array('projectid' => $projectid));//TODO: numitems

    if (!isset($xproject_logs) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $displaytitle = xarModGetVar('xproject', 'displaytitle');
    $data['displaytitle'] = $displaytitle ? $displaytitle : xarML("xProject - Active Public Projects");
    $data['projectinfo'] = $projectinfo;
    $data['xproject_logs'] = $xproject_logs;
    $data['pager'] = '';
    return $data;
}

?>
