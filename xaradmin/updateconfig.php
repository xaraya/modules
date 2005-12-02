<?php
/**
* Update module configuration
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
* Update module configuration
*/
function files_admin_updateconfig()
{
    // security checks
    if (!xarSecurityCheck('AdminFiles', 1)) return;
    if (!xarSecConfirmAuthKey()) return;

    // get HTTP vars
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('archive_dir', 'str:0', $archive_dir, xarPreCoreGetVarDirPath().'/files', XARVAR_NOT_REQUIRED)) return;

    // validate archive dir
    if (empty($archive_dir) || !is_dir($archive_dir) || !is_readable($archive_dir)) {
        $msg = xarML('Invalid archive directory.  Make sure it exists and is readable.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // save module vars
    xarModSetVar('files', 'SupportShortURLs', $shorturls);
    xarModSetVar('files', 'archive_dir', $archive_dir);

    // call updateconfig hooks
    xarModCallHooks('module', 'updateconfig', 'files', array('module' => 'files'));

    // set status and return to modifyconfig page
    xarSessionSetVar('statusmsg', xarML('Configuration saved!'));
    xarResponseRedirect(xarModURL('files', 'admin', 'modifyconfig'));

    // success
    return true;
}

?>
