<?php
/**
 * Start translation process
 *
 * @package modules
 * @copyright (C) 2002-2008 The Digital Development Foundation
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


/**
 * Entry point for beginning a translation
 *
 * @access  public
 * @return  array template data
*/
function translations_admin_start()
{
    // Security Check
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (xarConfigVars::get(null,'Site.MLS.TranslationsBackend') == 'xml2php') {
        $locales = $GLOBALS['xarMLS_allowedLocales'];
        foreach ($locales as $locale) {
            $l = xarMLS__parseLocaleString($locale);
            if ($l['charset'] != 'utf-8') continue;
            $list[] = $locale;
        }
        $tplData['locales'] = $list;
    } else {
        $tplData['locales'] = $GLOBALS['xarMLS_allowedLocales'];
    }

    $tplData['working_locale'] = translations_working_locale();
    $tplData['dnType'] = XARMLS_DNTYPE_CORE;

    return $tplData;
}

?>