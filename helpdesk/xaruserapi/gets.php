<?php
/**
  Gets items of a DynamicData object
  @author Brian McGilligan
  @param $args['itemtype'] - Item type
  @returns list of items of the item type
*/
function helpdesk_userapi_gets($args)
{
    extract($args);
    
    $modid = xarModGetIDFromName('helpdesk');

    $info = array();
    $info['modid'] = $modid;
    $info['itemtype'] = $itemtype;
            
    $items = xarModAPIFunc('dynamicdata', 'user', 'getitems', $info);
    
    return $items;
}
?>
