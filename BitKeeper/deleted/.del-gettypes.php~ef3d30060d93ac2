<?php
/**
  Gets items of the Type object
  @author Brian McGilligan
  @returns data used in a template
*/
function helpdesk_userapi_gettypes($args)
{
    extract($args);
    
    $modid = xarModGetIDFromName('helpdesk');
    $itemtype = 5;
    $items = xarModAPIFunc('dynamicdata', 'user', 'getitems', 
                          array('moduleid' => $modid,
                                'itemtype' => $itemtype));
    
    $data = array();                      
    foreach($items as $item){
        ///echo var_dump($item);
        $id = $item['id'];
        $data[$id] = $item['type'];
    }                         
    
    return $data;
}
?>
