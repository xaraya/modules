<?php
/**
 * Status report for the current translation
 *
 * @package modules
 * @subpackage translations
 * @copyright (C) 2004 Marcel van der Boom
 * @link http://www.xaraya.com
 * 
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function &count_entries(&$entries)
{
    $counts['numEntries']         = 0;
    $counts['numEmptyEntries']    = 0;
    $counts['numKeyEntries']      = 0;
    $counts['numEmptyKeyEntries'] = 0;
    foreach($entries as $entry) {
        $counts['numEntries']         += $entry['numEntries'];
        $counts['numEmptyEntries']    += $entry['numEmptyEntries'];
        $counts['numKeyEntries']      += $entry['numKeyEntries'];
        $counts['numEmptyKeyEntries'] += $entry['numEmptyKeyEntries']; 
    }
    return $counts;
}

function translations_admin_show_status()
{
    // Security Check
    if(!xarSecurityCheck('ReadTranslations')) return;
   
    $data = array();

    // core
    $tmp = translations_create_trabar(XARMLS_DNTYPE_CORE, 'xaraya', 0, 'core','core');
    $coreentries =& count_entries($tmp['entrydata']);
    unset($tmp);

    // modules
    if (!($mods = xarModAPIFunc('modules','admin','getlist', array('filter' => array('State' => XARMOD_STATE_ANY))))) return;
    $modentries = array();
    $mod_totalentries = 0; $mod_untranslated = 0; $mod_keytotalentries = 0; $mod_keyuntranslated =0;
    foreach($mods as $mod) {
        $modname = $mod['name'];
        $modid = $mod['regid'];

        $args['interface'] = 'ReferencesBackend';
        $args['locale'] = translations_working_locale();
        $testbackend = xarModAPIFunc('translations','admin','create_backend_instance',$args);
        if (isset($testbackend) && $testbackend->bindDomain(XARMLS_DNTYPE_MODULE, $modname)) {
            $tmp =  translations_create_trabar(XARMLS_DNTYPE_MODULE, $modname, $modid, 'modules',$modname);
            $modentries[$modname] =& count_entries($tmp['entrydata']);
            unset($tmp);
            $mod_totalentries += $modentries[$modname]['numEntries'];
            $mod_untranslated += $modentries[$modname]['numEmptyEntries'];
            $mod_keytotalentries += $modentries[$modname]['numKeyEntries'];
            $mod_keyuntranslated += $modentries[$modname]['numEmptyKeyEntries'];
        } else {
            $modentries[$modname]['numEntries'] = -1;
            $modentries[$modname]['numEmptyEntries'] = -1;
            $modentries[$modname]['numKeyEntries'] = -1;
            $modentries[$modname]['numEmptyKeyEntries'] = -1;
        }
        unset($testbackend);
    }

    // themes
    if (!($themes = xarModAPIFunc('themes','admin','getthemelist', array('filter' => array('State' => XARTHEME_STATE_ANY))))) return;

    $themeentries = array();
    $theme_totalentries = 0; $theme_untranslated =0; $theme_keytotalentries = 0; $theme_keyuntranslated = 0;
    foreach($themes as $theme) {
        $themename = $theme['osdirectory'];
        $themeid = $theme['regid'];

        $args['interface'] = 'ReferencesBackend';
        $args['locale'] = translations_working_locale();
        $testbackend = xarModAPIFunc('translations','admin','create_backend_instance',$args);
        if (isset($testbackend) && $testbackend->bindDomain(XARMLS_DNTYPE_THEME, $themename)) {
            $tmp =  translations_create_trabar(XARMLS_DNTYPE_THEME, $themename, $themeid, 'themes', $themename);
            $themeentries[$themename] =& count_entries($tmp['entrydata']);
            unset($tmp);
            $theme_totalentries += $themeentries[$themename]['numEntries'];
            $theme_untranslated += $themeentries[$themename]['numEmptyEntries'];
            $theme_keytotalentries += $themeentries[$themename]['numKeyEntries'];
            $theme_keyuntranslated += $themeentries[$themename]['numEmptyKeyEntries'];
        } else {
            $themeentries[$themename]['numEntries'] = -1;
            $themeentries[$themename]['numEmptyEntries'] = -1;
            $themeentries[$themename]['numKeyEntries'] = -1;
            $themeentries[$themename]['numEmptyKeyEntries'] = -1;
        }
        unset($testbackend);
    }

    $data['coreentries'] = $coreentries;
    $data['modentries']   = $modentries;
    $data['mod_totalentries'] = $mod_totalentries;
    $data['mod_untranslated'] = $mod_untranslated;
    $data['mod_keytotalentries'] = $mod_keytotalentries;
    $data['mod_keyuntranslated'] = $mod_keyuntranslated;
    $data['theme_totalentries'] = $theme_totalentries;
    $data['theme_untranslated'] = $theme_untranslated;
    $data['theme_keytotalentries'] = $theme_keytotalentries;
    $data['theme_keyuntranslated'] = $theme_keyuntranslated;
    $data['themeentries'] = $themeentries;

    return $data;
}

?>