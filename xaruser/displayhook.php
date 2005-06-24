<?php

/**
 * show date/time/schedule for an item - hook for ('item','display','GUI')
 */
function julian_user_displayhook($args)
{
    extract($args);
    
     // extra info as supplied by the hooking module.
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $extrainfo = array();
    }

     // Get the id of the object to display (the id as used in the hooking module).
    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'object ID', 'user', 'modifyhook', 'julian');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // When called via hooks, the module name may be empty, so we get it from the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

     // Convert module name into module id.
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)','module name', 'user', 'modifyhook', 'julian');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
     
    // Get item type.
     if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    $bl_data = array();
    
   // Load up database
   $dbconn =& xarDBGetConn();
   $xartable = xarDBGetTables();
   $event_linkage_table = $xartable['julian_events_linkage'];

    // Try to find the link for the current module, item type and item id.
   $query = "SELECT * FROM " .  $event_linkage_table . " WHERE (`hook_modid`=$modid AND `hook_itemtype`=$itemtype AND `hook_iid`=$objectid)";
   $result =& $dbconn->Execute($query);
   if (!empty($result)) {
        if (!$result->EOF) {
            $edit_obj = $result->FetchObject(false);
            
            // Start/end date (and time)
            $event_startdate = strtotime($edit_obj->dtstart);
            $event_enddate   = strtotime($edit_obj->recur_until);
            
            $bl_data['event_startdate'] = date("F j, Y",$event_startdate);
            $bl_data['event_starttime'] = date("g:i A",$event_startdate);
            $bl_data['event_enddate'] = strcmp($event_enddate,'')==0 ? '' : date("F j, Y",$event_enddate);
            
            // All day or not
            $bl_data['event_allday'] = ($edit_obj->isallday==1);

            // Event duration
            if (strcmp($edit_obj->duration,'')!=0) {
                list($bl_data['event_dur_hours'], $bl_data['event_dur_minutes']) = explode(':',$edit_obj->duration);
            }

            //Checking to see which repeating rule was used so the event_repeat can be set.
            if ($edit_obj->rrule==3 && $edit_obj->recur_count && $edit_obj->recur_interval && $edit_obj->recur_freq)
                $bl_data['event_repeat'] = 2;
            else if ($edit_obj->rrule && $edit_obj->recur_freq) 
               $bl_data['event_repeat'] = 1;
            else
                $bl_data['event_repeat'] = 0;

            //Depending on which recurrence rule was used, set the appropriate form fields.
            switch ($bl_data['event_repeat']) {
                case 1:
                    $bl_data['event_repeat_every_freq'] = $edit_obj->recur_freq;    // time unit (1=day, 2=week, 3=month, 4=year)
                    $bl_data['event_repeat_every_type']  = $edit_obj->rrule;            // every n time units
                    break;
                case 2:
                    $bl_data['event_repeat_on_day'] = $edit_obj->recur_count;        // day of the week (mon-sun)
                    $bl_data['event_repeat_on_num'] = $edit_obj->recur_interval;    // instance within month (1=1st, 2=2nd, ..., 5=last)
                    $bl_data['event_repeat_on_freq'] = $edit_obj->recur_freq;        // every n months
                    break;
            }
            
            $result->Close();
        }
        else {
            return 'There is no event hooked to this item.';
        }
    }
    else {
        return 'There is no event hooked to this item.';
    }

    return xarTplModule('julian','user','displayhook',$bl_data);
}

?>
