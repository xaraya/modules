<?php
function pmember_admin_new()
{
    // Security Check
    if(!xarSecurityCheck('AdminPMember')) return;
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Add Member Manually')));

    if (!xarVarFetch('phase','str:1:100',$phase,'request',XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;

    switch(strtolower($phase)) {
        case 'request':
        default:
            $data['authid'] = xarSecGenAuthKey();
            break;

        case 'update':
            if (!xarVarFetch('uname','str:1:100',$uname,'',XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
            if (!xarVarFetch('email','str:1:100',$email,'',XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;

            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;

            if ((empty($uname)) && (empty($email))) {
                $msg = xarML('You must enter either a username or password.');
                xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
                return;
            }

            // check for user and grab uid if exists
            $user = xarModAPIFunc('roles',
                                  'user',
                                  'get',
                                   array('uname' => $uname,
                                         'email' => $email));

            if (empty($user)) {
                $msg = xarML('That email address or username is not registered');
                xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
                return;
            }

            // Same logic as signing up on paypal.
            // First things first, let's get the uid out of the extrainfo soup
            $data['uid'] = $user['uid'];
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

            // And the difference is here to redirect to the view page.

            return xarResponseRedirect(xarModURL('pmember', 'admin', 'view'));

          break;
    }
    return $data;
}
?>