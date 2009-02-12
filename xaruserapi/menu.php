<?php

function labaccounting_userapi_menu()
{
    $menu = array();
    
    $displaytitle = xarModGetVar('labaccounting', 'displaytitle');
    if(empty($displaytitle)) {
        $displaytitle = xarML('labAccounting');
    }
    
    $menu['displaytitle'] = $displaytitle;
    
    return $menu;
}

?>
