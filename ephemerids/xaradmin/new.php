<?php
/**
 * Default
 */
function ephemerids_admin_new()
{
    // Security Check
    if(!xarSecurityCheck('AddEphemerids')) return;
    // TODO: figure out how to get a list of *available* languages
    // if (xarMLSGetMode() != XARMLS_BOXED_MULTI_LANGUAGE_MODE) {
    $locales = array();
    $lang_count = 1;
    if (xarMLSGetMode() != XARMLS_SINGLE_LANGUAGE_MODE) {
        $current_locale = xarMLSGetCurrentLocale();
        $site_locales = xarMLSListSiteLocales();
        asort($site_locales);
        $lang_count = count($site_locales);
        if ($lang_count > 1) {
            foreach ($site_locales as $locale) {
                $locale_data =& xarMLSLoadLocaleData($locale);
                $selected = ($current_locale == $locale);
                $locales[] = array(
                    'locale'   => $locale,
                    'country'  => $locale_data['/country/display'],
                    'name'     => $locale_data['/language/display'],
                    'selected' => $selected
                );
            }
        }
    }

    $data['locales'] = $locales;
    $data['lang_count'] = $lang_count;  
   

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>