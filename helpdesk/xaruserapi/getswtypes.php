<?php
/**
  Gets items of the Software Type object
  @author Brian McGilligan
  @returns data used in a template
*/
function helpdesk_userapi_getswtypes($args)
{
    extract($args);
    
    $modid = xarModGetIDFromName('helpdesk');
    $itemtype = 7;
    $items = xarModAPIFunc('dynamicdata', 'user', 'getitems', 
                          array('moduleid' => $modid,
                                'itemtype' => $itemtype));
    
    $data = array();                      
    foreach($items as $item){
        ///echo var_dump($item);
        $id = $item['id'];
        $data[$id] = $item['swtype'];
    }                         
    
    return $data;
}
?>
