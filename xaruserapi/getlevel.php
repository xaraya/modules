<?php
/**
  Gets items of a DynamicData object
  @author Brian McGilligan
  @param $args['itemtype'] - Item type
  @returns list of items of the item type
*/
function courses_userapi_getlevel($args)
{
    extract($args);
    
    $modid = xarModGetIDFromName('courses');

    $info = array();
    $info['modid'] = $modid;
    $info['itemtype'] = 3;
    $info['itemid'] = $level;
    $info['name'] = 'level';
    $item = xarModAPIFunc('dynamicdata', 'user', 'getfield', $info);
    
    return $item;
}
?>
