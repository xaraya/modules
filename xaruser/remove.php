<?php
/**
 * Reminders Module
 *
 * @package modules
 * @subpackage reminders
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Suppress the reminders of an entry
 *
 */
function reminders_user_remove()
{
    if (!xarSecurityCheck('ReadReminders')) return;

    if (!xarVarFetch('code',    'str',      $data['code'],       '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'checkbox', $data['confirm'],    false, XARVAR_NOT_REQUIRED)) return;

    $data['debugmode'] = xarModVars::get('reminders', 'debugmode');
	xarTpl::setPageTemplateName('user_full');
	return $data;
}
?>
