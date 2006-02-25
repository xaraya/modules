<?php
/**
 * Julian Calendar
 *
 * @package modules
 * @copyright (C) 2005 by Metrostat Technologies, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.metrostat.net
 *
 * @subpackage Julian Module
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 */
/**
 * Get data for a dropdown of users
 *
 * This function builds options for a select drop-down. These options contain
 * users from a certain group (user_id, name).
 *
 * @author  Julian Development Team, <michelv@xarayahosting.nl>
 * @author  initial template: Roger Raymond
 * @access  private
 * @param   uids
 * @return  $options
 * @deprecated 25 Feb 2006
 * @todo    MichelV. <#> Check this function and it functioning. Deprecate hopefully
 */
function julian_userapi_getuseroptions($args)
{
   extract($args);
   if (!xarVarFetch('uids','str',$uids,'')) return;
   $users=array();
   if(strcmp($uids,""))
      $users=split(",",$uids);

   //build an array of users who belong to the specified group
   $dbconn =& xarDBGetConn();
   //get db tables
   $xartable =& xarDBGetTables();
   //set roles table
   $roles_table = $xartable['roles'];
   //set rolemembers table
   $rolemembers_table=$xartable['rolemembers'];
   //do not include the current user in the options
   $current_user=xarUserGetVar('uid');
   $sql = "SELECT xrr.xar_uid FROM " . $rolemembers_table . " xrr LEFT JOIN " . $roles_table . " xro on(xrr.xar_uid=xro.xar_uid) WHERE xrr.xar_uid !='" . $current_user . "' AND xro.xar_type != '1' AND xro.xar_state='3' AND xrr.xar_parentid = '".xarModGetVar('julian','share_group')."' ORDER BY xro.xar_name";
   $result = $dbconn->Execute($sql);
   $options='';
   while(!$result->EOF)
   {
      $obj = $result->FetchObject(false);
      $options.= '<option value="' . $obj->xar_uid . '"';
      if(in_array($obj->xar_uid,$users))
         $options.= " SELECTED";
      $options.=">" . xarUserGetVar('name', $obj->xar_uid) . "</option>";
      $result->MoveNext();
   }
   return $options;
}
?>
