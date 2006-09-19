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
function xproject_pages_new()
{
    if (!xarVarFetch('projectid',     'id',     $projectid,     $projectid,     XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('inline', 'int', $inline, $inline, XARVAR_NOT_REQUIRED)) return;

    if (!xarModAPILoad('xproject', 'user')) return;

    $data = xarModAPIFunc('xproject','admin','menu');

    if (!xarSecurityCheck('AddXProject')) {
        return;
    }

    $projectinfo = xarModAPIFunc('xproject',
                          'user',
                          'get',
                          array('projectid' => $projectid));

    $pagelist = xarModAPIFunc('xproject',
                         'pages',
                         'getall',
                         array('projectid' => $projectid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $data['authid'] = xarSecGenAuthKey();
    $data['projectid'] = $projectid;
    $data['inline'] = $inline;
    $data['projectinfo'] = $projectinfo;
    $data['pagelist'] = $pagelist;

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Create Feature'));

    return $data;
}

?>