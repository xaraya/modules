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

    // get module vars
    $supportshorturls    = xarModGetVar('files', 'SupportShortURLs');
    $usemodulealias      = xarModGetVar('files', 'useModuleAlias');
    $aliasname           = xarModGetVar('files', 'aliasname');
    $archive_dir         = xarModGetVar('files', 'archive_dir');

    // set template vars
    $data = array();
    $data['supportshorturls']  = $supportshorturls;
    $data['usemodulealias']    = $usemodulealias;
    $data['aliasname']         = $aliasname;
    $data['archive_dir']       = $archive_dir;

    // add other vars to template
    $data['authid']   = xarSecGenAuthKey();

    // get modifyconfig hooks
    $data['hookoutput'] = xarModCallHooks('module', 'modifyconfig', 'files',
        array('module' => 'files')
    );

    return $data;
}

?>
