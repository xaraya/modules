<?php
/**
 * create an entry for a module item - hook for ('item','create','GUI')
 * Optional $extrainfo['pmember'] from arguments, or 'pmember' from input
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns array
 * @return extrainfo array
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pmember_adminapi_createhook($args)
{
    extract($args);
    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'object ID', 'admin', 'createhook', 'pmember');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $extrainfo = array();
    }

    // When called via hooks, modname wil be empty, but we get it from the
    // extrainfo or the current module
    if (empty($modname)) {
        if (!empty($extrainfo['module'])) {
            $modname = $extrainfo['module'];
        } else {
            $modname = xarModGetName();
        }
    }

    // Right now, paypalipn is the only supported module
    // Need to return because of the user menu hook.
    if ($modname != 'paypalipn'){
      return $extrainfo;
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'module name', 'admin', 'createhook', 'pmember');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        mail( "mikeypotter@yahoo.com", "Error", $msg );
        return;
    }

    if (!isset($itemtype) || !is_numeric($itemtype)) {
         if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
             $itemtype = $extrainfo['itemtype'];
         } else {
             $itemtype = 0;
         }
    }
    // extrainfo comes to us in a serialize array.  First thing first, lets get it workable.
    $subscription = unserialize($extrainfo['var_dump']);
    // Debug
    
                $mail['var_dump_formatted'] = var_export($subscription, true);
                $email                      = xarModGetVar('mail', 'adminmail');
                $name                       = xarModGetVar('mail', 'adminname');
                $subject                    = xarML('Paypal IPN Successful');
                $message = xarML('extrainfo dump');
                $message .= "\n\n";
                $message .= $mail['var_dump_formatted'];

                if (!xarModAPIFunc('mail',
                        'admin',
                        'sendmail',
                        array('info' => $email,
                            'name' => $name,
                            'subject' => $subject,
                            'message' => $message))) return;
   

    // So here we are with a notification from somewhere (probably paypal)
    // that we have received credit on something.  Life is good so far.
    // First things first, let's get the uid out of the extrainfo soup
    $data['uid'] = $subscription['item_number'];
    // Next let's determine the time now
    $data['time'] = time();
    // And if this is a subscription, when does it expire?
    if (xarModGetVar('pmember', 'typeoffee') == 'fee'){
        // doesn't expire for 25 years
        $data['expire'] = time() + 788940000;
    } else {
        // figure out the time
        if (xarModGetVar('pmember', 'period') == 'D'){
            $number_of_days = xarModGetVar('pmember', 'time');
            $duration = $number_of_days * 86400;
        }
        elseif (xarModGetVar('pmember', 'period') == 'W'){
            $duration = 7 * 86400;
        }
        elseif (xarModGetVar('pmember', 'period') == 'M'){
            $duration = 30 * 86400;
        }
        elseif (xarModGetVar('pmember', 'period') == 'Y'){
            $duration = 365 * 86400;
        }
        $data['expire'] = time() + $duration;
    }
    // Our storage is now set.
    // We also need to extract the price and other items to compare if life is still good
    // First, the price is right
    //if (xarModGetVar('pmember', 'price') != $subscription['payment_gross'] && xarModGetVar('pmember', 'price') != $subscription['mc_gross'] ){
        //return $extrainfo;
    //}
    // Next are we sure this is a completed transaction?
    switch (strtolower($subscription['payment_status'])) {
        // Most of the options that PayPal send mean that we can just go on our merry way
        case 'denied': 
        case 'failed':
        case 'pending':
            return $extrainfo;
            break;
        // However, there are a few that we need to revert the member to the original members group
        case 'refunded':
        case 'reversed':
                // we need to revert them here to the default user group
                $userRole = xarModGetVar('roles', 'defaultgroup');
                 // Get the group id
                $defaultRole = xarModAPIFunc('roles', 'user', 'get', array('name'  => $userRole, 'type'   => 1));
		if (empty($defaultRole)) return $extrainfo;
                // Make the user a member of the users role
                if(!xarMakeRoleMemberByID($data['uid'], $defaultRole['uid'])) return $extrainfo;
                //return $extrainfo;

                // Then remove them from the current group
                $current = xarModGetVar('pmember', 'defaultgroup');
                 // Get the group id
                $oldRole = xarModAPIFunc('roles', 'user', 'get', array('name'  => $current, 'type'   => 1));

                $roles = new xarRoles();
                $role = $roles->getRole($oldRole['uid']);
                $member = $roles->getRole($data['uid']);
                $removed = $role->removeMember($member);
                if (!$removed) return $extrainfo;

            break;
    } 

    // So now we should just have the pings that are either Complete or Cancelled_Reversal
    // Life should be getting better.  We should be able to upgrade them to the new group.
    $userRole = xarModGetVar('pmember', 'defaultgroup');
     // Get the group id
    $defaultRole = xarModAPIFunc('roles', 'user', 'get', array('name'  => $userRole, 'type'   => 1));
    if (empty($defaultRole)) return $extrainfo;
    // Make the user a member of the users role
    xarMakeRoleMemberByID($data['uid'], $defaultRole['uid']);

    // Then remove them from the current group
    $current = xarModGetVar('roles', 'defaultgroup');
     // Get the group id
    $oldRole = xarModAPIFunc('roles', 'user', 'get', array('name'  => $current, 'type'   => 1));

    $roles = new xarRoles();
    $role = $roles->getRole($oldRole['uid']);
    $member = $roles->getRole($data['uid']);
    $removed = $role->removeMember($member);
    if (!$removed) return $extrainfo;

    // That's done now.  Let's go on an log the transaction and depart.
    if (!xarModAPIFunc('pmember', 'admin', 'create', array('uid' => $data['uid'], 'expire' => $data['expire'], 'time' => $data['time']))) return $extrainfo;
    // Get User Info
    $user = xarModAPIFunc('roles', 'user', 'get', array('uid' => $data['uid']));
    // Finally, send a thank you message
    //Get the common search and replace values
    $sitename = xarModGetVar('themes', 'SiteName');
    $siteadmin = xarModGetVar('mail', 'adminname');
    $adminmail = xarModGetVar('mail', 'adminmail');
    $siteurl = xarServerGetBaseURL();
    $search = array('/%%sitename%%/','/%%siteadmin%%/', '/%%adminmail%%/','/%%siteurl%%/', '/%%name%%/', '/%%username%%/', '/%%useremail%%/');
    $replace = array("$sitename", "$siteadmin", "$adminmail", "$siteurl", "$user[name]", "$user[uname]", "$user[email]");
    $message = xarModGetVar('pmember', 'message');
    $message = preg_replace($search,
                              $replace,
                              $message);

    $subject = xarML('Thank you for subscribing');

    // Send confirmation email
    if (!xarModAPIFunc('mail',
                       'admin',
                       'sendmail',
                       array('info' => $user['email'],
                             'name' => $user['name'],
                             'subject' => $subject,
                             'message' => $message))) return false;

    // Moving on, nothing left to see or do, I don't believe
    return $extrainfo;
}
?>
