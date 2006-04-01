<?php
/**
* Update module configuration
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Update module configuration
*/
function ebulletin_admin_updateconfig()
{
    // security check
    if (!xarSecConfirmAuthKey()) return;

    // get HTTP vars
    if (!xarVarFetch('admin_issuesperpage', 'int:0', $admin_issuesperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('admin_subsperpage', 'int:0', $admin_subsperpage, 40, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('supportshorturls', 'checkbox', $supportshorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('usemodulealias', 'checkbox', $usemodulealias, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname', 'str:1:', $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('template_dir', 'str:1', $template_dir,
        xarPreCoreGetVarDirPath().'/ebulletin', XARVAR_NOT_REQUIRED)
    ) return;
    if (!xarVarFetch('theme', 'str:0', $theme, xarTplGetThemeName(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('issuenumsago', 'int', $issuenumsago, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('issueunitsago', 'str:0', $issueunitsago, 'week', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('issuestartsign', 'enum:before:after', $issuestartsign, 'before', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('issuenumsfromnow', 'int', $issuenumsfromnow, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('issueunitsfromnow', 'str:0', $issueunitsfromnow, 'days', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('issueendsign', 'enum:before:after', $issueendsign, 'after', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('msglimit', 'int:0:', $msglimit, 'msglimit', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('msgunit', 'enum:minute:hour:day:week:month', $msgunit, 'msgunit', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('requirevalidation', 'checkbox', $requirevalidation, false, XARVAR_NOT_REQUIRED)) return;

    // validate and clean up template dir
    $template_dir = trim($template_dir);
    if (empty($template_dir) || !is_dir($template_dir) || !is_readable($template_dir)) {
        $msg = xarML('Invalid template directory #(1).  Make sure it exists and is readable by the web server.', $template_dir);
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // validate and clean up module alias
    $aliasname = trim($aliasname);
    $aliasname = str_replace(' ', '_', $aliasname);
    $currentalias = xarModGetVar('ebulletin', 'aliasname');
    if ($usemodulealias && $aliasname) {
        if (!xarModSetAlias($aliasname, 'ebulletin')) return;
    } elseif ($currentalias) {
        xarModDelAlias($currentalias, 'ebulletin');
    }

    // save module vars
    xarModSetVar('ebulletin', 'admin_issuesperpage', $admin_issuesperpage);
    xarModSetVar('ebulletin', 'admin_subsperpage', $admin_subsperpage);
    xarModSetVar('ebulletin', 'SupportShortURLs', $supportshorturls);
    xarModSetVar('ebulletin', 'useModuleAlias', $usemodulealias);
    xarModSetVar('ebulletin', 'aliasname', $aliasname);
    xarModSetVar('ebulletin', 'template_dir', $template_dir);
    xarModSetVar('ebulletin', 'theme', $theme);
    xarModSetVar('ebulletin', 'issuenumsago', $issuenumsago);
    xarModSetVar('ebulletin', 'issueunitsago', $issueunitsago);
    xarModSetVar('ebulletin', 'issuestartsign', $issuestartsign);
    xarModSetVar('ebulletin', 'issuenumsfromnow', $issuenumsfromnow);
    xarModSetVar('ebulletin', 'issueunitsfromnow', $issueunitsfromnow);
    xarModSetVar('ebulletin', 'issueendsign', $issueendsign);
    xarModSetVar('ebulletin', 'msglimit', $msglimit);
    xarModSetVar('ebulletin', 'msgunit', $msgunit);
    xarModSetVar('ebulletin', 'requirevalidation', $requirevalidation);

    // call updateconfig hooks
    xarModCallHooks('module', 'updateconfig', 'ebulletin', array('module' => 'ebulletin'));

    // set session var and redirect to modifyconfig page
    xarSessionSetVar('statusmsg', xarML('Configuration successfully updated!'));
    xarResponseRedirect(xarModURL('ebulletin', 'admin', 'modifyconfig'));

    // success
    return true;
}

?>
