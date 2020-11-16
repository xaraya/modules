<?php
/**
 * Translations Module
 *
 * @package modules
 * @subpackage translations module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/77.html
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

function translations_admin_update_release_locale()
{
    // Security Check
    if (!xarSecurityCheck('AdminTranslations')) {
        return;
    }

    if (!xarVarFetch('locale', 'str:1:', $locale)) {
        return;
    }

    if (!xarVarFetch('dnType', 'int', $dnType)) {
        return;
    }
    if (!xarVarFetch('dnName', 'str:1:', $dnName)) {
        return;
    }
    if (!xarVarFetch('extid', 'int', $extid)) {
        return;
    }
            
    translations_release_locale($locale);
    xarController::redirect(xarModURL('translations', 'admin', 'generate_trans_info', array(
        'dnType' => $dnType,
        'dnName' => $dnName,
        'extid'  => $extid)));
    return true;
}
