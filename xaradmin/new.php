<?php
/**
 * Add a new item
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * Create a new item of the dyn_example object
 */
function dyn_example_admin_new()
{
    // See if the current user has the privilege to add an item. We cannot pass any extra arguments here
    if (!xarSecurityCheck('AddDynExample')) return;

    $data['object'] = DataObjectMaster::getObject(array('name' => 'dyn_example'));
    $data['tplmodule'] = 'foo';

    // Return the template variables defined in this function
    return $data;
}

?>