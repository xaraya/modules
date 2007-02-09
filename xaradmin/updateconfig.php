<?php
/**
* update configuration
*
* @package unassigned
* @copyright (C) 2002-2007 The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage highlight
* @link http://xaraya.com/index.php/release/559.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * update configuration
 */
function highlight_admin_updateconfig()
{
    // security checks
    if (!xarSecConfirmAuthKey()) return;
    if (!xarSecurityCheck('AdminHighlight')) return;

    // get HTTP vars
    if (!xarVarFetch('string', 'str:1:', $string, 'highlight', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('supportshorturls', 'checkbox', $supportshorturls, false, XARVAR_NOT_REQUIRED)) return;

    // validate and clean up template dir
    $string = trim($string);
    if (empty($string) || !is_string($string) || preg_match("/\s/", $string)) {
        $msg = xarML('Invalid highlight attribute "#(1)".  Must be a string of non-zero length, with no spaces.', $string);
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // save vars
    xarModSetVar('highlight', 'string', $string);
    xarModSetVar('highlight', 'SupportShortURLs', $supportshorturls);

    // set status message and redirect to modifyconfig page
    xarSessionSetVar('statusmsg', xarML('Configuration saved!'));
    xarResponseRedirect(xarModURL('highlight', 'admin', 'modifyconfig'));

    // success
    return true;
}
?>