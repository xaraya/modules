<?php
/**
 * Update configuration
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
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 * @return bool true on success of update
 */
function dyn_example_admin_updateconfig()
{

# --------------------------------------------------------
#
# Updating the configuration without using DD
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
    if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;

    xarModVars::set('dyn_example', 'bold', $bold);
    xarModVars::set('dyn_example', 'itemsperpage', $itemsperpage);
    xarModVars::set('dyn_example', 'SupportShortURLs', $shorturls);
*/

# --------------------------------------------------------
#
# Updating the configuration with DD class calls
#
# This is the same as the examples of creating, modifying or deleting an item 
# Note that, as in those examples, this code could be placed in the modifyconfig.php file
# and this file dispensed with.
#
/* 
    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');
    // Get the object we'll be working with
    $data['object'] = DataObjectMaster::getObject(array('name' => 'modulesettings_dyn_example'));
    // Get the data from the form
    $isvalid = $data['object']->checkInput();
    // Update the item with itemid = 1
    $item = $data['object']->updateItem(array('itemid' => 1));
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponse::redirect(xarModURL('dyn_example', 'admin', 'modifyconfig'));
    return true;
*/

# --------------------------------------------------------
#
# Updating the configuration with a DD API call
#
# This is a special case using the dynamicdata_admin_update function.
# It depends on finding all the relevant information on the template we are submitting, i.e.
# - objectid of the modulesettings_dyn_example object
# - itemid (1 in this case)
# - returnurl, telling us where to jump to after update
#
    if (!xarModFunc('dynamicdata','admin','update')) return;

}

?>
