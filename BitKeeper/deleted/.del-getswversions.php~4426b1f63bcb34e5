<?php
function helpdesk_userapi_getswversions($args) 
{
    // create the return object to be inserted into the form
    $output = array();

    extract($args);
    if(!isset($selected_id)){
        $selected_id=0;
    }
    if(!isset($selected_id)){ return; }
    
    $modid = xarModGetIDFromName(xarModGetName());
    $itemtype = 9;

    $items = xarModAPIFunc('dynamicdata', 'user', 'getitems', 
                          array('moduleid' => $modid,
                                'itemtype' => $itemtype
                               )
                         );

    $data = array();                         
    foreach($items as $item){
        if($item['software'] == $selected_id){
            if($item['version'] == $selected_id){ $selected = true; }
            else{ $selected = false; }
            $data[] = array('id' => $item['id'],
                            'selected' => $selected,
                            'name' => $item['version']);
        }
    }         
    
    return $data;          
}
?>
