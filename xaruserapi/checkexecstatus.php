<?php
/**
 * Check the executive status
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author jojodee
 */

/**
 * Check the executive status of current user
 * Alternatively check status of user role UID passed in as argument
 *
 * @author jojodee
 */
function legis_userapi_checkexecstatus($args)
{
    extract($args);
    /* Check to see if the user is allowed to set their hall
       Assumes that all legis moderators are children of a nominated group of moderators
    */
    $modgroup=(int)xarModGetVar('legis','moderatorgroup');
    $modgroupallow=xarModGetVar('legis','allowchangehall');

    if (xarUserIsLoggedIn() && !isset($uidcheck)) {
       $thisrole=xarUserGetVar('uid');
       $childrole = new xarRoles();
       $thisuser= $childrole->getRole($thisrole);

       $modrole=new xarRoles();
       $moderator=$modrole->getRole($modgroup);
            if ($thisuser->isAncestor($moderator)) {
               $isexec=true;
            } else {
               $isexec=false;
            }
    } elseif (isset($uidcheck) && is_numeric($uidcheck) && $uidcheck >0 && xarSecurityCheck('DeleteLegis',1)) {
        //$uid passed in
         $childroles = new xarRoles();
         $thisuser = $childroles->getRole($uidcheck);
         $modrole=new xarRoles();
         $moderator=$modrole->getRole($modgroup);
            if ($thisuser->isAncestor($moderator)) {
               $isexec=true;
            } else {
               $isexec=false;
            }
    }else {
         $isexec=false;
    }

    return $isexec;
}
?>