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
function accessmethods_log_view($args)
{
    extract($args);

    $data = xarModAPIFunc('accessmethods','user','menu');

    $accessmethodss = array();

    if (!xarSecurityCheck('ModerateXProject')) {
        return;
    }

    $projectinfo = xarModAPIFunc('accessmethods',
                          'user',
                          'getall',
                          array('siteid' => $siteid));

    if (!isset($projectinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $accessmethods_logs = xarModAPIFunc('accessmethods',
                          'log',
                          'getall',
                          array('siteid' => $siteid));//TODO: numitems

    if (!isset($accessmethods_logs) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $displaytitle = xarModGetVar('accessmethods', 'displaytitle');
    $data['displaytitle'] = $displaytitle ? $displaytitle : xarML("xProject - Active Public Projects");
    $data['projectinfo'] = $projectinfo;
    $data['accessmethods_logs'] = $accessmethods_logs;
    $data['pager'] = '';
    return $data;
}

?>
