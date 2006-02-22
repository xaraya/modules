<?php
include_once 'modules/autodoc/xarclass/reflection.php';
function autodoc_user_main($args)
{
    $data = array();
    xarVarFetch('itemtype','int::',$data['itemtype'],1);
    xarVarFetch('itemid','str::',$data['itemid'],'0');
    xarVarFetch('scope','int::',$data['scope'],2);
    //xarVarFetch('modscope','id::',$data['modscope'],0);
    xarVarFetch('includeraw','checkbox',$data['includeraw']);

    // Get the requested items
    $data['items'] = array();
    $data['items'] = xarModApiFunc('autodoc','user','get',
                                   array('itemtype'   => $data['itemtype'],
                                         'scope' => $data['scope']));

    // Make sure we dont run out of our bounds
    if(isset($data['items'][$data['itemid']])) {
        $data['ad_itemname'] = $data['items'][$data['itemid']];
    } else {
        $data['ad_itemname'] = reset($data['items']);
    }

    // Make it suitable for the templates (dropdowns)
    $data['items'] = array_map('ad_tplArray',array_keys($data['items']),$data['items']);


    // For the one item, get its reflection info
    $rc =& ReflectionInfo::GetInfo($data['ad_itemname'],$data['itemtype']);
    $data['itemInfo'] = $rc->toArray();

    $data['scope_options'] = array(
                                   array('id' => 0, 'name' => xarML('All')),
                                   array('id' => 1, 'name' => xarML('Internal')),
                                   array('id' => 2, 'name' => xarML('User defined'))
                                   );
    
    if(isset($data['includeraw'])) $data['itemInfo']['includeraw'] = $data['includeraw'];

    // Hooks
    $data['hooks'] = xarModCallHooks('item','search',$data['itemid'],array());
    return $data;
}

// Helper function
function ad_tplArray($key,$value)
{
    return array('id' => $key,'name' => $value);
}


?>