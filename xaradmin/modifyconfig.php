<?php
 /**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage amazonfps
 * @link http://xaraya.com/index.php/release/eid/1169
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  Modifyconfig
 */
function amazonfps_admin_modifyconfig() {
 
    if (!xarSecurityCheck('AdminAmazonFPS')) return;

    if (!xarVarFetch('phase',        'str:1:100', $phase,       'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;

    sys::import('modules.dynamicdata.class.objects.master');
   
    $data['object'] = DataObjectMaster::getObject(array('name' => 'amazonfps_module_settings'));
    $data['object']->getItem(array('itemid' => 0));

    // Get the object we'll be working with for common configuration settings
    $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'amazonfps'));
    // Decide which fields are configurable in this module
    $data['module_settings']->setFieldList('items_per_page');
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
                return xarTplModule('amazonfps','admin','modifyconfig', $data);
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
