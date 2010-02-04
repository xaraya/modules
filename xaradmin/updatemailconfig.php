<?php
/**
 * Sharecontent Module
 *
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage sharecontent Module
 * @link http://xaraya.com/index.php/release/894.html
 * @author Andrea Moro
 */
/**
 * Update configuration
 */
function sharecontent_admin_updatemailconfig()
{
    // Get parameters
    xarVarFetch('enablemail', 'checkbox', $enablemail, false, XARVAR_NOT_REQUIRED);
    xarVarFetch('maxemails', 'int:0:128', $maxemails, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('htmlmail', 'checkbox', $htmlmail,  false,XARVAR_NOT_REQUIRED);
    xarVarFetch('bcc', 'str', $bcc,'',XARVAR_NOT_REQUIRED);

    if (!xarSecConfirmAuthKey()) return;
    // Security Check
    if (!xarSecurityCheck('AdminSharecontent')) return;

	if (isset($enablemail)) xarModVars::set('sharecontent','enablemail',$enablemail);
	if (isset($maxemails)) xarModVars::set('sharecontent','maxemails',$maxemails);
	if (isset($htmlmail)) xarModVars::set('sharecontent','htmlmail',$htmlmail);
	if (isset($bcc)) xarModVars::set('sharecontent','bcc',$bcc);

    xarResponse::redirect(xarModURL('sharecontent', 'admin', 'mailconfig'));

    return true;
}

?>
