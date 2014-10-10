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
        $data['locales'] = $list;
    } else {
        $data['locales'] = $GLOBALS['xarMLS_allowedLocales'];
    }

    $data['working_locale'] = translations_working_locale();
    $data['dnType'] = XARMLS_DNTYPE_CORE;
    return $data;
}

?>