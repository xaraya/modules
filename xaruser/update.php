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
 * update item from publications_user_modify
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
    if (!xarSecurityCheck('ModeratePublications')) return;

    // Get parameters
    if(!xarVarFetch('itemid',       'isset', $data['itemid'],       NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('items',        'str',   $items,       '', XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('ptid',         'isset', $data['ptid'],      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('modify_cids',  'isset', $cids,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('preview',      'isset', $data['preview'],   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('quit',         'isset', $data['quit'],      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('tab',          'str:1', $data['tab'], '', XARVAR_NOT_REQUIRED)) {return;}

    // Confirm authorisation code
    // This has been disabled for now
//    if (!xarSecConfirmAuthKey()) return;

    $items = explode(',',$items);
    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $data['ptid']));
    $data['object'] = DataObjectMaster::getObject(array('name' => $pubtypeobject->properties['name']->value));
    
    // First we need to check all the data on the template
    // If checkInput fails, don't bail
    $itemsdata = array();
    $isvalid = true;
    foreach ($items as $prefix) {
        $data['object']->setFieldPrefix($prefix);
    
        // Disable the celkoposition property according if this is not the base document
        $fieldname = $prefix . '_dd_' . $data['object']->properties['parent']->id;
        $data['object']->properties['parent']->checkInput($fieldname);
        if (empty($data['object']->properties['parent']->value)) {
            $data['object']->properties['position']->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_DISPLAYONLY);
        } else {
            $data['object']->properties['position']->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_DISABLED);
        }
        
        // Now get the input from the form
        $thisvalid = $data['object']->checkInput();
        $isvalid = $isvalid && $thisvalid;
    // Store each item for later processing
        $itemsdata[$prefix] = $data['object']->getFieldValues(array(),1);
    }
    
    if ($data['preview'] || !$isvalid) {
        // Show debug info if called for
        if (!$isvalid && 
            xarModVars::get('publications','debugmode') && 
            in_array(xarUserGetVar('uname'),xarConfigVars::get(null, 'Site.User.DebugAdmins'))) {
            echo xarML('The following were invalid fields:');echo "<br/>";
            var_dump($data['object']->getInvalids());}
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
    $article = xarModCallHooks('item', 'transform-input', $data['itemid'], $article,
                               'publications', $data['ptid']);

    // Now talk to the database. Loop through all the translation pages
    foreach ($itemsdata as $id => $itemdata) {
        // Get the data for this item
        $data['object']->setFieldValues($itemdata,1);

        // Save or create the item (depends whether this translation is new)
        if (empty($id)) {$item = $data['object']->createItem();}
        else {$item = $data['object']->updateItem();}
        
        // Check if we have an alias and set it as an alias of the publications module
        $alias_flag = $data['object']->properties['alias_flag']->value;
        if ($alias_flag == 1) {
            $alias = $data['object']->properties['alias']->value;
            if (!empty($alias)) xarModAlias::set($alias, 'publications');
        } elseif($alias_flag == 2) {
            $alias = $data['object']->properties['name']->value;
            if (!empty($alias)) xarModAlias::set($alias, 'publications');
        }

        // Clear the itemid property in preparation for the next round
        unset($data['object']->itemid);
    }

    // Success
    xarSession::setVar('statusmsg', xarML('Publication Updated'));

    if ($data['quit']) {
        // Redirect if needed
        if (!xarVarFetch('return_url', 'str',   $return_url, '', XARVAR_NOT_REQUIRED)) {return;}
        if (!empty($return_url)) 
            xarController::redirect($return_url . '&itemid=' . $id);
    
        // Redirect if we came from somewhere else
        $current_listview = xarSession::getVar('publications_current_listview');
        if (!empty($current_listview)) xarController::redirect($current_listview);
        xarController::redirect(xarModURL('publications', 'user', 'view',
                                      array('ptid' => $data['ptid'])));
        return true;
    } else {
        xarController::redirect(xarModURL('publications', 'user', 'modify',
                                      array('name' => $pubtypeobject->properties['name']->value, 'itemid' => $data['itemid'])));
        return true;
    }
}

?>