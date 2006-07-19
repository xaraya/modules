<?php
/**
 * Main user function
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Lists Module
 * @link http://xaraya.com/index.php/release/46.html
 * @author Jason Judge
 */

/**
 * Main user function
 *
 * This function gets the present list items and dumps them.
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