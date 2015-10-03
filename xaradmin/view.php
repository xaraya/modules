<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 *//**
 * Standard function to view items
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_admin_view($args)
{
    extract($args);
    if (!xarVarFetch('sublink', 'str:1:', $sublink, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('catid', 'id', $catid, NULL, XARVAR_NOT_REQUIRED)) return;
    $data = array();
    if (!xarSecurityCheck('AddCrispBB', 0)) {
        return xarTplModule('privileges','user','errors',array('layout' => 'no_privileges'));
    }
    $now = time();

    // Get the forum base category
    // $mastertype = xarMod::apiFunc('crispbb', 'user', 'getitemtype', array('fid' => 0, 'component' => 'forum'));
    
    // Get the base categories ofthis module
    $basecats = xarMod::apiFunc('crispbb','user','getallcatbases');
    $parentcat = count($basecats) > 0 ? $basecats[0] : 0;
    if (!empty($catid)) {
        $categories[$catid] = xarMod::apiFunc('categories', 'user', 'getcatinfo',
            array('cid' => $catid));
    } else {
        $categories = xarMod::apiFunc('categories', 'user', 'getchildren',
            array('cid' => $parentcat));
    }

    sys::import('modules.dynamicdata.class.objects.master');
    sys::import('modules.crispbb.class.cache.links');
    // add links for cats and forums
    $numcats = count($categories);
    $ci = 1;
    $data['authid'] = xarSecGenAuthKey();
    $userLevel = xarMod::apiFunc('crispbb', 'user', 'getseclevel');
    $minLevel = 800;
    $secLevels = array();
    if (!empty($categories)) {
        foreach ($categories as $cid => $category) {
            $catinfo = $category;
            $catLevel = xarMod::apiFunc('crispbb', 'user', 'getseclevel',
                array('catid' => $cid));
            if ($catLevel < $minLevel) $minLevel = $catLevel;
            // SECCHECK: add_open_forums $cid:All IGNORE
            if ($catLevel < 400) { // No privs
                unset($categories[$cid]);
                continue;
            }
            $catinfo['forums'] = DataObjectMaster::getObjectList(array('name' => 'crispbb_forums'));
            $catinfo['forums']->layout = 'admin';
            $fieldlist = array('fname', 'fdesc','fprivileges','ftype');
            $catinfo['forums']->setFieldlist($fieldlist);
            $catinfo['forums']->setCategories($cid);
            $filter = array('sort' => 'forder ASC', 'linkfield' => 'fname', 'catid' => $cid);
            $catinfo['forums']->getItems($filter);
            $catinfo['numforums'] = count($catinfo['forums']->items);
            $catinfo['newforum'] = LinkCache::getCachedURL('crispbb', 'admin', 'new', array('catid' => $cid));
            $catinfo['view'] = LinkCache::getCachedURL('crispbb', 'admin', 'view', array('catid' => $cid));
            if ($userLevel == 800 && xarSecurityCheck('ManageCategories', 0)) {
               if ($ci > 1) {
                    $catinfo['moveup'] = LinkCache::getCachedURL('crispbb', 'admin', 'ordercats', array('itemid' => $cid, 'direction' => 'up', 'authid' => $data['authid']));
               }
               if ($ci < count($categories)) {
                    $catinfo['movedown'] = LinkCache::getCachedURL('crispbb', 'admin', 'ordercats', array('itemid' => $cid, 'direction' => 'down', 'authid' => $data['authid']));
               }
               $catinfo['modifycat'] = LinkCache::getCachedURL('crispbb', 'admin', 'modifycat', array('itemid' => $cid));
               $catinfo['deletecat'] = LinkCache::getCachedURL('crispbb', 'admin', 'deletecat', array('itemid' => $cid));
            }
            $ci++;
            $categories[$cid] = $catinfo;
            $recount = false;
        } // end categories loop
    }
    $data['categories'] = $categories;
    $data['menulinks'] = xarMod::apiFunc('crispbb', 'admin', 'getmenulinks',
        array(
            'current_module' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'view',
            'current_sublink' => $sublink,
            'catid' => $catid,
            'secLevels' => $secLevels
        ));

    if (empty($categories) && ($userLevel == 800 && xarSecurityCheck('ManageCategories', 0)))
        $data['newcat'] = LinkCache::getCachedURL('crispbb', 'admin', 'newcat');

    $pageTitle = xarML('Manage Forums');
    // store function name for use by admin-main as an entry point
    xarSessionSetVar('crispbb_adminstartpage', 'view');
    xarTpl::setPageTitle(xarVarPrepForDisplay($pageTitle));

    return $data;

}
?>