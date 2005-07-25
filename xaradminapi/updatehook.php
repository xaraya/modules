<?php

function uploads_adminapi_updatehook($args)
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
    // instances of uploads on a page/pubtype
    $modId = xarModGetIDFromName($modName);
    $itemType = (isset($args['extrainfo']['itemtype'])?$args['extrainfo']['itemtype']:0);
    $itemId = (isset($args['objectid'])?$args['objectid']:0);

    
    // define the prefix to use the module ID, item Type and item ID (which will be 0 for now, b/c this is a new item)
    $data['prefix'] = $modId.'_'.$itemType.'_'.$itemId.'_';// modid_itemtype_itemid
    $varName = $data['prefix']."attachment_list";// modid_itemtype_itemi
    
    // get the semi delimited array passed to us from the form
    if (!xarVarFetch($varName,   'str:1:', $value,     ''  ,   XARVAR_NOT_REQUIRED)) return;

    // if we didnt get a value for $value, make it an empty string
    if (empty($value)) {
        $value = '';
    }

    //generate an array by exploding the string were were passed
    $selectedFiles = array_filter(explode(';', $value),'trim');

    // we always want to zero out what's attached to this item, so just
    // call delete assoc every time and then add back any passed items,
    // if any
    
    $_deletedFileIds = xarModAPIFunc('uploads','user','db_delete_association',
        array(
            'modid'        => $modId,
            'itemtype'    => $itemType,
            'itemid'     => $itemId)
        );

    if (!empty($selectedFiles)){
        // loop through the passed file IDs and process each one
        foreach ($selectedFiles as $_fileId) {
            
            // add the stuff to the db for this item
            $_resultingFileId = xarModAPIFunc('uploads','user','db_add_association',
                array(
                    'modid'        =>$modId,
                    'itemtype'    =>$itemType,
                    'itemid'     =>$itemId,
                    'fileid'    =>$_fileId)
                );
                    
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
