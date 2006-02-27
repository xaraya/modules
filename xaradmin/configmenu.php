<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
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