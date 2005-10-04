<?php

function filemanager_adminapi_createhook($args)
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

    // get the modID & itemId for the modSets below, this will allow multiple
    // instances of filemanager on a page/pubtype
    $modId = xarModGetIDFromName($modName);
    $itemType = (isset($args['extrainfo']['itemtype'])?$args['extrainfo']['itemtype']:0);
    $itemId = 0;
    
    // define the prefix to use the module ID, item Type and item ID (which will be 0 for now, b/c this is a new item)
    $data['prefix'] = $modId.'_'.$itemType.'_'.$itemId.'_';// modid_itemtype_itemid
    $varName = 'files.selected.'.$data['prefix'];// modid_itemtype_itemi
    
    if (!xarVarFetch($data['prefix'] . 'attachment_list', 'str:0:', $value, '', XARVAR_NOT_REQUIRED)) return;

    // make sure that any new articles wont try to load another, defunct new articles selected list
    xarSessionSetVar('filemanager_'.$data['prefix'].'_fristTime',0);
    
    // if we didnt get a value for $value, make it an empty string
    if (empty($value)) {
        $value = '';
    }    
    
    //generate an array by exploding the string were were passed
    $selectedFiles = array_filter(explode(';', $value),'trim');
    
    // get rid of empty entries w/ trim
    $list = array_filter($selectedFiles,'trim');
    
    // use the new articles item ID instead of temp 0 id
    $itemId = (isset($args['extrainfo']['itemid'])?$args['extrainfo']['itemid']:0);
    
    // set the var name so that we can do a xarModSetVar using the real itemId, instead of 
    // using 0 as a temp id
    $varName = 'files.selected.'.$modId.'_'.$itemType.'_'.$itemId.'_';// modid_itemtype_itemid
    xarModSetVar('filemanager', $varName, serialize($selectedFiles));
    
    if (!empty($list)){
        // loop through the passed file IDs and process each one
        foreach ($selectedFiles as $_fileId) {
            
            // add the stuff to the db for this item
            $_resultingFileId = xarModAPIFunc('filemanager','user','db_add_association',array(
                    'modid'        =>$modId,
                    'itemtype'    =>$itemType,
                    'itemid'     =>$itemId,
                    'fileid'    =>$_fileId));
            // make sure it saved correctly by matching two file IDs
            if ($_resultingFileId != $_fileId){
                return;
            }// failed db_add_associate
        }// for each file ID
    }// if $list isn't empty
    else {
        return true;
    }
}

?>
