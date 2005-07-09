<?php
/**
  Get an item of an object

  @author Brian McGilligan
  @param $args['object'] - The type of object to query
  @param $args['itemid'] - The items id
  @param $args['field']  - The desired field to return (optional)
  @returns
*/
function helpdesk_userapi_get($args)
{
    extract($args);

    if (!isset($object)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'object', 'userapi', 'get', 'helpdesk');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }
               
    if (empty($itemid)) { return xarML('Undefined'); }
    
    $modid = xarModGetIDFromName('helpdesk');
 
    // Do what we can to find the name of the field we want
    if(empty($field) && is_string($object)){
        $name = $object;
    }elseif(!empty($field) && is_string($field)){
        $name = $field;
    }    
       
    // Gets the item types of the objects
    switch($object){            
        case 'priority':
            $itemtype = 2;
            $name = null;
            break;
            
        case 'status':
            $itemtype = 3;
            break;
            
        case 'source':
            $itemtype = 4;
            break;
    }
       
    $item = xarModAPIFunc('dynamicdata', 'user', 'getitem', 
                          array('moduleid' => $modid,
                                'itemtype' => $itemtype,
                                'itemid'   => $itemid
                               )
                         );

    // if there is no name then just return the whole object or item
    if(!empty($name)){
        return $item[$name];
    }else{
        return $item;
    }
}
?>
