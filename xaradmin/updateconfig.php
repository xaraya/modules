<?php
/**
 * File: $Id:
 *
 * Update configuration parameters of the module with information passed back by the modification form
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf
 */
function bible_admin_updateconfig()
{
    // security check
    if (!xarSecConfirmAuthKey()) return;

    // get HTTP vars
    if (!xarVarFetch('admin_textsperpage', 'int', $admin_textsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('user_searchversesperpage', 'int', $user_searchversesperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('user_lookupversesperpage', 'int', $user_lookupversesperpage, 20, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('user_wordsperpage', 'int', $user_wordsperpage, 40, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('textdir', 'str:1:', $textdir,
        xarPreCoreGetVarDirPath().'/bible', XARVAR_NOT_REQUIRED)
    ) return;
    if (!xarVarFetch('supportshorturls', 'checkbox', $supportshorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('usemodulealias', 'checkbox', $usemodulealias, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname', 'str:1:', $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('altdb', 'checkbox', $altdb, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('altdbtype', 'str:1:', $altdbtype, 'mysql', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('altdbhost', 'str:1:', $altdbhost, 'localhost', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('altdbname', 'str:1:', $altdbname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('altdbuname', 'str:1:', $altdbuname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('altdbpass', 'str:1:', $altdbpass, '', XARVAR_NOT_REQUIRED)) return;

    // validate and clean up module alias
    $aliasname = trim($aliasname);
    $aliasname = str_replace(' ', '_', $aliasname);
    $currentalias = xarModGetVar('bible', 'aliasname');
    if ($usemodulealias && $aliasname) {
        if (!xarModSetAlias($aliasname, 'bible')) return;
    } elseif ($currentalias) {
        xarModDelAlias($currentalias, 'bible');
    }

    // Update module variables.
    xarModSetVar('bible', 'admin_textsperpage', $admin_textsperpage);
    xarModSetVar('bible', 'user_searchversesperpage', $user_searchversesperpage);
    xarModSetVar('bible', 'user_lookupversesperpage', $user_lookupversesperpage);
    xarModSetVar('bible', 'user_wordsperpage', $user_wordsperpage);
    xarModSetVar('bible', 'admin_textsperpage', $admin_textsperpage);
    xarModSetVar('bible', 'textdir', preg_replace("/\/\$/", '', $textdir));
    xarModSetVar('bible', 'SupportShortURLs', $supportshorturls);
    xarModSetVar('bible', 'useModuleAlias', $usemodulealias);
    xarModSetVar('bible', 'aliasname', $aliasname);
    xarModSetVar('bible', 'altdb', $altdb);
    xarModSetVar('bible', 'altdbtype', $altdbtype);
    xarModSetVar('bible', 'altdbhost', $altdbhost);
    xarModSetVar('bible', 'altdbname', $altdbname);
    xarModSetVar('bible', 'altdbuname', $altdbuname);
    xarModSetVar('bible', 'altdbpass', $altdbpass);

    // call updateconfig hooks
    xarModCallHooks('module','updateconfig', 'bible', array('module' => 'bible'));

    // set session var and redirect
    xarSessionSetVar('statusmsg', xarML('Configuration successfully updated!'));
    xarResponseRedirect(xarModURL('bible', 'admin', 'modifyconfig'));

    return true;
}

?>
