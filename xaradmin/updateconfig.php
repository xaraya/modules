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
    // security check
    if (!xarSecConfirmAuthKey()) return;

    // get HTTP vars
    if (!xarVarFetch('supportshorturls', 'checkbox', $supportshorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('usemodulealias', 'checkbox', $usemodulealias, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname', 'str:1:', $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('archive_dir', 'str:0', $archive_dir, xarPreCoreGetVarDirPath().'/files', XARVAR_NOT_REQUIRED)) return;

    // validate archive dir
    if (empty($archive_dir) || !is_dir($archive_dir) || !is_readable($archive_dir)) {
        $msg = xarML('Invalid archive directory.  Make sure it exists and is readable.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // validate and clean up module alias
    $aliasname = trim($aliasname);
    $aliasname = str_replace(' ', '_', $aliasname);
    $currentalias = xarModGetVar('files', 'aliasname');
    if ($usemodulealias && $aliasname) {
        if (!xarModSetAlias($aliasname, 'files')) return;
    } elseif ($currentalias) {
        xarModDelAlias($currentalias, 'files');
    }

    // save module vars
    xarModSetVar('files', 'SupportShortURLs', $supportshorturls);
    xarModSetVar('files', 'useModuleAlias', $usemodulealias);
    xarModSetVar('files', 'aliasname', $aliasname);
    xarModSetVar('files', 'archive_dir', $archive_dir);

    // call updateconfig hooks
    xarModCallHooks('module', 'updateconfig', 'files', array('module' => 'files'));

    // set session var and redirect to modifyconfig page
    xarSessionSetVar('statusmsg', xarML('Configuration successfully updated!'));
    xarResponseRedirect(xarModURL('files', 'admin', 'modifyconfig'));

    // success
    return true;
}

?>
