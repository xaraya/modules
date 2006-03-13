<?php
/**
 * prep info for a new upload for an item
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 Xaraya
 * @link http://www.xaraya.com
 *
 * @subpackage filemanager
 * @author ashley jones <ajones@schwabfoundation.org>
*/
function filemanager_admin_newhook($args)
{
    // extract args out of an array and into local name space
    extract($args);

    // make sure we have an array
    if (!isset($extrainfo)) {
        $extrainfo = array();
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modName = xarModGetName();
    } else {
        $modName = $extrainfo['module'];
    }

    // get the modID & itemType for the modSets below, this will allow multiple
    // instances of filemanager on a page/pubtype
    $modId = xarModGetIDFromName($modName);
    $itemType = (isset($args['extrainfo']['itemtype'])?$args['extrainfo']['itemtype']:0);
    $itemId = (isset($args['extrainfo']['itemid'])?$args['extrainfo']['itemid']:0);

    // define the prefix to use the module ID, item Type and item ID (which will be 0 for now, b/c this is a new item)
    $data['prefix'] = $modId.'_'.$itemType.'_'.$itemId.'_';// modid_itemtype_itemid

    $varName = 'files.selected.'.$data['prefix'];// modid_itemtype_itemid

    // see if there has been an error with the form.  if yes, get the value, if no, set it to empty array()
    if (xarSessionGetVar('filemanager_'.$data['prefix'].'_fristTime')){
        // get the list of files from before, possibly when there was an error w/ the new item's form
        $value = @unserialize(xarModGetUserVar('filemanager', $varName));
    } else {
        xarSessionSetVar('filemanager_'.$data['prefix'].'_fristTime',1);
        $value = array();
    }

    // if we didnt get a value for $value, make it an empty string
    if (empty($value)) {
        $value = array();

        // set up an empty count of attachments, and an empty list of files
        $data['totalAttachments'] = 0;
        $data['attachment_list'] = '';
    } else {
        // count how many items we have (totalAttachments) and what the ID list is (attachment_list)
        $data['totalAttachments'] = count($value);
        $data['attachment_list'] = implode(';',$value);
    }


    // prep the URL that will open to select files.
    $data['destination_url'] = xarModURL('filemanager', 'user', 'file_selector', array('prefix' => $data['prefix']));

    // set the array of ID's so we have access to it on a different page: the file selector pop up window
    if (!xarModGetVar('filemanager', $varName)) {
        xarModSetVar('filemanager', $varName, serialize(array()));
    }
    xarModSetUserVar('filemanager', $varName, serialize($value));

    return xarTplModule('filemanager', 'user', 'showinput', $data, NULL);
}

?>
