<?php
/**
 * Example user settings
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
function dyn_example_user_settings()
{
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('My Settings')));

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');
    // Get the object we'll be working with
    $data['object'] = DataObjectMaster::getObject(array('name' => 'usersettings_dyn_example'));
    $data['id'] = xarUserGetVar('id');
    $data['object']->getItem(array('itemid' => $data['id']));

    return $data;
}

?>