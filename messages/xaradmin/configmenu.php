<?php
function messages_adminpriv_configmenu() 
{

    /*
     * Build the configuration submenu
     */
    $menu = array(
        array(
            'label' =>  'Config',
            'url'   =>  xarModURL(
                'messages',
                'admin',
                'config' )));


    $menu[] = array(
            'label' =>  'Messages',
            'url'   =>  xarModURL(
                'messages',
                'admin',
                'config'
                ,array( 'itemtype' => '1' )));


    return $menu;

}
?>