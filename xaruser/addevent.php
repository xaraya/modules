<?php

/**
* File: $Id: addevent.php,v 1.3 2005/03/27 13:52:53 michelv01 Exp $
*
* Generates a form for adding an event.
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

function julian_user_addevent()
{
   //This prevents users from viewing something they are not suppose to.
   if (!xarSecurityCheck('Editjulian')) return;
   
   if (!xarVarFetch('cal_date','int::',$cal_date)) return;
   
     // Build description for the item we want the hooks (i.e. category) for.
    $item = array();
    $item['module'] = 'julian';
    $item['multiple'] = false;    // Doesn't function yet, requires change in  categories_admin_newhook
    //$item['itemtype'] = empty, because no item type is needed (we have on one type of object;
     //     module variable number_of_categories.itemtype should be made if we set itemtype)
     
     // Get the hooks for this item.
     //    xarModCallHooks parameters:
     //        hookObject (string) - what object are we working on
     //       hookAction (string) - what are we doing with the object?
     //        hookId (integer) - id of the object we are working on
     //        extraInfo (dictionary) - additional info on the current object
     //       callerModName (string) - name of the calling module (deprecated, specify in extraInfo instead)
     //        callerItemType (string) - the type of item (deprecated, specify in extraInfo instead)
    $hooks = xarModCallHooks('item', 'new', 0, $item);
     
    // Deal with no-hook scenario (the template then must get an empty hook-array)
     if (empty($hooks)) {
        $bl_data['hooks'] = array();
    } else {
        $bl_data['hooks'] = $hooks;
    }
     
   $bl_data['todays_month'] = date("n",strtotime($cal_date));
   $bl_data['todays_year'] = date("Y",strtotime($cal_date));
   $bl_data['todays_day'] = date("d",strtotime($cal_date));  
   //building share options
   $bl_data['share_options'] = xarModAPIFunc('julian','user','getuseroptions',array('uids'=>''));  
   $bl_data['cal_date']=$cal_date;
   return $bl_data;
}
?>
