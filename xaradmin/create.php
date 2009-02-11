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
 * create item from xarModFunc('publications','admin','new')
 *
 * @param id     ptid       The publication Type ID for this new article
 * @param array  new_cids   An array with the category ids for this new article (OPTIONAL)
 * @param string preview    Are we gonna see a preview?
 * @param string save       Call the save action
 * @param string return_url The URL to return to
 * @throws BAD_PARAM
 * @return  bool true on success, or mixed on failure
 */

sys::import('modules.dynamicdata.class.objects.master');

function publications_admin_create()
{
    if (!xarVarFetch('ptid',       'id',    $data['ptid'])) {return;}
    if (!xarVarFetch('new_cids',   'array', $cids,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('preview',    'str',   $data['preview'], NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('save',       'str',   $save, NULL, XARVAR_NOT_REQUIRED)) {return;}

    // Confirm authorisation code
    // This has been disabled for now
//    if (!xarSecConfirmAuthKey()) return;

    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $data['ptid']));
    $data['object'] = DataObjectMaster::getObject(array('name' => $pubtypeobject->properties['name']->value));
    $isvalid = $data['object']->checkInput();
    
    if ($data['preview'] || !$isvalid) {
        // Preview or bad data: redisplay the form
        $data['properties'] = $data['object']->getProperties();
        if ($data['preview']) $data['tab'] = 'preview';
        return xarTplModule('publications','admin','new', $data);    
    }

/*
    if (!empty($cids) && count($cids) > 0) {
        $article['cids'] = array_values(preg_grep('/\d+/',$cids));
    } else {
        $article['cids'] = array();
    }

    // call transform input hooks
    $article['transform'] = array('summary','body','notes');
    $article = xarModCallHooks('item', 'transform-input', 0, $article,
                               'publications', $data['ptid']);
*/
    // Pass to API
    $id = $data['object']->createItem();

    // if we can edit publications, go to admin view, otherwise go to user view
    if (xarSecurityCheck('EditPublications',0,'Publication',$data['ptid'].':All:All:All')) {
        xarResponseRedirect(xarModURL('publications', 'admin', 'view',
                                      array('ptid' => $data['ptid'])));
    } else {
        xarResponseRedirect(xarModURL('publications', 'user', 'view',
                                      array('ptid' => $data['ptid'])));
    }

    return true;
}

?>