<?php
/**
 * Main user function
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Lists Module
 */
 
/*
 * Main user function
 *
 */
function lists_user_main($args) {
    //$types = xarModAPIfunc('lists', 'user', 'getlisttypes');
    //var_dump($types);

    //$lists = xarModAPIfunc('lists', 'user', 'getlists');
    //var_dump($lists);

    $items = xarModAPIfunc('lists', 'user', 'getlistitems', array('list_name'=>'list1', 'listkey'=>'name', 'itemkey'=>'code'));
    echo "<pre>";
    var_dump($items);
    echo "</pre>";

    return "hello";
}
?>