<?php
/**
 * show input fields for uploads module (used in DD properties)
 *
 * @param  $args ['id'] string id of the upload field(s)
 * @param  $args ['value'] string the current value(s)
 * @param  $args ['format'] string format specifying 'fileupload', 'textupload' or 'upload' (future ?)
 * @param  $args ['multiple'] boolean allow multiple uploads or not
 * @param  $args ['methods'] array of allowed methods 'trusted', 'external', 'stored' and/or 'upload'
 * @returns string
 * @return string containing the input fields
 */
function uploads_admin_modifyhook($args)
{
    extract($args);

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modName = xarModGetName();
    } else {
        $modName = $extrainfo['module'];
    }

    // get the modID & itemId for the modSets below, this will allow multiple
    // instances of uploads on a page/pubtype
    $modId = xarModGetIDFromName($modName);
    $itemType = (isset($args['extrainfo']['itemtype'])?$args['extrainfo']['itemtype']:0);
    $itemId = (isset($args['extrainfo']['itemid'])?$args['extrainfo']['itemid']:0);
    
    
    $data['prefix']  = $modId.'_'.$itemType.'_'.$itemId.'_';// modid_itemtype_itemid
    $varName = 'files.selected.' .  $data['prefix'];
    
    // get all the file the user may have associated with this item
    $selectedFiles =array_keys(xarModAPIFunc('uploads','user','db_get_associations',array(
                'modid'        =>$modId,
                'itemtype'    =>$itemType,
                'itemid'     =>$itemId)));
                
    // check to see if we got some file IDs
    if (!isset($selectedFiles) || empty($selectedFiles)) {
        $selectedFiles = array();
    } else {
        // make sure each file exists by getting info on the file from the DB
        $fileList = xarModAPIFunc('uploads', 'user', 'db_get_file_entry', array('fileId' => $selectedFiles));
        if (is_array($fileList)){
            // return all the found IDs in the DB.  IDs that the DB didn't find, were'nt returned with db_get_file_entry
            // and thus have been discarded
            $selectedFiles = array_keys($fileList);
        }
    }
    
    // prep a destination URL for the pop up    [1] => 236 window: file selector
    $data['destination_url']  = xarModURL('uploads', 'user', 'file_selector', array('prefix' => $data['prefix']));
    $data['totalAttachments'] = count($selectedFiles);
    $data['attachment_list']  = ';'.implode(';',$selectedFiles);

    // set the array of ID's so we have access to it on a different page: the file selector pop up window
    if (!xarModGetVar('uploads', $varName)) {
        xarModSetVar('uploads', $varName, serialize(array()));
    }
    xarModSetUserVar('uploads', $varName, serialize($selectedFiles));
    
    // return the concatenated strign with all the info we prepped above.
    return xarTplModule('uploads', 'user', 'showinput', $data, NULL);

}
?>