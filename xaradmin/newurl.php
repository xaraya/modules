<?php
/**
 * Add URL Form
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage window
 * @link http://xaraya.com/index.php/release/3002.html
 * @author Marc Lutolf
 * @author Yassen Yotov (CyberOto)
 */

/**
 * Add URL Form
 *
 * @return array $data template array values
 */
function window_admin_newurl()
{
    if (!xarSecurityCheck('AdminWindow')) return;

	if (!xarVarFetch('reg_user_only', 'int', $data['reg_user_only'], xarModGetVar('window', 'reg_user_only'), XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('open_direct', 'int', $data['open_direct'], xarModGetVar('window', 'open_direct'), XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('use_fixed_title', 'int', $data['use_fixed_title'], xarModGetVar('window', 'use_fixed_title'), XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('auto_resize', 'int', $data['auto_resize'], xarModGetVar('window', 'auto_resize'), XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('status', 'int', $data['status'], 1, XARVAR_NOT_REQUIRED)) return;

    $data['authid'] = xarSecGenAuthKey();
    $data['action'] = xarModURL('window', 'admin', 'addurl');
    $data['window_status'] = 'add';
    $data['urls'] = xarModAPIFunc('window','admin','geturls');
    $data['id'] = '';
    $data['host'] = 'http://';
    $data['alias'] = '';
    $data['vsize'] = xarModGetVar('window', 'vsize');
    $data['hsize'] = xarModGetVar('window', 'hsize');
    $data['lang_action'] = xarML('Add');

    return $data;
}
?>