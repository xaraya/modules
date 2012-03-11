<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * update item from publications_admin_modify
 *
 * @param id     ptid       The publication Type ID for this new article
 * @param array  new_cids   An array with the category ids for this new article (OPTIONAL)
 * @param string preview    Are we gonna see a preview? (OPTIONAL)
 * @param string save       Call the save action (OPTIONAL)
 * @param string return_url The URL to return to (OPTIONAL)
 * @return  bool true on success, or mixed on failure
 */

sys::import('modules.dynamicdata.class.objects.master');

function publications_user_update()
{
    // Get parameters
    if(!xarVarFetch('itemid',       'isset', $itemid,       NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('items',        'str',   $items,       '', XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('ptid',         'isset', $data['ptid'],      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('modify_cids',  'isset', $cids,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('preview',      'isset', $data['preview'],   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('quit',         'isset', $data['quit'],      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('tab',          'str:1', $data['tab'], '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('returnurl',    'str:1', $data['returnurl'], 'view', XARVAR_NOT_REQUIRED)) {return;}

    // Confirm authorisation code
    // This has been disabled for now
//    if (!xarSecConfirmAuthKey()) return;

    $items = explode(',',$items);
    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $data['ptid']));
    $data['object'] = DataObjectMaster::getObject(array('name' => $pubtypeobject->properties['name']->value));
    $isvalid = $data['object']->checkInput();
    
    // First we need to check all the data on the template
    // If checkInput fails, don't bail
    $itemsdata = array();
    $isvalid = true;
    
    /*foreach ($items as $prefix) {
        $data['object']->setFieldPrefix($prefix);
        $thisvalid = $data['object']->checkInput();
        $isvalid = $isvalid && $thisvalid;
    // Store each item for later processing
        $itemsdata[$prefix] = $data['object']->getFieldValues(array(),1);
    }*/
    
    if ($data['preview'] || !$isvalid) {
        // Preview or bad data: redisplay the form
        $data['properties'] = $data['object']->getProperties();
        if ($data['preview']) $data['tab'] = 'preview';
        $data['items'] = $itemsdata;
        // Get the settings of the publication type we are using
        $data['settings'] = xarModAPIFunc('publications','user','getsettings',array('ptid' => $data['ptid']));
        
        return xarTplModule('publications','user','modify', $data);
    }
    
    // call transform input hooks
    $article['transform'] = array('summary','body','notes');
    $article = xarModCallHooks('item', 'transform-input', $itemid, $article,
                               'publications', $data['ptid']);

    // Now talk to the database
    /*foreach ($itemsdata as $itemid => $itemdata) {
        $data['object']->setFieldValues($itemdata);
        if (empty($itemid)) $item = $data['object']->createItem();
        else $item = $data['object']->updateItem();
    // Clear the itemid property in preparation for the next round
        unset($data['object']->itemid);
    }*/
    
    if (empty($itemid)) $item = $data['object']->createItem();
    else $item = $data['object']->updateItem();
    // Success
    xarSession::setVar('statusmsg', xarML('Publication Updated'));

    // if we can edit publications, go to admin view, otherwise go to user view
    if (xarSecurityCheck('EditPublications',0,'Publication',$data['ptid'].':All:All:All')) {
        if ($data['quit']) {
            xarController::redirect(xarModURL('publications', 'user', 'view',
                                          array('ptid' => $data['ptid'])));
            return true;
        } else {
            xarController::redirect(xarModURL('publications', 'user', 'modify',
                                          array('name' => $pubtypeobject->properties['name']->value, 'itemid' => $itemid)));
            return true;
        }
    }
    
    return true;
}

?>