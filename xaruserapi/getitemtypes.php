<?php

/**
    Utility function to retrieve the list of item types of this module
 
    @author Brian McGilligan
    @returns array - containing the item types and their description
*/
function helpdesk_userapi_getitemtypes($args)
{
    $itemtypes = array();
    
    // Get objects to build tabs    
    $modid = xarModGetIDFromName('helpdesk');
    $objects = xarModAPIFunc('dynamicdata', 'user', 'getObjects');        
    $data['objects'] = array();
    foreach($objects as $object){
        if($object['moduleid'] == $modid){           
            $data['objects'][] = $object;
            $itemtypes[$object['itemtype']]   = 
                array('label' => xarVarPrepForDisplay($object['label']),
                      'title' => xarVarPrepForDisplay(xarML('View Object')),
                      'url'   => xarModURL('helpdesk','user','view')
                     );
        }
    }
    
    return $itemtypes;
}
?>