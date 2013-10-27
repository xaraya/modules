<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Modify configuration
 */
function publications_admin_modifyconfig()
{
    if (!xarSecurityCheck('AdminPublications')) return;

    // Get parameters
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'global', XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('ptid', 'int', $data['ptid'], xarModVars::get('publications', 'defaultpubtype'), XARVAR_DONT_SET)) {return;}

    if ($data['tab'] == 'pubtypes') {
        // Configuration specific to a publication type
        if (!xarSecurityCheck('AdminPublications',1,'Publication',$data['ptid'] . ":All:All:All")) return;

        if (empty($pubtypes[$data['ptid']]['config']['state']['label'])) {
            $data['withstate'] = 0;
        } else {
            $data['withstate'] = 1;
        }

        $viewoptions = array();
        $viewoptions[] = array('id' => 1, 'name' => xarML('Latest Items'));

        if (!isset($data['usetitleforurl'])) $data['usetitleforurl'] = 0;
        if (!isset($data['defaultsort'])) $data['defaultsort'] = 'date';

        // get root categories for this publication type
        if (!empty($id)) {
            $catlinks = xarMod::apiFunc('categories','user','getallcatbases',array('module' => 'publications','itemtype' => $data['ptid']));
        // Note: if you want to use a *combination* of categories here, you'll
        //       need to use something like 'c15+32'
            foreach ($catlinks as $catlink) {
                $viewoptions[] = array('id' => 'c' . $catlink['category_id'],
                                       'name' => xarML('Browse in') . ' ' .
                                                  $catlink['name']);
            }
        }
        $data['viewoptions'] = $viewoptions;

        // Get the publication type for this display
        $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
        $pubtypeobject->getItem(array('itemid' => $data['ptid']));

        // Get the settings for this publication type
        sys::import('modules.publications.xaruserapi.getsettings');
        $settings = @unserialize($pubtypeobject->properties['configuration']->getValue());
        $globalsettings = publications_userapi_getglobalsettings();
        if (is_array($settings)) {
            $data['settings'] = $settings + $globalsettings;
        } else {
            $data['settings'] = $globalsettings;
        }
    
    } else {
        // Global configuration
        if (!xarSecurityCheck('AdminPublications')) return;

        //The usual bunch of vars
        $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'publications'));
        $data['module_settings']->setFieldList('items_per_page, use_module_alias, module_alias_name, user_menu_link', 'use_module_icons');
        $data['module_settings']->getItem();

        $data['shorturls'] = xarModVars::get('publications','SupportShortURLs') ? true : false;

        $data['defaultpubtype'] = xarModVars::get('publications', 'defaultpubtype');
        if (empty($data['defaultpubtype'])) {
            $data['defaultpubtype'] = '';
        }
        $data['sortpubtypes'] = xarModVars::get('publications', 'sortpubtypes');
        if (empty($data['sortpubtypes'])) {
            $data['sortpubtypes'] = 'id';
            xarModVars::set('publications','sortpubtypes','id');
        }

        // Get the tree of all pages.
        $data['tree'] = xarMod::apiFunc('publications', 'user', 'getpagestree', array('dd_flag' => false));    

        // Implode the names for each page into a path for display.
        $data['treeoptions'] = array();
        foreach ($data['tree']['pages'] as $key => $page) {
    //        $data['tree']['pages'][$key]['slash_separated'] =  '/' . implode('/', $page['namepath']);
            $data['treeoptions'][] = array('id' => $page['id'], 'name' => '/' . implode('/', $page['namepath']));
        }

        // Module alias for short URLs
        $pubtypes = xarMod::apiFunc('publications','user','get_pubtypes');
        if (!empty($id)) {
            $data['alias'] = $pubtypes[$id]['name'];
        } else {
            $data['alias'] = 'frontpage';
        }
        $modname = xarModGetAlias($data['alias']);
        if ($modname == 'publications') {
            $data['usealias'] = true;
        } else {
            $data['usealias'] = false;
        }

        $data['redirects'] = unserialize(xarModVars::get('publications','redirects'));
    
        // Whether the languages property is loaded
        sys::import('modules.dynamicdata.class.properties.registration');
        $types = PropertyRegistration::Retrieve();
        $data['languages'] = isset($types[30039]);

    }

    return $data;
}
?>