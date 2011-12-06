<?php
/**
 * ModuleUpdateconfig Hook
 *
 * Updates subject module (+itemtype) keywords configuration
**/
function keywords_hooksapi_moduleupdateconfig($args)
{
    extract($args);

    if (empty($extrainfo))
        $extrainfo = array();

    // objectid is the name of the module
    if (empty($objectid)) {
        if (!empty($extrainfo['module']) && is_string($extrainfo['module'])) {
            $objectid = $extrainfo['module'];
        } else {
            $objectid = xarMod::getName();
        }
    }

    if (!isset($objectid) || !is_string($objectid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('objectid (module name)', 'admin', 'moduleupdatehook', 'keywords');
        throw new BadParameterException($vars, $msg);
    }

    $modname = $objectid;

    $modid = xarMod::getRegId($modname);
    if (empty($modid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('module', 'admin', 'moduleupdatehook', 'keywords');
        throw new BadParameterException($vars, $msg);
    }

    if (!empty($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (!xarSecurityCheck('AdminKeywords', 0, 'Item', "$modid:$itemtype:All")) return $extrainfo;

    $settings = xarMod::apiFunc('keywords', 'hooks', 'getsettings',
        array(
            'module' => $modname,
            'itemtype' => $itemtype,
        ));

    if ($settings['config_state'] == 'default') {
        // per module settings disabled, if this isn't the keywords module, bail
        if ($modname != 'keywords') return $extrainfo;
    } elseif ($settings['config_state'] == 'module') {
        // per itemtype settings disabled, if this isn't itemtype 0, bail
        if (!empty($itemtype)) return $extrainfo;
    }

    if (!xarVarFetch('keywords_settings["global_config"]', 'checkbox',
        $global_config, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('keywords_settings["auto_tag_create"]', 'pre:trim:str:1:',
        $auto_tag_create, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('keywords_settings["auto_tag_persist"]', 'checkbox',
        $auto_tag_persist, false, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('keywords_settings["meta_keywords"]', 'int:0:2',
        $meta_keywords, 0, XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('keywords_settings["restrict_words"]', 'checkbox',
        $restrict_words, false, XARVAR_NOT_REQUIRED)) return;

    if (!empty($auto_tag_create)) 
        $auto_tag_create = xarModAPIFunc('keywords','admin','separekeywords',
            array(
                'keywords' => $auto_tag_create,
            ));

    if (!empty($meta_keywords)) {
        if (!xarVarFetch('keywords_settings["meta_lang"]', 'pre:trim:lower:str:1:',
            $meta_lang, $settings['meta_lang'], XARVAR_NOT_REQUIRED)) return;
        $settings['meta_lang'] = $meta_lang;
    }

    // when switching between restricted and unrestricted we want to preserve settings
    $status_quo = $restrict_words == $settings['restrict_words'];
    if ($restrict_words && $status_quo) {
        if (!xarVarFetch('keywords_settings["restricted_list"]', 'pre:trim:str:1:',
            $restricted_list, '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('keywords_settings["allow_manager_add"]', 'checkbox',
            $allow_manager_add, false, XARVAR_NOT_REQUIRED)) return;
        $settings['allow_manager_add'] = $allow_manager_add;
        $old_list = xarMod::apiFunc('keywords', 'words', 'getwords',
            array(
                'index_id' => $settings['index_id'],
            ));
        $new_list = xarModAPIFunc('keywords','admin','separekeywords',
            array(
                'keywords' => $restricted_list,
            ));
        // be sure to add any auto tags to the list 
        if (!empty($auto_tag_create))
            $new_list = array_merge($new_list, $auto_tag_create);
        $new_list = array_values(array_unique(array_filter($new_list)));
        // add everything from new list that's not in old list
        $toadd = array_diff($new_list, $old_list);
        // remove everything from old list that's not in new list
        $toremove = array_diff($old_list, $new_list);

        if (!empty($toadd)) {
            if (!xarMod::apiFunc('keywords', 'words', 'createitems',
                array(
                    'index_id' => $settings['index_id'],
                    'keyword' => $toadd,
                ))) return;
        }
        if (!empty($toremove)) {
            if (!xarMod::apiFunc('keywords', 'words', 'deleteitems',
                array(
                    'index_id' => $settings['index_id'],
                    'keyword' => $toremove,
                ))) return;
        }
    }

    $settings['global_config'] = $global_config;
    $settings['auto_tag_create'] = !empty($auto_tag_create) ? $auto_tag_create : array();
    $settings['auto_tag_persist'] = $auto_tag_persist;
    $settings['meta_keywords'] = $meta_keywords;
    $settings['restrict_words'] = $restrict_words;
    if (!xarMod::apiFunc('keywords', 'hooks', 'updatesettings',
        array(
            'module' => $modname,
            'itemtype' => $itemtype,
            'settings' => $settings,
        ))) return $extrainfo;

    return $extrainfo;
}
?>