<?php
/**
 * Providing Configuration Data for template
 *
 * @package modules
 * @copyright (C) 2003-2009 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


function translations_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'display', XARVAR_NOT_REQUIRED)) return;

    $data['translationsBackend'] = xarConfigGetVar('Site.MLS.TranslationsBackend');
    $data['releaseBackend'] = xarModGetVar('translations', 'release_backend_type');
    $data['showcontext'] = xarModGetVar('translations', 'showcontext');
    $data['maxreferences'] = xarModGetVar('translations', 'maxreferences');
    $data['maxcodelines'] = xarModGetVar('translations', 'maxcodelines');
    $data['confirmskelsgen'] = xarModGetVar('translations', 'confirmskelsgen');

    $data['authid'] = xarSecGenAuthKey();
    $data['updatelabel'] = xarML('Update Translations Configuration');

    return $data;

}

?>