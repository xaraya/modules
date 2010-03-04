<?php
 /**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage shop Module
 * @link http://www.xaraya.com/index.php/release/eid/1031
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  Modifyconfig
 */
function shop_admin_modifyconfig()
{
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminShop')) return;

    // Check if this template has been submitted, or if we just got here
    if (!xarVarFetch('phase',        'str:1:100', $phase,       'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');
    // Get the object we'll be working with for shop-specific configuration
    $data['object'] = DataObjectMaster::getObject(array('name' => 'shop_module_settings'));
    // Get the appropriate item of the dataobject. Using itemid 0 (not passing an itemid parameter) is standard convention
    $data['object']->getItem(array('itemid' => 0));

    // Get the object we'll be working with for common configuration settings
    $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'shop'));
    // Decide which fields are configurable in this module
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, module_alias_name, enable_user_menu, user_menu_link');
    // Get the appropriate item of the dataobject. Using itemid 0 (not passing an itemid parameter) is standard convention
    $data['module_settings']->getItem();

    // Run the appropriate code depending on whether the template was submitted or not
    switch (strtolower($phase)) {
        case 'modify':
        default:
            break;

        case 'update':
          

            $isvalid = $data['module_settings']->checkInput();
            if (!$isvalid) {
                return xarTplModule('shop','admin','modifyconfig', $data);
            } else {
                $itemid = $data['module_settings']->updateItem();
            }
 
                if (!xarMod::guiFunc('dynamicdata','admin','update')) return;

            break;
    }

    // Return the template variables defined in this function
    return $data;
}

?>
