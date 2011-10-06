<?php
/**
 * Keywords Modfiy Config form
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Keywords Module
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
*/

/**
 * Prepare data for form. May be called from form itself with updated
 * configuration parameters.
 *
 * @author mikespub
 * @access public
 * @param int $restricted 1 for pregiven keyword list, 0 for free input
 * @param int $useitemtype 1 for itemtype specific keyword lists
 * @return true on success or void on failure
 * @throws no exceptions
 * @todo nothing
 */
function keywords_admin_modifyconfig()
{
    // Default value is NULL for providing NOT isset variables to following code
    if (!xarVarFetch('restricted', 'int:0:1', $data['restricted'], xarModVars::get('keywords','restricted'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('useitemtype', 'int:0:1', $data['useitemtype'], xarModVars::get('keywords','useitemtype'), XARVAR_NOT_REQUIRED)) return;
    if (!xarSecurityCheck('AdminKeywords')) return;

    $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'keywords'));
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, module_alias_name, enable_short_urls, user_menu_link');
    $data['module_settings']->getItem();

    $data['settings'] = array();
    $keywords = xarModAPIFunc('keywords',
                              'admin',
                              'getwordslimited',
                              array('moduleid' => '0'));


    // $keywords = xarModVars::get('keywords','default');
    if ($data['useitemtype']== 0) {
    $data['settings']['default'] = array('label' => xarML('Default configuration'),
                                         'keywords' => $keywords);
    } else {
    $data['settings']['default'][0] = array('label' => xarML('Default configuration'),
                                            'keywords' => $keywords);
    }

    $hookedmodules = xarModAPIFunc('modules',
                                   'admin',
                                   'gethookedmodules',
                                   array('hookModName' => 'keywords'));

    if (isset($hookedmodules) && is_array($hookedmodules)) {
        foreach ($hookedmodules as $modname => $value) {
            if ($data['useitemtype']== 1) {
                $modules[$modname] = xarModAPIFunc($modname,'user','getitemtypes',array(), 0);
                if (!isset($modules[$modname])) {
                    $modules[$modname][0]['label']= $modname;
                 }
                foreach ($modules as $mod => $v1) {
                    foreach ($v1 as $itemtype => $item) {
                        foreach ($item as $k3 => $v3) {
                            $moduleid = xarModGetIDFromName($mod,'module');
                         $keywords = xarModAPIFunc('keywords',
                                                   'admin',
                                                   'getwordslimited',
                                                   array('moduleid' => $moduleid,
                                'itemtype' => $itemtype));
                            if ($itemtype == 0) {
                                $link = xarModURL($mod,'user','main');
                            } else {
                                $link = xarModURL($mod,'user','view',array('itemtype' => $itemtype));
                            }
                            $label = $item['label'];
                            $data['settings'][$mod][$itemtype] = array('label'     => $label,
                                                                    'keywords'   => $keywords);
                        }
                    }
                }
            } else {
                      $moduleid = xarModGetIDFromName($modname,'module');
                      $keywords = xarModAPIFunc('keywords',
                                                'admin',
                                                'getwordslimited',
                                                 array('moduleid' => $moduleid));
                $link = xarModURL($modname,'user','main');
                $data['settings'][$modname] = array('label'    => $modname,
                                                    'keywords'   => $keywords);
            }
        }
    }

    $data['isalias'] = xarModVars::get('keywords','SupportShortURLs');
    $data['showsort'] = xarModVars::get('keywords','showsort');
    $data['displaycolumns'] = xarModVars::get('keywords','displaycolumns');
    $data['delimiters'] = xarModVars::get('keywords','delimiters');
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}
?>