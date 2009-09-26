<?php

/**
 * File: $Id$
 *
 * Modify module configuration
 *
 * @package Xaraya
 * @copyright (C) 2004 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.academe.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 */

function xarpages_admin_modifyconfig($args)
{
    extract($args);
    $data = array();

    // Need admin priv to modify config.
    if (!xarSecurityCheck('AdminXarpagesPage')) return;

    // Get the tree of all pages.
    $data['tree'] = xarMod::apiFunc('xarpages', 'user', 'getpagestree', array('dd_flag' => false));
    if (!xarVarFetch('phase',        'str:1:100', $phase,       'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;

    // Implode the names for each page into a path for display.
    foreach ($data['tree']['pages'] as $key => $page) {
        $data['tree']['pages'][$key]['slash_separated'] =  '/' . implode('/', $page['namepath']);
    }

    // Get the current module values.
    $data['defaultpage'] = xarModVars::get('xarpages', 'defaultpage');
    $data['errorpage'] = xarModVars::get('xarpages', 'errorpage');
    $data['notfoundpage'] = xarModVars::get('xarpages', 'notfoundpage');
    $data['noprivspage'] = xarModVars::get('xarpages', 'noprivspage');

    // Boolean (1/0) flags.
    $data['transformref'] = xarModVars::get('xarpages', 'transformref');
    $data['shortestpath'] = xarModVars::get('xarpages', 'shortestpath');
    $data['shorturl'] = xarModVars::get('xarpages', 'enable_short_urls');

    // Text fields
    $data['transformfields'] = xarModVars::get('xarpages', 'transformfields');
        
    $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'xarpages'));
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, module_alias_name, enable_short_urls');
    $data['module_settings']->getItem();
    switch (strtolower($phase)) {
        case 'modify':
        default:
            break;

        case 'update':

        // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) {
                return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
            }        

            $isvalid = $data['module_settings']->checkInput();
            if (!$isvalid) {
                return xarTplModule('authsystem','admin','modifyconfig', $data);        
            } else {
                $itemid = $data['module_settings']->updateItem();
            }

            // Get the special pages.
            foreach(array('defaultpage', 'errorpage', 'notfoundpage', 'noprivspage') as $special_name) {
                unset($special_id);
                if (!xarVarFetch($special_name, 'id', $special_id, 0, XARVAR_NOT_REQUIRED)) {return;}
                xarModVars::set('xarpages', $special_name, $special_id);

                // Save value for redisplaying in the form.
                $data[$special_name] = $special_id;
            }

            // Shortest path flag.
            xarVarFetch('shortestpath', 'int:0:1', $shortestpath, 0, XARVAR_NOT_REQUIRED);
            xarModVars::set('xarpages', 'shortestpath', $shortestpath);
            $data['shortestpath'] = $shortestpath;

            // Enable internal references transform flag.
            // This transforms "#" anchors in content to an absolute URI for the page.
            xarVarFetch('transformref', 'int:0:1', $transformref, 0, XARVAR_NOT_REQUIRED);
            xarModVars::set('xarpages', 'transformref', $transformref);
            $data['transformref'] = $transformref;

            // Limit the DD fields that will be transformed.
            xarVarFetch('transformfields', 'strlist: ,;|:pre:trim:vtoken', $transformfields, '', XARVAR_NOT_REQUIRED);
            xarModVars::set('xarpages', 'transformfields', $transformfields);
            $data['transformfields'] = $transformfields;

            // Use icons in hte UI
            xarVarFetch('useicons', 'checkbox', $useicons, 0, XARVAR_NOT_REQUIRED);
            xarModVars::set('xarpages', 'useicons', $useicons);
        break;
    }

    // Check any problem aliases
    $problem_aliases = xarMod::apiFunc('xarpages', 'user', 'getaliases', array('mincount' => 2));
    $data['problem_aliases'] = $problem_aliases;

    // Config hooks for all page types.

    // Get the itemtype of the page type.
    $type_itemtype = xarMod::apiFunc('xarpages', 'user', 'gettypeitemtype');

    $confighooks = array();
    $confighooks = xarModCallHooks(
        'module', 'modifyconfig', 'xarpages',
        array('module' => 'xarpages', 'itemtype' => $type_itemtype)
    );

    // Clear out any empty hooks.
    if(!empty($confighooks)) {
        foreach($confighooks as $key => $confighook) {
            if (trim($confighook) == '') {
                unset($confighooks[$key]);
            } else {
                $confighooks[$key] = trim($confighook);
            }
        }
        
    }
    $data['confighooks'] =& $confighooks;

    return $data;
}

?>
