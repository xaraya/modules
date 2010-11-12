<?php
/**
 * Modify config
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Content Module
 * @link http://www.xaraya.com/index.php/release/1015.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * This is a standard function to modify and update the configuration parameters of the
 * module
 */
function messages_admin_modifyconfig()
{

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminMessages')) return;

	$data['groups'] = xarMod::apiFunc('roles', 'user', 'getallgroups');

    // Check if this template has been submitted, or if we just got here
    if (!xarVarFetch('phase',        'str:1:100', $phase,       'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return; 

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');
    // Get the object we'll be working with for content-specific configuration
    $object = DataObjectMaster::getObject(array('name' => 'messages_module_settings'));
    // Get the appropriate item of the dataobject. Using itemid 0 (not passing an itemid parameter) is standard convention
    $object->getItem(array('itemid' => 0));
	$data['object'] = $object;

    // Get the object we'll be working with for common configuration settings
    $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'messages'));
    // Decide which fields are configurable in this module
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, enable_short_urls');
    // Get the appropriate item of the dataobject. Using itemid 0 (not passing an itemid parameter) is standard convention
    $data['module_settings']->getItem();

    // Run the appropriate code depending on whether the template was submitted or not
    switch (strtolower($phase)) {
        case 'modify':
        default:
            break;

        case 'update':
            # --------------------------------------------------------
            #
            # Confirm the authorisation code
            #
            # This is done to make sure this was submitted by a human to this module and not called programatically
            # However in this case we need to disable ît because although the common configuration below is
            # handled in this module, the module-specific part at the end of the page is sent to be done by
            # the dynamicdata module, where the same check is done. Since both checks cannot simultaneously
            # be passed, (the act of checking resets the check) the one below is disabled in this example.
            #
            //if (!xarSecConfirmAuthKey()) return;

            # --------------------------------------------------------
            #
            # Updating the common configuration
            #
            # We do this before anything else, because the checkInput error checking might
            # decide something is not right and redisplay the form. In such a case we don't want to
            # already have saved data. The module-specific code active at the bottom of the page does no
            # such error checking. This is not really a problem because you can't really get input errors
            # when you're dealing with checkboxes (they're either checked or they aren't)
            #

            $isvalid = $data['module_settings']->checkInput();
            if (!$isvalid) {
                return xarTplModule('messages','admin','modifyconfig', $data);
            } else {
                $itemid = $data['module_settings']->updateItem();
            }

			//sys::import('modules.dynamicdata.class.properties.master');
						

			foreach ($data['groups'] as $key => $value) {
				//$property = DataPropertyMaster::getProperty(array('name' => 'roleid_'.$key)); 
				//$property->checkInput('roleid_'.$key); 
				$the_key = $value['id'];
				if (!xarVarFetch('roleid_'.$the_key,  'array',    $roleid_{$the_key}, 0, XARVAR_NOT_REQUIRED)) return; 
				xarModItemVars::set('messages', "allowedsendmessages", serialize($roleid_{$the_key}),$the_key);
			}

            # --------------------------------------------------------
            #
            # Updating the content configuration without using DD
            #
            # In this case we get each value from the template and set the appropriate modvar
            # Note that in this case we are setting modvars, whereas in the other two ways below we are actually
            # setting moditemvars with the itemid = 1
            # This can work because in the absence of a moditemvar the corresponding modvar is returned
            # What we cannot do however is mix these methods, because once we have a moditemvar defined, we can
            # no longer default back to the modvar (unless called specifically as below).
            #
            /*
                // Get parameters from whatever input we need.  All arguments to this
                // function should be obtained from xarVarFetch(), getting them
                // from other places such as the environment is not allowed, as that makes
                // assumptions that will not hold in future versions of Xaraya
                if (!xarVarFetch('bold', 'checkbox', $bold, false, XARVAR_NOT_REQUIRED)) return;

                // Confirm authorisation code.  This checks that the form had a valid
                // authorisation code attached to it.  If it did not then the function will
                // proceed no further as it is possible that this is an attempt at sending
                // in false data to the system
                if (!xarSecConfirmAuthKey()) return;

                xarModVars::set('content', 'bold', $bold);
            */

            # --------------------------------------------------------
            #
            # Updating the content configuration with DD class calls
            #
            # This is the same as the examples of creating, modifying or deleting an item
            # Note that, as in those examples, this code could be placed in the modifyconfig.php file
            # and this file dispensed with.
            #
            
                // Load the DD master object class. This line will likely disappear in future versions
                sys::import('modules.dynamicdata.class.objects.master');
                // Get the object we'll be working with
                $object = DataObjectMaster::getObject(array('name' => 'messages_module_settings'));
                // Get the data from the form
                $isvalid = $object->checkInput();
                // Update the item with itemid = 0
				 
				
                $item = $object->updateItem(array('itemid' => 0));

				xarResponse::redirect(xarModURL('messages','admin','modifyconfig'));

            # --------------------------------------------------------
            #
            # Updating the content configuration with a DD API call
            #
            # This is a special case using the dynamicdata_admin_update function.
            # It depends on finding all the relevant information on the template we are submitting, i.e.
            # - objectid of the content_module_settings object
            # - itemid (1 in this case)
            # - returnurl, telling us where to jump to after update
            #
            # This needs to be the last thing happening on this page because it redirects. Code below
            # this point will not execute

                if (!xarMod::guiFunc('messages','admin','update')) return;

            break;
    }
 
    // Return the template variables defined in this function
    return $data;
}

?>
