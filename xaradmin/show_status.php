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

    // Get the modules
    if (!($mods = xarMod::apiFunc('modules','admin','getlist', array('filter' => array('State' => XARMOD_STATE_ANY))))) return;
    $modentries = array();
    $mod_totalentries = 0; $mod_untranslated = 0; $mod_keytotalentries = 0; $mod_keyuntranslated =0;
    foreach($mods as $mod) {
        $modname = $mod['name'];
        $modid = $mod['regid'];

        $args['interface'] = 'ReferencesBackend';
        $args['locale'] = translations_working_locale();
        $testbackend = xarMod::apiFunc('translations','admin','create_backend_instance',$args);
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

    // Get the properties
    xarMod::apiLoad('dynamicdata');
    $tables =& xarDB::getTables();
    sys::import('xaraya.structures.query');
    $q = new Query('SELECT',$tables['dynamic_properties_def']);
    $q->eq('modid', 0);
    $q->run();
    $properties = $q->output();
    
    $propertyentries = array();
    $property_totalentries = 0; $property_untranslated =0; $property_keytotalentries = 0; $property_keyuntranslated = 0;
    foreach($properties as $property) {
        $propertyname = $property['name'];
        $propertyid = $property['id'];

        $args['interface'] = 'ReferencesBackend';
        $args['locale'] = translations_working_locale();
        $testbackend = xarMod::apiFunc('translations','admin','create_backend_instance',$args);
        if (isset($testbackend) && $testbackend->bindDomain(XARMLS_DNTYPE_PROPERTY, $propertyname)) {
            $tmp =  translations_create_trabar(XARMLS_DNTYPE_PROPERTY, $propertyname, $propertyid, 'properties', $propertyname);
            $propertyentries[$propertyname] =& count_entries($tmp['entrydata']);
            unset($tmp);
            $property_totalentries += $propertyentries[$propertyname]['numEntries'];
            $property_untranslated += $propertyentries[$propertyname]['numEmptyEntries'];
            $property_keytotalentries += $propertyentries[$propertyname]['numKeyEntries'];
            $property_keyuntranslated += $propertyentries[$propertyname]['numEmptyKeyEntries'];
        } else {
            $propertyentries[$propertyname]['numEntries'] = -1;
            $propertyentries[$propertyname]['numEmptyEntries'] = -1;
            $propertyentries[$propertyname]['numKeyEntries'] = -1;
            $propertyentries[$propertyname]['numEmptyKeyEntries'] = -1;
        }
        unset($testbackend);
    }

    // Get the blocks
    $blocks = xarMod::apiFunc('blocks','types','getitems',array('module_id' => 0, 'type_state' => xarBlock::TYPE_STATE_ACTIVE));

    $blockentries = array();
    $block_totalentries = 0; $block_untranslated =0; $block_keytotalentries = 0; $block_keyuntranslated = 0;
    foreach($blocks as $block) {
        $blockname = $block['type'];
        $blockid = $block['type_id'];

        $args['interface'] = 'ReferencesBackend';
        $args['locale'] = translations_working_locale();
        $testbackend = xarMod::apiFunc('translations','admin','create_backend_instance',$args);
        if (isset($testbackend) && $testbackend->bindDomain(XARMLS_DNTYPE_BLOCK, $blockname)) {
            $tmp =  translations_create_trabar(XARMLS_DNTYPE_BLOCK, $blockname, $blockid, 'blocks', $blockname);
            $blockentries[$blockname] =& count_entries($tmp['entrydata']);
            unset($tmp);
            $block_totalentries += $blockentries[$blockname]['numEntries'];
            $block_untranslated += $blockentries[$blockname]['numEmptyEntries'];
            $block_keytotalentries += $blockentries[$blockname]['numKeyEntries'];
            $block_keyuntranslated += $blockentries[$blockname]['numEmptyKeyEntries'];
        } else {
            $blockentries[$blockname]['numEntries'] = -1;
            $blockentries[$blockname]['numEmptyEntries'] = -1;
            $blockentries[$blockname]['numKeyEntries'] = -1;
            $blockentries[$blockname]['numEmptyKeyEntries'] = -1;
        }
        unset($testbackend);
    }

    // Get the themes
    if (!($themes = xarMod::apiFunc('themes','admin','getthemelist', array('filter' => array('State' => XARTHEME_STATE_ANY))))) return;

    $themeentries = array();
    $theme_totalentries = 0; $theme_untranslated =0; $theme_keytotalentries = 0; $theme_keyuntranslated = 0;
    foreach($themes as $theme) {
        $themename = $theme['osdirectory'];
        $themeid = $theme['regid'];

        $args['interface'] = 'ReferencesBackend';
        $args['locale'] = translations_working_locale();
        $testbackend = xarMod::apiFunc('translations','admin','create_backend_instance',$args);
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
    $data['property_totalentries'] = $property_totalentries;
    $data['property_untranslated'] = $property_untranslated;
    $data['property_keytotalentries'] = $property_keytotalentries;
    $data['property_keyuntranslated'] = $property_keyuntranslated;
    $data['propertyentries'] = $propertyentries;
    $data['block_totalentries'] = $block_totalentries;
    $data['block_untranslated'] = $block_untranslated;
    $data['block_keytotalentries'] = $block_keytotalentries;
    $data['block_keyuntranslated'] = $block_keyuntranslated;
    $data['blockentries'] = $blockentries;
    $data['theme_totalentries'] = $theme_totalentries;
    $data['theme_untranslated'] = $theme_untranslated;
    $data['theme_keytotalentries'] = $theme_keytotalentries;
    $data['theme_keyuntranslated'] = $theme_keyuntranslated;
    $data['themeentries'] = $themeentries;

    return $data;
}

?>