<?php
/**
 * remove subscriptions that are out of date and return to default group
 * 
 * @author John Cox
 * @access public 
 */
function pmember_schedulerapi_unsubscribe($args)
{
    // The user API function is called
    $links = xarModAPIFunc('pmember',
                           'user',
                           'getall');
    $now = time();
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];
        // Just need to purge out the old expired memberships on a scheduled basis
        // Might have a couple of extra hours depending on when it runs
        // But in the end, whats a few hours between friends?
        if ($now > $link['expires']) {
            xarModAPIFunc('pmember',
                          'admin',
                          'delete',
                           array('uid' => $link['uid']));

            // Send the user back to the default user group
            $userRole = xarModGetVar('roles', 'defaultgroup');
             // Get the group id
            $defaultRole = xarModAPIFunc('roles', 'user', 'get', array('name'  => $userRole, 'type'   => 1));
            if (empty($defaultRole)) return $extrainfo;
            // Make the user a member of the users role
            if(!xarMakeRoleMemberByID($link['uid'], $defaultRole['uid'])) return;

            // Then remove them from the current group
            $current = xarModGetVar('pmember', 'defaultgroup');
             // Get the group id
            $oldRole = xarModAPIFunc('roles', 'user', 'get', array('name'  => $current, 'type'   => 1));

            $roles = new xarRoles();
            $role = $roles->getRole($oldRole['uid']);
            $member = $roles->getRole($data['uid']);
            $removed = $role->removeMember($member);
            if (!$removed) return $extrainfo;

        }
    }
    return true;
}
?>