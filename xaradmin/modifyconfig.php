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
    if(!xarVarFetch('ptid', 'int', $ptid, 10, XARVAR_DONT_SET)) {return;}

    // Security check
    if (empty($ptid)) {
        $ptid = '';
        if (!xarSecurityCheck('AdminPublications')) return;
    } else {
        if (!xarSecurityCheck('AdminPublications',1,'Publication',"$ptid:All:All:All")) return;
    }

    $data = array();
    $data['ptid'] = $ptid;

    if (empty($id) || empty($pubtypes[$id]['config']['state']['label'])) {
        $data['withstate'] = 0;
    } else {
        $data['withstate'] = 1;
    }
    if (!isset($data['usetitleforurl'])) {
        $data['usetitleforurl'] = 0;
    }
    if (!isset($data['defaultsort'])) {
        $data['defaultsort'] = 'date';
    }

    // call modifyconfig hooks with module + itemtype
    $hooks = xarModCallHooks('module', 'modifyconfig', 'publications',
                             array('module'   => 'publications',
                                   'itemtype' => $ptid));

    if (empty($hooks)) {
        $data['hooks'] = array('categories' => xarML('You can assign base categories by enabling the categories hooks for publications...'));
    } else {
        $data['hooks'] = $hooks;
    }

    $data['updatelabel'] = xarML('Update Configuration');

    // Get the list of current hooks for item displays
    $hooklist = xarModGetHookList('publications','item','display',$ptid);
    $seenhook = array();
    foreach ($hooklist as $hook) {
        $data['seenhook'][$hook['module']] = 1;
    }


    $viewoptions = array();
    $viewoptions[] = array('value' => 1, 'label' => xarML('Latest Items'));

    // get root categories for this publication type
    if (!empty($id)) {
        $catlinks = xarModAPIFunc('categories','user','getallcatbases',array('module' => 'publications','itemtype' => $ptid));
    // Note: if you want to use a *combination* of categories here, you'll
    //       need to use something like 'c15+32'
        foreach ($catlinks as $catlink) {
            $viewoptions[] = array('value' => 'c' . $catlink['category_id'],
                                   'label' => xarML('Browse in') . ' ' .
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
    $data['authid'] = xarSecGenAuthKey();

    // Get the publication type for this display
    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $ptid));
    $data['settings'] = $pubtypeobject->properties['configuration']->getValue();

    return $data;
}
?>
