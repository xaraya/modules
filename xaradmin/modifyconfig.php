<?php
/**
 * Short description of purpose of file
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


function translations_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'locales', XARVAR_NOT_REQUIRED)) return;

   $localehome = xarServer::getBaseURL().sys::varpath().'/locales';
    if (!file_exists($localehome)) {
        throw new Exception('The locale directory was not found.');
    }
    $dd = opendir($localehome);
    $locales = array();
    while ($filename = readdir($dd)) {
            if (is_dir($localehome . "/" . $filename) && file_exists($localehome . "/" . $filename . "/locale.xml")) {
                $locales[] = $filename;
            }
    }
    closedir($dd);

    $allowedlocales = xarConfigVars::get(null,'Site.MLS.AllowedLocales');
    foreach($locales as $locale) {
        if (in_array($locale, $allowedlocales)) $active = true;
        else $active = false;
        $data['locales'][] = array('name' => $locale, 'active' => $active);
    }

    $data['translationsBackend'] = xarConfigVars::get(null,'Site.MLS.TranslationsBackend');
    $data['releaseBackend'] = xarModVars::get('translations', 'release_backend_type');
    $data['showcontext'] = xarModVars::get('translations', 'showcontext');
    $data['maxreferences'] = xarModVars::get('translations', 'maxreferences');
    $data['maxcodelines'] = xarModVars::get('translations', 'maxcodelines');

    $data['authid'] = xarSecGenAuthKey();
    $data['updatelabel'] = xarML('Update Translations Configuration');
    return $data;

}

?>