<?php
/**
  Gets items of the Manufacturers object
  @author Brian McGilligan
  @returns data used in a template
*/
function helpdesk_userapi_getmanufacturers($args)
{
    extract($args);
    
    $modid = xarModGetIDFromName(xarModGetName());
    $itemtype = 6;
    $items = xarModAPIFunc('dynamicdata', 'user', 'getitems', 
                          array('moduleid' => $modid,
                                'itemtype' => $itemtype));
    
    $data = array();                      
    foreach($items as $item){
        ///echo var_dump($item);
        $id = $item['id'];
        $data[$id] = $item['manufacturer'];
    }                         
    
    return $data;
}
?>
