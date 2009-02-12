<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * Modify configuration
 */
function publications_admin_modifyconfig()
{
    // Get parameters
    if(!xarVarFetch('ptid', 'int', $data['ptid'], xarModVars::get('publications', 'defaultpubtype'), XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'global', XARVAR_NOT_REQUIRED)) return;

    // Security check
    if (empty($data['ptid'])) {
        if (!xarSecurityCheck('AdminPublications')) return;
    } else {
        if (!xarSecurityCheck('AdminPublications',1,'Publication',$data['ptid'] . ":All:All:All")) return;
    }

    if (empty($id) || empty($pubtypes[$id]['config']['state']['label'])) {
        $data['withstate'] = 0;
    } else {
        $data['withstate'] = 1;
    }
    if (!isset($data['usetitleforurl'])) $data['usetitleforurl'] = 0;
    if (!isset($data['defaultsort'])) $data['defaultsort'] = 'date';

    $viewoptions = array();
    $viewoptions[] = array('id' => 1, 'name' => xarML('Latest Items'));

    // get root categories for this publication type
    if (!empty($id)) {
        $catlinks = xarModAPIFunc('categories','user','getallcatbases',array('module' => 'publications','itemtype' => $data['ptid']));
    // Note: if you want to use a *combination* of categories here, you'll
    //       need to use something like 'c15+32'
        foreach ($catlinks as $catlink) {
            $viewoptions[] = array('id' => 'c' . $catlink['category_id'],
                                   'name' => xarML('Browse in') . ' ' .
                                              $catlink['name']);
        }
    }
    $data['viewoptions'] = $viewoptions;

    if (empty($id)) {
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
    }

    $data['stateoptions'] = array();
    $states = xarModAPIFunc('publications','user','getstates');
    foreach ($states as $id => $name) {
        $data['stateoptions'][] = array('value' => $id, 'label' => $name);
    }

    // Module alias for short URLs
    $pubtypes = xarModAPIFunc('publications','user','getpubtypes');
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

    // Get the publication type for this display
    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $data['ptid']));
    $data['settings'] = $pubtypeobject->properties['configuration']->getValue();

    return $data;
}
?>
