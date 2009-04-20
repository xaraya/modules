<?php
/**
 * Update release locale
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function translations_admin_update_release_locale()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (!xarVarFetch('locale', 'str:1:', $locale)) return;

    if (!xarVarFetch('dnType','int',$dnType)) return;
    if (!xarVarFetch('dnName','str:1:',$dnName)) return;
    if (!xarVarFetch('extid','int',$extid)) return;
            
    translations_release_locale($locale);
    xarResponse::Redirect(xarModURL('translations', 'admin', 'generate_trans_info', array(
        'dnType' => $dnType,
        'dnName' => $dnName,
        'extid'  => $extid)));
}

?>