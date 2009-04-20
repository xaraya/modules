<?php
/**
 * Update working locale
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_update_working_locale()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('locale', 'str:1:', $locale)) return;
    translations_working_locale($locale);
    translations_release_locale($locale);
    xarResponse::Redirect(xarModURL('translations', 'admin','start'));
}

?>