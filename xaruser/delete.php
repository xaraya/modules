<?php

/**
* File: $Id: delete.php,v 1.3 2005/03/27 13:52:53 michelv01 Exp $
*
* Deletes an event.
*
* @package Xaraya eXtensible Management System
* @copyright (C) 2004 by Metrostat Technologies, Inc.
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.metrostat.net
*
* @subpackage julian
* initial template: Roger Raymond
* @author Jodie Razdrh/John Kevlin/David St.Clair
*/

  function julian_user_delete()
  {  
   //This prevents users from viewing something they are not suppose to.
   if (!xarSecurityCheck('Editjulian')) return;
   if (!xarVarFetch('cal_date','isset',$cal_date)) return;
   if (!xarVarFetch('id','isset',$id)) return;
   
   // establish db connection  
   $dbconn =& xarDBGetConn();
   //get db tables
   $xartable = xarDBGetTables();
   //set events table
   $event_table = $xartable['julian_events'];
   
   //delete the event
   $query = "DELETE FROM " . $event_table . " WHERE `event_id` = '".$id."'";
   $result = $dbconn->Execute($query);
   // Tell hooked modules that the event was deleted.
   $item = array();
   $item['module'] = 'julian';
   $hooks = xarModCallHooks('item', 'delete', $id, $item);
   //redirect the user to the month view
   xarResponseRedirect(xarModURL('julian', 'user', 'month',array('cal_date'=>$cal_date)));
  }
   
?>
