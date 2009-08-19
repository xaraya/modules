<?php
/**
 * Display the user menu
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
 * display the user menu hook
 * This is a standard function to provide a link in the "Your Account Page"
 *
 * @param $phase is the which part of the loop you are on
 *
 */
function dyn_example_user_usermenu($args)
{
    extract($args);
    // Security check  - if the user has read access to the menu, show a
    // link to display the details of the item
    if (!xarSecurityCheck('ViewDynExample',0)) return '';

    // First, lets find out where we are in our logic. If the phase
    // variable is set, we will load the correct page in the loop.
    if(!xarVarFetch('phase','notempty', $phase, 'menu', XARVAR_NOT_REQUIRED)) {return;}

    switch(strtolower($phase)) {
        case 'menu':
            $data['icon'] = xarTplGetImage('module-generic.png','base');
            $data['link'] = xarModURL('roles', 'user', 'account', array('moduleload' => 'dyn_example'));
            $data['label'] = xarML('Choose Settings');
            return (serialize($data));                                                                         
            break;

        case 'form':

            xarTplSetPageTitle(xarVarPrepForDisplay(xarML('My Settings')));

            // Load the DD master object class. This line will likely disappear in future versions
            sys::import('modules.dynamicdata.class.objects.master');
            // Get the object we'll be working with
            $data['object'] = DataObjectMaster::getObject(array('name' => 'usersettings_dyn_example'));
            $data['id'] = xarUserGetVar('id');
            $data['object']->getItem(array('itemid' => $data['id']));

            return serialize($data);
            break;

        case 'update':
            // This is not used. The update is done by the DD update function
            break;
    }

    // Finally, we need to send our variables to block layout for processing.  Since we are
    // using the data var for processing above, we need to do the same with the return.
    return $data;
}

?>
