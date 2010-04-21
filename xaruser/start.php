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
 *  Start the checkout process -- user can create account or log into existing account
 */
function shop_user_start() 
{

    // Redirects at the start of the user functions are just a way to make sure someone isn't where they don't need to be
    if (xarUserIsLoggedIn()) {
        xarResponse::redirect(xarModURL('shop','user','viewcart'));
        return true;
    }
    $shop = xarSession::getVar('shop');
    if (empty($shop)) {
        xarResponse::redirect(xarModURL('shop','user','main'));
        return true;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    sys::import('modules.dynamicdata.class.properties.master');

    $rolesobject = DataObjectMaster::getObject(array('name' => 'roles_users'));
    $properties = $rolesobject->properties;
    $data['properties'] = $properties;

    $isvalid = $rolesobject->properties['email']->checkInput();
    $isvalid2 = $rolesobject->properties['password']->checkInput();

    if ($isvalid && $isvalid2) {

        if (!xarSecConfirmAuthKey()) {  // right time to do this??
            return xarTplModule('privileges','user','errors',array('layout' => 'bad_author'));
        }     

        // Create the role and the customer object and then log in
        $email = $rolesobject->properties['email']->getValue();
        $password = $rolesobject->properties['password']->getValue();
        
        $values['name'] = $email;
        $values['email'] = $email;
        $values['uname'] = $email;
        $values['password'] = $password;
        $values['state'] = 3;
        $rolesobject->setFieldValues($values,1);
        $uid = $rolesobject->createItem();

        $custobject = DataObjectMaster::getObject(array('name' => 'shop_customers'));
        $custobject->createItem(array('id' => $uid));

        $name = 'dd_' . $properties['password']->id;
        $vals = $properties['password']->fetchValue($name);
        $pass = $vals[1][0]; 

        $res = xarMod::APIFunc('authsystem','user','login',array('uname' => $email, 'pass' => $pass));

        xarResponse::redirect(xarModURL('shop','user','shippingaddress'));
        return true;

    } else {
        // We don't yet have a valid email or password for registration...
        return xarTplModule('shop','user','start', $data); 
    }


}

?>