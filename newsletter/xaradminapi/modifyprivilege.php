<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/

/**
 * Create a privilege
 *
 * @private
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['type'] 'add' or 'remove' the privilege
 * @param $args['mask'] the mask
 * @param $args['rolename'] the role name
 */
function newsletter_adminapi_modifyprivilege($args)
{
    // Extract args
    extract($args);

    // Argument check
    $invalid = array();

    if (!isset($type) || !is_string($type))
        $invalid[] = 'type';

    if (!isset($mask) || !is_string($mask))
        $invalid[] = 'mask';

    if (!isset($rolename) || !is_string($rolename))
        $invalid[] = 'rolename';

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'modifyprivilege', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Create privileges
    switch($mask){
        case 'ReadNewsletter':
            $privilege = "NewsletterRead";
            $comment = "The publication privilege granting read access";
            $access = 'ACCESS_READ';
            break;
        case 'CommentNewsletter':
            $privilege = "NewsletterComment";
            $comment = "The publication privilege granting comment access";
            $access = 'ACCESS_COMMENT';
            break;
        case 'ModerateNewsletter':
            $privilege = "NewsletterModerate";
            $comment = "The publication privilege granting moderate access";
            $access = 'ACCESS_MODERATE';
            break;
        case 'EditNewsletter':
            $privilege = "NewsletterEdit";
            $comment = "The publication privilege granting edit access";
            $access = 'ACCESS_EDIT';
            break;
        case 'AddNewsletter':
            $privilege = "NewsletterAdd";
            $comment = "The publication privilege granting add access";
            $access = 'ACCESS_ADD';
            break;
        case 'DeleteNewsletter':
            $privilege = "NewsletterDelete";
            $comment = "The publication privilege granting delete access";
            $access = 'ACCESS_DELETE';
            break;
        case 'AdminNewsletter':
            $privilege = 'NewsletterAdmin';
            $comment = "The publication privilege granting admin access";
            $access = 'ACCESS_ADMIN';
            break;
        case 'OverviewNewsletter':
        default:
            $privilege = 'NewsletterOverview';
            $comment = "The publication privilege granting overview access";
            $access = 'ACCESS_OVERVIEW';
            break;
    }
    
    if ($type == 'add') {
        xarRegisterPrivilege($privilege,'All','newsletter','All','All',$access,$comment);

        xarAssignPrivilege($privilege,$rolename);
    } elseif ($type == 'remove') {
        $role = xarFindRole($rolename);
        if (!$role) return false;  // throw back

        $privs = new xarPrivileges();
        $priv = $privs->findPrivilege($privilege);
        if (!$priv) return false;  // throw back

        if (!$role->removePrivilege($priv)) 
            return false;
    }
    
    return true;
}

?>
