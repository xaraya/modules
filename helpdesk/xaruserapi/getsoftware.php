<?php
/**
  Gets items of the Software object
  @author Brian McGilligan
  @returns data used in a template
*/
function helpdesk_userapi_getsoftware($args)
{
    extract($args);
    
    $modid = xarModGetIDFromName(xarModGetName());
    $itemtype = 8;
    $items = xarModAPIFunc('dynamicdata', 'user', 'getitems', 
                          array('moduleid' => $modid,
                                'itemtype' => $itemtype));
    
    $data = array();
    if(!empty($select)){
        foreach($items as $item){
            $data[] = array('software_id'   => $item['id'],
                            'software_name' => $item['software'],
                            'swtype_id'     => $item['software_type']);
        }                         
    }else{                          
        foreach($items as $item){
            $id = $item['id'];
            $data[$id] = $item['software'];
        }                         
    }
    
    return $data;
}
?>
