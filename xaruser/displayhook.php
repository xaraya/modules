<?php
/**
 * show input fields for filemanager module (used in DD properties)
 *
 * @param  $args ['id'] string id of the upload field(s)
 * @param  $args ['value'] string the current value(s)
 * @param  $args ['format'] string format specifying 'fileupload', 'textupload' or 'upload' (future ?)
 * @param  $args ['multiple'] boolean allow multiple filemanager or not
 * @param  $args ['methods'] array of allowed methods 'trusted', 'external', 'stored' and/or 'upload'
 * @returns string
 * @return string containing the input fields
 */
function filemanager_user_displayhook($args)
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
    // instances of filemanager on a page/pubtype
    $modId = xarModGetIDFromName($modName);
    $itemType = (isset($args['extrainfo']['itemtype'])?$args['extrainfo']['itemtype']:0);
    $itemId = (isset($args['objectid'])?$args['objectid']:0);
    
    
    $data['prefix']  = $modId.'_'.$itemType.'_'.$itemId.'_';// modid_itemtype_itemid
    $varName = 'files.selected.' .  $data['prefix'];

    // get all the file the user may have associated with this item
    $selectedFiles =array_keys(xarModAPIFunc('filemanager','user','db_get_associations',array(
                'modid'        =>$modId,
                'itemtype'    =>$itemType,
                'itemid'     =>$itemId)));
                
    // check to see if we got some file IDs
    if (!isset($selectedFiles) || empty($selectedFiles)) {
        $selectedFiles = array();
    } else {
        // make sure each file exists by getting info on the file from the DB
        $fileList = xarModAPIFunc('filemanager', 'user', 'db_get_file_entry', array('fileId' => $selectedFiles));
        if (is_array($fileList)){
            // return all the found IDs in the DB.  IDs that the DB didn't find, were'nt returned with db_get_file_entry
            // and thus have been discarded
            $selectedFiles = array_keys($fileList);
        }
    }
    
    // get the settings string  into the $settings array
    $settings = xarModAPIFunc('filemanager', 'admin', 'get_attachment_settings', array('modid' => $modId, 'itemtype' => $itemType));

    // get the array file meta data based on the $list array
    $data['attached_items'] = xarModAPIFunc('filemanager','user','db_get_file_entry',array(
                     'fileId' => $selectedFiles));
    
    switch($settings['displayas']){
        case "list":
        case "icons":  
            return xarTplModule('filemanager','user','display_hook',$data,$settings['displayas']);
            break; 
        case "raw":  
            // TODO get the mod name and itemtype name for human readabilty
            $modName = (isset($args['extrainfo']['module'])?$args['extrainfo']['module']:'no_module_specified');
            $types = xarModAPIFunc($modName, 'user', 'getitemtypes',
                        // don't throw an exception if this function doesn't exist
                        array(), 0);
            $itemName = strtolower(xarVarPrepForOS($types[$itemType]['label']));
            
            return xarTplModule('filemanager','user','display_hook',$data,'raw_'.$modName."_".$itemName);
            break;
    }
}
?>