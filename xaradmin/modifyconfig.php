<?php
/**
* Display GUI for config modification
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage files
* @link http://xaraya.com/index.php/release/554.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Display GUI for config modification
*/
function files_admin_modifyconfig()
{
    // security check
    if (!xarSecurityCheck('AdminFiles')) return;

    // initialize template vars
    $data = xarModAPIFunc('files', 'admin', 'menu');

    $data['authid'] = xarSecGenAuthKey();
    $data['shorturls'] = xarModGetVar('files', 'SupportShortURLs') ? 'checked' : '';
    $data['archive_dir'] = xarModGetVar('files', 'archive_dir');

    // call modifyconfig hooks
    $data['hookoutput'] = xarModCallHooks('module', 'modifyconfig', 'files',
        array('module' => 'files'));

    return $data;
}

?>
